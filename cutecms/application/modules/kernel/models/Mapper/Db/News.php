<?php

class Model_Mapper_Db_News extends Model_Mapper_Db_Abstract
{

	protected $_defaultInjections = array(
		'Model_Db_Table_Interface' => 'Model_Db_Table_News',
		'Model_Object_Interface' => 'Model_Object_News',
        'Model_Collection_Interface' => 'Model_Collection_News',

        'Model_Db_Table_News_Description' => 'Model_Db_Table_NewsDescription',
        'Model_Mapper_Db_Plugin_Description',
        'Model_Mapper_Db_Plugin_Sorting',
        'Model_Mapper_Db_Plugin_Resource',
        'Model_Db_Table_Resources',
        'Model_Mapper_Db_User',
	
        'Model_Mapper_Db_Plugin_Multisite' => 'Model_Mapper_Db_Plugin_Multisite_ManyToMany',
        'Model_Mapper_Db_Site',
        'Model_Db_Table_SiteRef' => 'Model_Db_Table_NewsSiteRef',                        
	);



    public function init()
    {
        $this->addPlugin(
            'Description',
            $this ->getInjector()
                  ->getObject(
                    'Model_Mapper_Db_Plugin_Description',
                    array(
                        'mapper' => $this,
                        'table' => $this->getInjector()->getObject('Model_Db_Table_News_Description'),
                        'refColumn' => 'news_id',
                        'descFields' => array(
                            'title', 'title2', 'announce', 'full',
                            'html_title', 'meta_keywords', 'meta_description',
                        ),
                    )
                  )
        )
        ->addPlugin('Resource',$this->getInjector()->getObject('Model_Mapper_Db_Plugin_Resource', array('rc_id'), 2))
        ;
        $this->addPlugin('Multisite', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Multisite', array(
            'siteMapper' => $this->getInjector()->getObject('Model_Mapper_Db_Site'),
            'refTable' => $this->getInjector()->getObject('Model_Db_Table_SiteRef'),
            'refEntityColumn' => 'news_id',
        )));                                    
    }



    /**
     * addons for complex select
     * @param Zend_Db_Select
     * @return Zend_Db_Select
     */
    protected function _onFetchComplex(Zend_Db_Select $select)
    {
        $lang = Model_Service::factory('language')->getCurrent();
        $select = $this -> getMapper('User')
                        -> joinSubQuery(
                                $select,
                                'adder',
                                'news_adder_id = adder.user_id',
                                array(
                                    'news_adder_login' => 'adder.user_login',
                                    'news_adder_name' => 'adder.user_name',
                                )
                           );
        $select = $this -> getMapper('User')
                        -> joinSubQuery(
                                $select,
                                'changer',
                                'news_changer_id = changer.user_id',
                                array(
                                    'news_changer_login' => 'changer.user_login',
                                    'news_changer_name' => 'changer.user_name',
                                )
                           );
        $select -> joinLeft(
                        array('ntopic'=>'news_topic_description'),
                        'news_ntopic_id = ntopic_desc_ntopic_id '
                            . ' AND ntopic_desc_language_id = '.$lang->id,
                        array(
                            'news_ntopic_name' => 'ntopic_desc_name',
                            'news_ntopic_brief' => 'ntopic_desc_brief',
                        )
                   )
                -> joinLeft(
                        array(
                            'ntcount' => $this->getTable()->select()
                                                          ->from('news', array('ntopic_id'=>'news_ntopic_id', 'ntopic_news_count'=>'COUNT(news_id)'))
                                                          ->group('news_ntopic_id')
                        ),
                        'ntcount.ntopic_id = news_ntopic_id',
                        array('news_ntopic_news_count' => 'ntopic_news_count',)
                   )
                -> joinLeft(
                        array('ntopic2'=>'news_topic'),
                        'ntopic2.ntopic_id = news_ntopic_id',
                        array('ntopic_seo_id' => 'ntopic2.ntopic_seo_id')
                      )
                   ;
        $userService = Model_Service::factory('user');
        if ($userService->isAuthorized()) {
            $select->joinLeft(array('uns'=>'user_news_subscription'),
                              'uns_ntopic_id = news_ntopic_id AND uns_user_id = '.$userService->getCurrent()->id,
                              array('news_ntopic_subscribed'=>'uns_id'));
        }

        return $select;
    }

    /**
     * addon actions when building complex object
     * @param Model_Object_Interface $object
     * @param array $values
     * @return Model_Object_Interface
     */
    protected function _onBuildComplexObject(Model_Object_Interface $object, array $values = NULL, $addedPrefix = TRUE)
    {
        /*
        $prefixAdder = 'news_adder';
        if ($addedPrefix) {
            $prefixAdder =  $addedPrefix . '_' . $prefixAdder;
        }
        $prefixChanger = 'news_changer';
        if ($addedPrefix) {
            $prefixChanger =  $addedPrefix . '_' . $prefixChanger;
        }
        $object->Adder = $this->getMapper('User')->makeSimpleObject($values, $prefixAdder);
        $object->Changer = $this->getMapper('User')->makeSimpleObject($values, $prefixChanger);
        */
        return $object;
    }






	/**
	 * @param string $seoId
	 * @return Model_Object_Interface
	 */
	public function fetchOneActiveBySeoId($seoId)
	{
		if (empty($seoId)) {
			throw new Model_Mapper_Db_Exception('id should be set');
		}
        $rows = $this->fetchComplex(
                            array(
                                'news_seo_id = ?' => $seoId,
                                'news_status > ?' => new Zend_Db_Expr(0),
                            )
                       );
		if ( ! $rows->count()) {
			throw new Model_Mapper_Db_Exception('active table row with seo_id="'.$seoId.'" not found!');
		}
		else {
			$object = $rows->current();
		}
		return $object;
	}

    /**
     * make paginator for complex fetching
     * @param mixed array|string
     * @param int
     * @param int
     */
    public function paginatorFetchComplex($where, $rowsPerPage, $page)
    {
        $query = $this->fetchComplex($where, FALSE)->order('news_date_publish DESC');

        return $this->paginator($query,  $rowsPerPage, $page, Model_Object_Interface::STYLE_COMPLEX);
    }

    /**
     * complex fetch of last active news
     * @param mixed string|array
     * @param int
     * @return Model_Collection_Interface
     */
    public function fetchLatestActive($where, $limit = NULL, $fetch = TRUE)
    {
        $select = $this  ->fetchComplex($where, FALSE)
                         ->where('news_status > 0')
                         ->order(array('ntopic_desc_name ASC', 'news_date_publish DESC'))
                         ;
        if ($limit !== NULL) {
            $select->limit($limit);
        }
        if ($fetch === TRUE) {
            $news = $this->makeComplexCollection($select->query()->fetchAll());
        }
        else {
            $news = $select;
        }
        return $news;
    }
    
    public function fetchLatestActiveByDate($limit = NULL)
    {
        $select = $this  ->fetchComplex(NULL, FALSE)
                         ->where('news_status > 0')
                         ->order(array('news_date_publish DESC'))
                         ;
        if ($limit !== NULL) {
            $select->limit($limit);
        }
        $news = $this->makeComplexCollection($select->query()->fetchAll());
        return $news;
    }

    

    /**
     * make paginator for complex fetching
     * @param mixed array|string
     * @param int
     * @param int
     */
    public function paginatorFetchLatestActive($rowsPerPage, $page, $topicId = NULL)
    {
        if ($topicId==0) {
            /*$where = array('isnull(news_ntopic_id)');*/
            $where = NULL;
        }
        else if ($topicId > 0) {
            $where = array('news_ntopic_id = ?' => $topicId);
        }
        else {
            $where = NULL;
        }
        /*$query = $this->fetchLatestActiveByTopicAndDate($where, NULL, FALSE);*/
        $query = $this->fetchLatestActive($where, NULL, FALSE);
        return $this->paginator($query,  $rowsPerPage, $page, Model_Object_Interface::STYLE_COMPLEX);
    }


    /**
     * @param Model_Object_Interface
     * @param array
     * @return Model_Object_Interface
     */
    protected function _postSaveComplex(Model_Object_Interface $obj, array $values)
    {
        App_Event::factory('Model_Object_News__trigger__onAfterSaveComplex', array($obj, $values))->dispatch();
        return $obj;
    }
    
    public function fetchComplexByTopics(array $topics, $fetch = TRUE)
    {
        if (empty($topics)) {
            $where = NULL;
        }
        else {
            $where = array('news_ntopic_id IN (?)' => $topics);
        }
        $result = $this->fetchComplex($where, $fetch);
        return $result;
    }

    public function fetchComplexByTopicSeoId($topic)
    {
        $select = $this->getTable()
                              ->select()->setIntegrityCheck(FALSE)
                              ->from(array('news'))
                              ->joinLeft(
                                    array('news_description'),
                                    'news_desc_language_id = '.$this->getPlugin('Description')->getCurrentLanguage()->id
                                        . ' AND news_desc_news_id = news_id',
                                    array('news_desc_title', 'news_desc_announce')
                                )
                              -> joinLeft(
                                    array('ntopic'=>'news_topic'),
                                    'ntopic.ntopic_id = news_ntopic_id',
                                    array('ntopic_seo_id' => 'ntopic.ntopic_seo_id')
                                )
                              -> joinLeft(
                                    array('rc'=>'resource'),
                                    'rc.rc_id = news_rc_id',
                                    array('news_rc_id_preview2' => 'rc.rc_preview2')
                                )
                              ->where('ntopic_seo_id = ?', $topic)
                              ->where('news_status > 0')
                              ->order(array('news_date_publish DESC'))
                              ;
        $collection = $this->makeComplexCollection($select->query()->fetchAll());
        return $collection;
    }

    public function fetchComplexByNewsSeoIdFull($seo_id)
    {
        $select = $this->getTable()
                              ->select()->setIntegrityCheck(FALSE)
                              ->from(array('news'))
                              ->joinLeft(
                                    array('news_description'),
                                    'news_desc_language_id = '.$this->getPlugin('Description')->getCurrentLanguage()->id
                                        . ' AND news_desc_news_id = news_id',
                                    array('news_desc_title', 'news_desc_full', 'news_desc_title2')
                                )
                              ->where('news_seo_id = ?', $seo_id);

        if( $row = $this->getTable()->fetchRow($select) ) {
            return $this->makeComplexObject($row->toArray());
        }
        
        return $row;
    }

}
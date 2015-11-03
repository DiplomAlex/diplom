<?php

class Model_Mapper_Db_Page extends Model_Mapper_Db_Abstract
{

	protected $_defaultInjections = array(
		'Model_Db_Table_Interface' => 'Model_Db_Table_Pages',
		'Model_Object_Interface' => 'Model_Object_Page',
        'Model_Collection_Interface' => 'Model_Collection_Page',

        'Model_Db_Table_Page_Description' => 'Model_Db_Table_PagesDescription',
        'Model_Mapper_Db_Plugin_Description',
        'Model_Mapper_Db_Plugin_Sorting',
        'Model_Mapper_Db_User',
	    'Model_Mapper_Db_Plugin_Resource',
	    'Model_Db_Table_Resources',
        'Model_Mapper_Db_Plugin_Multisite' => 'Model_Mapper_Db_Plugin_Multisite_ManyToMany',
        'Model_Mapper_Db_Site',
        'Model_Db_Table_SiteRef' => 'Model_Db_Table_PageSiteRef',	
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
                        'table' => $this->getInjector()->getObject('Model_Db_Table_Page_Description'),
                        'refColumn' => 'page_id',
                        'descFields' => array(
                            'title', 'brief', 'full',
                            'html_title', 'meta_keywords', 'meta_description', 'video', 'banner'
                        ),
                    )
                  )
        )
        ->addPlugin('Sorting', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Sorting'))
        ->addPlugin('Resource',$this->getInjector()->getObject(
            'Model_Mapper_Db_Plugin_Resource', 
            array('rc_id'),
            Zend_Registry::get('config')->images->previewMaxCount 
        ))        
        ->addPlugin('Multisite', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Multisite', array(
            'siteMapper' => $this->getInjector()->getObject('Model_Mapper_Db_Site'),
            'refTable' => $this->getInjector()->getObject('Model_Db_Table_SiteRef'),
            'refEntityColumn' => 'page_id',
        )))        
        ;
    }


    /**
     * addons for complex select
     * @param Zend_Db_Select
     * @return Zend_Db_Select
     */
    protected function _onFetchComplex(Zend_Db_Select $select)
    {

        $select = $this -> getMapper('User')
                        -> joinSubQuery(
                                $select,
                                'adder',
                                'page_adder_id = adder.user_id',
                                array(
                                    'page_adder_login' => 'adder.user_login',
                                    'page_adder_name' => 'adder.user_name',
                                )
                           );
        $select = $this -> getMapper('User')
                        -> joinSubQuery(
                                $select,
                                'changer',
                                'page_changer_id = changer.user_id',
                                array(
                                    'page_changer_login' => 'changer.user_login',
                                    'page_changer_name' => 'changer.user_name',
                                )
                           );

        return $select;
    }


	/**
	 * @param string $seoId
	 * @return Model_Object_Interface
	 */
	public function fetchOneBySeoId($seoId)
	{
		if (empty($seoId)) {
			throw new Model_Mapper_Db_Exception('id should be set');
		}
		$coll = $this->fetchComplex(array('page_seo_id = ?' => $seoId));
		if ( ! $coll->count()) {
			throw new Model_Mapper_Db_Exception('table row with seo_id="'.$seoId.'" not found!');
		}
		else {
			$object = $coll->current();
		}
		return $object;
	}

    /**
     * @param string $code
     * @return Model_Object_Interface
     */
    public function fetchOneByCode($code)
    {
        if (empty($code)) {
            throw new Model_Mapper_Db_Exception('code should be set');
        }
        if ( ! $rows = $this->fetchComplex(array('page_code = ?' => $code))) {
            throw new Model_Mapper_Db_Exception('page with code="'.$code.'" not found!');
        }
        else {
            $object = $rows->current();
        }
        return $object;
    }
	
	
    public function fetchComplexByFlag($flagNum, $fetch = TRUE)
    {
        $table = $this->getTable();
        $prefix = $table->getColumnPrefix().$table->getPrefixSeparator();
        $where = array($prefix.'flag'.$flagNum.' > 0');
        /*var_dump($where);exit;*/
        $result = $this->fetchComplex($where, $fetch);
        return $result;
    }

    public function fetchByText($text = NULL)
    {
        $select = $this->fetchComplex(array(), FALSE)
                    ->where('page_status = ?', 1)
                    ->where('page_desc_title LIKE ? OR page_desc_full LIKE ?', '%'.$text.'%');
                    
        $result = $this->makeComplexCollection($select->query()->fetchAll());
        return $result;
    }
}
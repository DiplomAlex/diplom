<?php

class Model_Mapper_Db_Faq extends Model_Mapper_Db_Abstract
{

	protected $_defaultInjections = array(
		'Model_Db_Table_Interface' => 'Model_Db_Table_Faqs',
		'Model_Object_Interface' => 'Model_Object_Faq',
        'Model_Collection_Interface' => 'Model_Collection_Faq',

        'Model_Db_Table_Description' => 'Model_Db_Table_FaqsDescription',
        'Model_Mapper_Db_Plugin_Description',
        'Model_Mapper_Db_Plugin_Sorting',
        'Model_Mapper_Db_User',
        'Model_Mapper_Db_Plugin_Multisite' => 'Model_Mapper_Db_Plugin_Multisite_ManyToMany',
        'Model_Mapper_Db_Site',
        'Model_Db_Table_SiteRef' => 'Model_Db_Table_FaqSiteRef', 	
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
                        'table' => $this->getInjector()->getObject('Model_Db_Table_Description'),
                        'refColumn' => 'faq_id',
                        'descFields' => array(
                            'quest', 'brief', 'full',
                            'html_title', 'meta_keywords', 'meta_description',
                        ),
                    )
                  )
        )
        ->addPlugin('Sorting', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Sorting'))
        ;
        $this->addPlugin('Multisite', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Multisite', array(
            'siteMapper' => $this->getInjector()->getObject('Model_Mapper_Db_Site'),
            'refTable' => $this->getInjector()->getObject('Model_Db_Table_SiteRef'),
            'refEntityColumn' => 'faq_id',
        )));                    
        
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
                                'faq_adder_id = adder.user_id',
                                array(
                                    'faq_adder_login' => 'adder.user_login',
                                    'faq_adder_name' => 'adder.user_name',
                                )
                           );
        $select = $this -> getMapper('User')
                        -> joinSubQuery(
                                $select,
                                'changer',
                                'faq_changer_id = changer.user_id',
                                array(
                                    'faq_changer_login' => 'changer.user_login',
                                    'faq_changer_name' => 'changer.user_name',
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
		if ( ! $rows = $this->fetchComplex(array('faq_seo_id = ?' => $seoId))) {
			throw new Model_Mapper_Db_Exception('table row with seo_id="'.$seoId.'" not found!');
		}
		else {
			$object = $rows->current();
		}
		return $object;
	}


    public function fetchAllActive($limit = NULL, $fetch = TRUE)
    {
        $select = $this->fetchComplex(NULL, FALSE)
                       ->where('faq_status > 0');
        if ($limit !== NULL) {
            $select->limit($limit);
        }
        if ($fetch === TRUE) {
            $result = $this->makeComplexCollection($select->query()->fetchAll());
        }
        else {
            $result = $select;
        }
        return $result;
    }

    public function paginatorFetchAllActive($rowsPerPage, $page)
    {
        $query = $this->fetchAllActive(NULL, FALSE);
        return $this->paginator($query,  $rowsPerPage, $page, Model_Object_Interface::STYLE_COMPLEX);
    }


}
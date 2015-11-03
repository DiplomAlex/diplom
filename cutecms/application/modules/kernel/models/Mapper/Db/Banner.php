<?php

class Model_Mapper_Db_Banner extends Model_Mapper_Db_Abstract
{

	protected $_defaultInjections = array(
		'Model_Db_Table_Interface' => 'Model_Db_Table_Banners',
		'Model_Object_Interface' => 'Model_Object_Banner',
        'Model_Collection_Interface' => 'Model_Collection_Banner',
        'Model_Mapper_Db_Plugin_Sorting',
        'Model_Mapper_Db_Plugin_Description',
        'Model_Mapper_Db_Plugin_Resource',
        'Model_Db_Table_BannersDescription',
        'Model_Db_Table_Resources',
        'Model_Mapper_Db_Plugin_Multisite' => 'Model_Mapper_Db_Plugin_Multisite_ManyToMany',
        'Model_Mapper_Db_Site',
        'Model_Db_Table_SiteRef' => 'Model_Db_Table_BannerSiteRef',                    	
	);



    public function init()
    {
        $this
            ->addPlugin('Sorting', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Sorting'))
            ->addPlugin(
                'Description',
                $this ->getInjector()
                      ->getObject(
                        'Model_Mapper_Db_Plugin_Description',
                        array(
                            'mapper' => $this,
                            'table' => $this->getInjector()->getObject('Model_Db_Table_BannersDescription'),
                            'refColumn' => 'banner_id',
                            'descFields' => array(
                                'name', 'html', 'text',
                            ),
                        )
                      )
            )
            ->addPlugin(
                'Resource',
                $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Resource', array('image_id'))
            )        
            ->addPlugin('Multisite', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Multisite', array(
                'siteMapper' => $this->getInjector()->getObject('Model_Mapper_Db_Site'),
                'refTable' => $this->getInjector()->getObject('Model_Db_Table_SiteRef'),
                'refEntityColumn' => 'banner_id',
            )))
        ;                    
        
    }



    /**
     * get random banner by place
     * @return Model_Object_User
     */
    public function fetchRandomByPlace($place)
    {
        if ( ! $place) {
            $this->_throwException('place should be set');
        }
        $select = $this->fetchComplex(NULL, FALSE)
                       ->where('banner_status = 1')
                       ->where('banner_place = ?', $place)
                       ->order('RAND()')
                       ->limit(1)
                       ;
        if ( ! $row = $select->query()->fetch()) {
            /*$this->_throwException('banner with place="'.$place.'" not found!');*/
            return FALSE;
        }
        $object = $this->makeComplexObject($row);
        return $object;
    }
	
	 public function fetchAllByPlace($place)
    {
        if ( ! $place) {
            $this->_throwException('place should be set');
        }
        $select = $this->fetchComplex(NULL, FALSE)
                       ->where('banner_status = 1')
                       ->where('banner_place = ?', $place);
		    if ( ! $row = $select->query()->fetchAll()) {
            return FALSE;
        }
        $object = $this->makeComplexCollection($row);
        return $object;
    }
	
	public function fetchByDescName($name)
    {
        $select = $this->getTable()
                              ->select()->setIntegrityCheck(FALSE)
                              ->from(array('banner'), array('banner_id'))
                              ->joinLeft(
                                    array('banner_description'),
                                     'banner_desc_banner_id = banner_id',
                                    array('banner_desc_name')
                                )
                              ->where('banner_desc_name = ?', $name)
                              ;
        $collection = $this->makeComplexCollection($select->query()->fetchAll());
        return $collection;
    }

}

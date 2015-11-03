<?php

class Catalog_Model_Mapper_Db_Manufacturer extends Model_Mapper_Db_Abstract
{ 
    
    protected $_defaultInjections = array(
        'Model_Object_Interface'     => 'Catalog_Model_Object_Manufacturer',
        'Model_Collection_Interface' => 'Catalog_Model_Collection_Manufacturer',
        'Model_Db_Table_Interface'   => 'Catalog_Model_Db_Table_Manufacturer',
        'Model_Db_Table_Description' => 'Catalog_Model_Db_Table_ManufacturerDescription',
        'Model_Mapper_Db_Plugin_Description',
    	'Model_Mapper_Db_Plugin_Resource',
        'Model_Db_Table_Resources',
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
                        'refColumn' => 'manufacturer_id',
                        'descFields' => array(
                            'name', 'brief', 'full', 
                            'html_title', 'meta_keywords', 'meta_description',
                        ),
                    )
                  )
        )
        ;
        $this->addPlugin('Resource',$this->getInjector()->getObject('Model_Mapper_Db_Plugin_Resource', array('rc_id'), 1));        
        
    }
    
    public function fetchTop($limit, $fetch = TRUE)
    {
        $select = $this->fetchComplex(NULL, FALSE)->limit($limit);
        if ($fetch) {
            $result = $this->makeComplexCollection($select->query()->fetchAll());
        }
        else {
            $result = $select;
        }
        return $result;
    }
    
    
}
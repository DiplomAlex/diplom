<?php

class Catalog_Model_Service_Manufacturer extends Model_Service_Abstract
{ 
    
    protected $_defaultInjections = array(
        'Model_Object_Interface'     => 'Catalog_Model_Object_Manufacturer',
        'Model_Collection_Interface' => 'Catalog_Model_Collection_Manufacturer',
        'Model_Mapper_Interface'     => 'Catalog_Model_Mapper_Db_Manufacturer',  
        'Model_Service_Language',
    );
    
    public function init()
    {
        $lang = $this->getInjector()->getObject('Model_Service_Language');
        $mapper = $this->getMapper();
        $mapper->getPlugin('Description')->setLanguages($lang->getAllActive())->setCurrentLanguage($lang->getCurrent());        
    }
    


    /**
     * get objects fields values for edit form
     * @return array
     */
    public function getEditFormValues($id)
    {
        $obj = $this->getComplex($id);
        $values = $obj->toArray();
        $descs = $this->getMapper()->getPlugin('Description')->fetchDescriptions($id);
        $values = $values + $descs;
        return $values;
    }    
    
    public function getTop($limit)
    {
        return $this->getMapper()->fetchTop($limit);
    }
    
} 
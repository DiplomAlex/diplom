<?php

class Catalog_Model_Mapper_Config_ImporterCategory extends Model_Mapper_Config_Abstract
{
    
    protected $_defaultInjections = array(
        'Model_Collection_Interface' => 'Catalog_Model_Collection_Category',
        'Model_Object_Interface' => 'Catalog_Model_Object_Category',
    );

    /**
     * @param mixed Zend_Config
     * @return Model_Collection_Interface
     */
    public function makeComplexCollection($config)
    {
        $coll = $this->getInjector()->getObject('Model_Collection_Interface');
        if ( ! empty($config)) {
            foreach ($config as $key=>$row) {
                $coll->add($this->makeComplexObject($row));
            }
        }
        return $coll;
    }
    
    
    public function makeSimpleObject(Zend_Config $row)
    {
        $obj = $this->getInjector()->getObject('Model_Object_Interface');
        $obj->guid        = $row->category_guid;
        $obj->delete      = $row->category_del;
        $obj->name        = $row->category_name;
        $obj->parent_guid = $row->category_parent_guid;
        $obj->brief       = $row->category_brief_desc;
        $obj->full        = $row->category_full_desc;
        return $obj;
    }
    
}

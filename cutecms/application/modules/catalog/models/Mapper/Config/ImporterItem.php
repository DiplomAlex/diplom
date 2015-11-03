<?php

class Catalog_Model_Mapper_Config_ImporterItem extends Model_Mapper_Config_Abstract
{

    
    protected $_defaultInjections = array(
        'Model_Collection_Interface' => 'Catalog_Model_Collection_Item',
        'Model_Object_Interface' => 'Catalog_Model_Object_Item',
    );

    /**
     * @param string suffix for make method
     * @param mixed Zend_Config
     * @return Model_Collection_Interface
     */
    protected function _makeCollection($style, $config)
    {
        $coll = $this->getInjector()->getObject('Model_Collection_Interface');
        if ( ! empty($config)) {
            foreach ($config as $row) {
                $coll->add($this->{'make'.ucfirst($style).'Object'}($row));
            }
        }
        return $coll;
    }
    
    
    public function makeSimpleObject(Zend_Config $row)
    {
        $obj = $this->getInjector()->getObject('Model_Object_Interface');
        $obj->guid          = $row->product_guid;
        $obj->delete        = $row->product_del;
        $obj->sku           = $row->product_sku;
        $obj->code          = $row->product_code;
        $obj->unit          = $row->product_unit;
        $obj->name          = $row->product_name;
        $obj->category_guid = $row->product_category_guid;
        $obj->brief         = $row->product_brief_desc;
        $obj->full          = $row->product_full_desc;
        $obj->model         = $row->product_model;
        $obj->manufacturer  = $row->product_manufacturer;
        $obj->stock_qty     = $row->product_qty;
        $obj->param1        = $row->product_param1;
        $obj->param2        = $row->product_param2;
        $obj->param3        = $row->product_param3;
        return $obj;
    }
    
    
    
}

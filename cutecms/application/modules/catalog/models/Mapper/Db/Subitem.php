<?php

class Catalog_Model_Mapper_Db_Subitem extends Model_Mapper_Db_Abstract
{
    
    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Catalog_Model_Object_Subitem',
        'Model_Collection_Interface' => 'Catalog_Model_Collection_Subitem',
        'Model_Db_Table_Interface' => 'Catalog_Model_Db_Table_Item',
        'Model_Db_Table_Description' => 'Catalog_Model_Db_Table_ItemDescription',
        'Model_Mapper_Db_Plugin_Description',
        'Model_Mapper_Db_Plugin_Resource',
        'Model_Db_Table_Resources',
        'Catalog_Model_Db_Table_CategoryItemRef',
        'Model_Mapper_XML_Attribute' => 'Catalog_Model_Mapper_XML_Attribute',
        'Model_Mapper_XML_Brule' => 'Catalog_Model_Mapper_XML_Brule',
        'Model_Mapper_XML_Image' => 'Catalog_Model_Mapper_XML_Image',
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
                        'refColumn' => 'item_id',
                        'descFields' => array(
                            'name', 'brief', 
                        ),
                    )
                  )
        )
        ->addPlugin('Resource',$this->getInjector()->getObject('Model_Mapper_Db_Plugin_Resource', array('rc_id'), 1))
        ;
    }

    protected function _onBuildComplexObject(Model_Object_Interface $obj, array $values = NULL, $addedPrefix = TRUE)
    {
        $obj->attributes = $values['item_attributes_xml'];
        return $obj;
    }

    public function fetchAllAvailable(array $search = NULL, array $order = NULL, $fetch = TRUE)
    {
        $select = $this->fetchComplex(NULL, FALSE);
        
        if ($search AND array_key_exists('sku', $search)) {
            $search['sku'] = trim($search['sku']);
            $select->where('item_sku LIKE ?', $search['sku'].'%');
        }
        else if ($search AND array_key_exists('name', $search)) {
            $search['name'] = trim($search['name']);
            $select->where('lower(item_desc_name) REGEXP ?', App_Utf8::strtolower($search['name']));
        }
        
        
        if (empty($order)) {
            $select->order('item_desc_name ASC');
        }        
        else if ($order AND array_key_exists('sku', $order)) {            
            $select->order('item_sku '.$order['sku']);
        }
        else if ($order AND array_key_exists('name', $order)) {            
            $select->order('item_desc_name '.$order['name']);
        }        
        else if ($order AND array_key_exists('price', $order)) {            
            $select->order('item_price '.$order['price']);
        }
        
        
        if ($fetch === TRUE) {
            $result = $this->makeComplexCollection($select->query()->fetchAll());
        }
        else {
            $result = $select;
        }
        return $result;
    }
    
    public function paginatorFetchAllAvailable(array $search, array $order, $rowsPerPage, $page)
    {
        return $this->paginator($this->fetchAllAvailable($search, $order, FALSE), $rowsPerPage, $page);
    }
    
}
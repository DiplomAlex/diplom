<?php

class Catalog_Model_Mapper_Db_ItemBundle extends Model_Mapper_Db_Abstract
{
    
    protected $_defaultInjections = array(
        'Model_Object_Interface'     => 'Catalog_Model_Object_ItemBundle',
        'Model_Collection_Interface' => 'Catalog_Model_Collection_ItemBundle',
        'Model_Db_Table_Interface'   => 'Catalog_Model_Db_Table_ItemBundle',
        'Model_Object_Subitem'       => 'Catalog_Model_Object_Subitem',
        'Model_Collection_Subitem'   => 'Catalog_Model_Collection_Subitem',
        'Model_Mapper_XML_Subitem'   => 'Catalog_Model_Mapper_XML_Subitem',  
        'Model_Db_Table_SubitemRef'  => 'Catalog_Model_Db_Table_ItemBundleSubitemRef',
    );
    
    protected function _preSaveComplex(Model_Object_Interface $obj, array $values)
    {
        $obj->subitems_xml = $this->getInjector()->getObject('Model_Mapper_XML_Subitem')->unmapCollectionToXML($obj->subitems);
        return $obj;
    }
    
    protected function _postSaveComplex(Model_Object_Interface $obj, array $values)
    {
        $table = $this->getInjector()->getObject('Model_Db_Table_SubitemRef');
        $tableName = $table->getTableName();
        $tablePrefix = $table->getColumnPrefix().$table->getPrefixSeparator();
        $table->delete(array($tablePrefix.'bundle_id = ?'=>$obj->id));
        foreach ($obj->subitems as $sub) {
            $table->insert(array($tablePrefix.'bundle_id'=>$obj->id, 
                                 $tablePrefix.'item_id'=>$obj->item_id, 
                                 $tablePrefix.'subitem_id'=>$sub->id,));
        }
        return $obj;
    }    

    protected function _onBuildComplexObject(Model_Object_Interface $obj, array $values = NULL, $addedPrefix = TRUE)
    {
        $obj->subitems = $obj->subitems_xml;
        return $obj;
    }
    
    public function fetchBundlesForItem($itemId)
    {
        if ( ! $itemId) {
            $result = $this->getInjector()->getObject('Model_Collection_Interface');
        }
        else {
            $result = $this->fetchComplex(array('bundle_item_id = ?' => $itemId));
        } 
        return $result;
    }
    
    public function clearBundlesForItem($itemId)
    {
        $this->getTable()->delete(array('bundle_item_id = ?'=> (int) $itemId));
        return $this;
    }

    
    
}
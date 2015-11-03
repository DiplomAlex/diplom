<?php

class Catalog_Model_Mapper_Db_Attribute extends Model_Mapper_Db_Abstract
{

    protected $_defaultInjections = array(
        'Model_Db_Table_Interface' => 'Catalog_Model_Db_Table_Attribute',
        'Model_Object_Interface' => 'Catalog_Model_Object_Attribute',
        'Model_Collection_Interface' => 'Catalog_Model_Collection_Attribute',


        'Model_Db_Table_Description' => 'Catalog_Model_Db_Table_AttributeDescription',
        'Model_Mapper_Db_Plugin_Description',
        'Model_Mapper_Db_Plugin_Sorting',

        'Catalog_Model_Db_Table_Attribute_Group_Ref' => 'Catalog_Model_Db_Table_AttributeGroupRef',

        'Model_Mapper_XML_Variant' => 'Catalog_Model_Mapper_XML_AttributeVariant',
        'Model_Mapper_XML_Attribute' => 'Catalog_Model_Mapper_XML_Attribute',
        'Model_Db_Table_Item' => 'Catalog_Model_Db_Table_Item',
    
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
                        'refColumn' => 'attr_id',
                        'descFields' => array(
                            'name', 'brief',
                        ),
                    )
                  )
        )
        ->addPlugin('Sorting', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Sorting'))
        ;
    }


    protected function _onBuildComplexObject(Model_Object_Interface $obj, array $values = NULL, $addedPrefix = TRUE)
    {
        $obj->variants = $obj->variants_xml;
        return $obj;
    }



    protected function _preSaveComplex(Model_Object_Interface $obj, array $values)
    {
        $obj->variants_xml = $this->getInjector()->getObject('Model_Mapper_XML_Variant')->unmapCollectionToXML($obj->variants);
        return $obj;
    }


    /**
     * @param Model_Object_Interface
     * @param array
     * @return Model_Object_Interface
     */
    protected function _postSaveComplex(Model_Object_Interface $obj, array $values)
    {
        $refTable = $this->getTable('catalog/attribute_group_ref');
        $refTable->delete(array('agr_attr_id = ?' => $obj->id));
        if (is_array($values['attribute_groups'])) {
            $groups = $values['attribute_groups'];
        }
        else if (is_array($obj->attribute_groups)) {
            $groups = $obj->attribute_groups;
        }
        else {
            $groups = array();
        }
        if (is_array($groups)) {
            $i = 0;
            foreach ($groups as $group) {
                $refTable->insert(array(
                    'agr_attr_id' => $obj->id,
                    'agr_ag_id' => $group,
                    'agr_sort' => $i++,
                ));
            }
        }
        $this->_updateItemsAttribute($obj);
        return $obj;
    }

    protected function _updateItemsAttribute(Model_Object_Interface $attr) {
        $itemTable = $this->getInjector()->getObject('Model_Db_Table_Item');
        $itemAttrsField = $itemTable->getColumnPrefix().$itemTable->getPrefixSeparator().'attributes_xml';
        $xmlMapper = $this->getInjector()->getObject('Model_Mapper_XML_Attribute');
        $class = $this->getInjector()->getInjection('Model_Object_Interface');
        $query =         
                '                           
                UPDATE '.$itemTable->getTableName().'
                SET '.$itemAttrsField.' = 
                    UPDATEXML('.$itemAttrsField.', 
                        \'//collection/object[@class="'.$class.'"]/code[text()="'.$attr->code.'"]/..\',
                        UPDATEXML(\''.addslashes($xmlMapper->unmapObjectToXml($attr)).'\', 
                            \'//object[@class="'.$class.'"]/current_value\', 
                            CONCAT(\'<current_value><![CDATA[\',
                                EXTRACTVALUE('.$itemAttrsField.', 
                                    \'//collection/object[@class="'.$class.'"]/code[text()="'.$attr->code.'"]/../current_value\'
                                ), 
                                \']]></current_value>\'
                            )
                        )
                    ) 
                WHERE 
                    NOT ISNULL(
                        EXTRACTVALUE('.$itemAttrsField.', 
                            \'//collection/object[@class="'.$class.'"]/code[text()="'.$attr->code.'"]\'
                        )
                    )                            
                ';                            
                            
        try {
            $this->getTable()->getAdapter()->getConnection()->query($query);
        }
        catch (Exception $e) {
            /* no mysql 5.1 */;
        }
    }
        

    /**
     * @param int
     * @param int
     * @param int
     * @return Zend_Paginator
     */
    public function paginatorFetchComplexByGroup($group, $rows, $page)
    {
        $query = $this->fetchComplexByGroup($group, FALSE);
        return $this->paginator($query, $rows, $page);
    }

    
    public function fetchComplexByGroup($group, $fetch = TRUE)
    {
        $query = $this->fetchComplex(NULL, FALSE);
        if ($group > 0) {
            $query->distinct(TRUE)
                  ->joinLeft(array('agr'=>'attribute_group_ref'), 'agr_attr_id = attr_id', array())
                  ->where('agr_ag_id = ?', $group)
                  ;
        }
        if ($fetch === TRUE) {
            $result = $this->makeComplexCollection($query->query()->fetchAll());
        }        
        else {
            $result = $query;
        }
        return $result;
    }    

    public function fetchComplexByCode($code)
    {
        return $this->fetchComplex(array('attr_code = ?'=>$code), TRUE);
    }

}


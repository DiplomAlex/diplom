<?php

class Catalog_Model_Mapper_XML_Attribute extends Model_Mapper_XML_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Catalog_Model_Object_Attribute',
        'Model_Collection_Interface' => 'Catalog_Model_Collection_Attribute',
        'Model_Object_Variant' => 'Catalog_Model_Object_AttributeVariant',
        'Model_Collection_Variant' => 'Catalog_Model_Collection_AttributeVariant',
    );

}
<?php

class Catalog_Model_Mapper_XML_Subitem extends Model_Mapper_XML_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface'     => 'Catalog_Model_Object_Subitem',
        'Model_Collection_Interface' => 'Catalog_Model_Collection_Subitem',
    );

}
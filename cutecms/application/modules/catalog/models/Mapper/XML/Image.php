<?php

class Catalog_Model_Mapper_XML_Image extends Model_Mapper_XML_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Catalog_Model_Object_Image',
        'Model_Collection_Interface' => 'Catalog_Model_Collection_Image',
    );

}
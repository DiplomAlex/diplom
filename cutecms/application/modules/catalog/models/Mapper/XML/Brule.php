<?php

class Catalog_Model_Mapper_XML_Brule extends Model_Mapper_XML_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface'     => 'Catalog_Model_Object_Brule',
        'Model_Collection_Interface' => 'Catalog_Model_Collection_Brule',
    );

}
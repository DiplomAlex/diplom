<?php

class Catalog_Model_Mapper_Db_AttributeVariant extends Model_Mapper_Db_Abstract
{

    protected $_defaultInjections = array(
        'Model_Db_Table_Interface' => 'Catalog_Model_Db_Table_AttributeVariant',
        'Model_Object_Interface' => 'Catalog_Model_Object_AttributeVariant',
        'Model_Collection_Interface' => 'Catalog_Model_Collection_AttributeVariant',


        'Model_Db_Table_Description' => 'Catalog_Model_Db_Table_AttributeVariantDescription',
        'Model_Mapper_Db_Plugin_Description',
        'Model_Mapper_Db_Plugin_Sorting',
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
                        'refColumn' => 'option_id',
                        'descFields' => array(
                            'text',
                        ),
                    )
                  )
        )
        ->addPlugin('Sorting', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Sorting'))
        ;
    }



}
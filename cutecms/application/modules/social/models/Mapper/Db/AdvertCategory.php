<?php

class Social_Model_Mapper_Db_AdvertCategory extends Model_Mapper_Db_Abstract
{

    protected $_defaultInjections = array(
        'Model_Db_Table_Interface' => 'Social_Model_Db_Table_AdvertCategory',
        'Model_Object_Interface' => 'Social_Model_Object_AdvertCategory',
        'Model_Collection_Interface' => 'Social_Model_Collection_AdvertCategory',

        'Model_Db_Table_Description' => 'Social_Model_Db_Table_AdvertCategoryDescription',
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
                        'refColumn' => 'adv_category_id',
                        'descFields' => array(
                            'name', 'brief', 'full',
                        ),
                    )
                  )
        )
        ->addPlugin('Sorting', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Sorting'))
        ;
    }



    protected function _onBuildComplexObject(Model_Object_Interface $object, array $values = NULL, $addedPrefix = TRUE)
    {
        /*
        $object->category_fields = $this->getMapper('social/advert-field')->makeSimpleCollection($values['advert_category_fields']);
        $object->fields = $this->getMapper('social/advert-field')->makeSimpleCollection($values['fields']);
        */
        return $object;
    }


}
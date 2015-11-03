<?php

class Social_Model_Mapper_Db_Advert extends Model_Mapper_Db_Abstract
{

    protected $_defaultInjections = array(
        'Model_Db_Table_Interface' => 'Social_Model_Db_Table_Advert',
        'Model_Object_Interface' => 'Social_Model_Object_Advert',
        'Model_Collection_Interface' => 'Social_Model_Collection_Advert',
    );

    protected function _onFetchComplex(Zend_Db_Select $select)
    {
        $select -> joinLeft(array('cat'=>'advert_category'),
                            'advert_category_id = adv_category_id',
                            array('advert_category_fields'=>'adv_category_fields'))
                -> joinLeft(array('cat_desc'=>'advert_category_description'),
                            'advert_category_id = adv_cat_desc_adv_category_id AND adv_cat_desc_language_id = '.Model_Service::factory('language')->getCurrent()->id,
                            array('advert_category_name'=>'adv_cat_desc_name',
                                  'advert_category_brief'=>'adv_cat_desc_brief',
                                  'advert_category_full'=>'adv_cat_desc_full',))
                -> joinLeft(array('adder'=>'user'), 'adder.user_id = advert_adder_id',
                            array('advert_adder_name'=>'adder.user_name',
                                  'advert_adder_login'=>'adder.user_login'))
                -> order('advert.advert_date_added DESC');
        return $select;
    }

    protected function _onBuildComplexObject(Model_Object_Interface $object, array $values = NULL, $addedPrefix = TRUE)
    {
        /*$fieldService = Model_Service::factory('social/advert-field');
        $object->category_fields = $fieldService->getCollectionFromXML($values['avdert_category_fields']);
        $object->fields = $fieldService->getCollectionFromXML($values['fields']);*/
        return $object;
    }

}

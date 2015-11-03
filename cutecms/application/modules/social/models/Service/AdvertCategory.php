<?php

class Social_Model_Service_AdvertCategory extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
        'Model_Mapper_Interface' => 'Social_Model_Mapper_Db_AdvertCategory',
        'Model_Object_Interface' => 'Social_Model_Object_AdvertCategory',
        'Model_Service_Language',
    );


    /**
     * initializes object
     * @see Model_Service_Abstract::init()
     */
    public function init()
    {
        $lang = $this->getInjector()->getObject('Model_Service_Language');
        $this->getMapper()->getPlugin('Description')->setLanguages($lang->getAllActive())->setCurrentLanguage($lang->getCurrent());
    }


    /**
     * get objects fields values for edit form
     * @return array
     */
    public function getEditFormValues($id)
    {
        $obj = $this->getComplex($id);
        $values = $obj->toArray();
        $descs = $this->getMapper()->getPlugin('Description')->fetchDescriptions($id);
        $values = $values + $descs;
        return $values;
    }

}

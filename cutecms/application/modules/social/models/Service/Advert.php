<?php

class Social_Model_Service_Advert extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
        'Model_Mapper_Interface' => 'Social_Model_Mapper_Db_Advert',
        'Model_Object_Interface' => 'Social_Model_Object_Advert',
        'Model_Service_Helper_Fields' => 'Social_Model_Service_Helper_AdvertCategoryFields'
    );


    public function init()
    {
        $this->addHelper('Fields', $this->getInjector()->getObject('Model_Service_Helper_Fields'));
    }

    public function getTitle(Model_Object_Interface $advert)
    {
        return $this->getHelper('Fields')->getAdvertTitle($advert);
    }


    protected function _generateGroupKey()
    {
        return md5(App_Uuid::get());
    }

    /**
     * save it
     * @param array
     */
    public function saveFromValues(array $values)
    {
        /**
         * this "if" should be removed while refactoring
         */
        if (empty($values['id'])) {
            unset($values['id']);
        }
        if (empty($values['group_key'])) {
            $values['group_key'] = $this->_generateGroupKey();
        }
        $this->getMapper()->saveComplex($values);
        return $this;
    }

    public function getAutomateModelsList($asArray = TRUE)
    {
        $str = Model_Service::factory('config')->read('var/automates', 'models')->list;
        if ($asArray === TRUE) {
            $list = explode('|', $str);
            $result = array();
            foreach ($list  as $model) {
                $result[$model] = $model;
            }
        }
        else {
            $result = str_replace('|', "\r\n", $str);
        }
        return $result;
    }

    public function saveAutomateModelsList($value)
    {
        $configService = Model_Service::factory('config');
        $config = $configService->read('var/automates', NULL, FALSE);
        $str = str_replace("\r", '', $value);
        $str = str_replace("\n\n", "\n", $str);
        $str = str_replace("\n", '|', $str);
        $list = explode('|', $str);
        foreach ($list as $key=>$val) {
            $val = trim($val);
            if (empty($val)) {
                unset($list[$key]);
            }
        }
        $config->models->list = implode('|', $list);
        $configService->write($config, 'var/automates');
    }

}
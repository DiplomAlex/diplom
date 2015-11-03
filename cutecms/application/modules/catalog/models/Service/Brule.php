<?php

class Catalog_Model_Service_Brule extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Catalog_Model_Object_Brule',
        'Model_Collection_Interface' => 'Catalog_Model_Collection_Brule',
    );

    protected function _getAllAvailableFor($section, $forSelect = TRUE)
    {
        $cacheKey = __CLASS__.'__'.__FUNCTION__.'__'.$section.'__'.(string) $forSelect;
        $cache = Zend_Registry::get('Zend_Cache');
        if ( ! $data = $cache->load($cacheKey)) {
            $config = Model_Service::factory('config')->read('var/item_brules.xml', 'item');
            if ($forSelect) {
                $data = array();
                foreach ($config as $key=>$val) {
                    $data[$key] = $this->getTranslator()->_('brule_'.$section.'.'.$key);
                }
            }
            else {
                $data = $config;
            }
            $cache->save($data, $cacheKey);
        }
        return $data;
    }

    public function getAllAvailableForItem($forSelect = TRUE)
    {
        return $this->_getAllAvailableFor('item', $forSelect);
    }

    public function getAllAvailableForOrder($forSelect = TRUE)
    {
        return $this->_getAllAvailableFor('order', $forSelect);
    }

}
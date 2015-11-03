<?php

class Checkout_Model_Service_Brule extends Model_Service_Abstract
{

    const TYPE_TOTAL = 'total';
    const TYPE_SHIPMENT = 'shipment';
    const TYPE_PAYMENT = 'payment';

    protected $_defaultInjections = array(
        'Model_Object_Interface'     => 'Checkout_Model_Object_Brule',
        'Model_Collection_Interface' => 'Checkout_Model_Collection_Brule',
        'Model_Mapper_Interface'     => 'Checkout_Model_Mapper_XML_Brule',
    );

    protected $_configFilename = 'var/order_brules.xml';

    protected function _getConfigService()
    {
        $result = Model_Service::factory('config');
        return $result;
    }

    /**
     * @param string - $type of bruleset
     * @return Zend_Config;
     */
    public function getAll($type = self::TYPE_TOTAL)
    {
        return $this->_getConfigService()->read($this->_configFilename, $type, FALSE);
    }

    /**
     * @param Zend_Config - one brule or set of brules if $code === NULL
     * @param string - $code of brule, or NULL
     * @param string - $type
     * @return $this
     */
    public function save(Zend_Config $brule, $code, $type)
    {
        $all = $this->_getConfigService()->read($this->_configFilename, NULL, FALSE);
        if ($code === NULL) {
            $all->{$type}->merge($brule);
        }
        else {
            $all->{$type}->{$code}->merge($brule);
        }
        $this->_getConfigService()->write($all, $this->_configFilename);
        return $this;
    }

}
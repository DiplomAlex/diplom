<?php

class Checkout_Model_Service_Shipment extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface'     => 'Checkout_Model_Object_Shipment',
        'Model_Collection_Interface' => 'Checkout_Model_Collection_Shipment',
        'Model_Mapper_Interface'     => 'Checkout_Model_Mapper_Config_Shipment',
    );

    protected function _getBruleService()
    {
        $result = Model_Service::factory('checkout/brule');
        return $result;
    }

    protected function _getLangService()
    {
        $result = Model_Service::factory('language');
        return $result;
    }

    public function init()
    {
        $this->getMapper()->setLanguage ($this->_getLangService()->getCurrent())
                          ->setLanguages($this->_getLangService()->getAllActive());
    }

    public function getAll($style = Model_Object_Interface::STYLE_CUSTOM)
    {
        if ($style == Model_Object_Interface::STYLE_CUSTOM) {
            $func = 'makeCustomObject';
        }
        else if ($style == Model_Object_Interface::STYLE_COMPLEX) {
            $func = 'makeComplexObject';
        }
        else {
            $func = 'makeSimpleObject';
        }
        $allConfig = $this->_getBruleService()->getAll(Checkout_Model_Service_Brule::TYPE_SHIPMENT);
        $coll = $this->getInjector()->getObject('Model_Collection_Interface');
        foreach ($allConfig as $method=>$data) {
            $obj = $this->getMapper()->{$func}($data);
            $obj->method = $method;
            $coll->add($obj);
        }
        return $coll;
    }

    public function get($id)
    {
        $all = $this->getAll();
        $found = NULL;
        foreach ($all as $obj) {
            if ($obj->method == $id) {
                $found = $obj;
                break;
            }
        }
        if ( ! $found) {
            $this->_throwException('shipment "'.$id.'" is not installed');
        }
        return $found;
    }

    public function getComplex($id)
    {
        $all = $this->getAll(Model_Object_Interface::STYLE_COMPLEX);
        $found = NULL;
        foreach ($all as $obj) {
            if ($obj->method == $id) {
                $found = $obj;
                break;
            }
        }
        if ( ! $found) {
            $this->_throwException('shipment "'.$id.'" is not installed');
        }
        return $found;
    }

    /**
     * if field is in list of array or i18n fields, then in $values in should be presented as
     * fieldname__innerkey
     */
    public function getEditFormValues($obj)
    {
        if ($obj instanceof Model_Object_Interface) {
            $method = $obj->method;
        }
        else {
            $method = $obj;
            $obj = $this->getComplex($method);
        }
        $arrayFields = $this->getMapper()->getArrayFields();
        $i18nFields = $this->getMapper()->getI18nFields();
        $langs = $this->_getLangService()->getAllActive();
        $values = array();
        foreach ($obj->getElements() as $key=>$value) {
            if ((in_array($key, $i18nFields) OR in_array($key, $arrayFields)) AND (is_array($value) OR ($value instanceof Traversable))) {
                foreach ($value as $innerKey=>$innerValue) {
                    $values[$key.'__'.$innerKey] = $innerValue;
                }
            }
            else {
                $values[$key] = $value;
            }
        }
        return $values;
    }



    public function save(Model_Object_Interface $object)
    {
        $config = $this->getMapper()->unmapSimpleObject($object);
        $this->_getBruleService()->save($config, $object->method, Checkout_Model_Service_Brule::TYPE_SHIPMENT);
        return $this;
    }

    public function saveComplex(Model_Object_Interface $object)
    {
        return $this->save($object);
    }

    /**
     * if field is in list of array or i18n fields, then in $values in should be presented as
     * fieldname__innerkey
     */
    public function saveFromValues(array $values, $returnObj = FALSE)
    {
        $obj = $this->get($values['method']);
        $newObj = $this->getMapper()->makeCustomObject($values);
        $arrayFields = $this->getMapper()->getArrayFields();
        $i18nFields = $this->getMapper()->getI18nFields();
        $langs = $this->_getLangService()->getAllActive();
        foreach ($newObj->getElements() as $key=>$value) {
            if (in_array($key, $values)) {
                $obj->{$key} = $values[$key];
            }
            else if (in_array($key, $i18nFields)) {
                $arr = array();
                foreach ($langs as $lang) {
                    $lKey = $key.'__'.$lang->code2;
                    if (array_key_exists($lKey, $values)) {
                        $arr[$lang->code2] = $values[$lKey];
                    }
                }
                $obj->{$key} = $arr;
            }
            else if (in_array($key, $arrayFields)) {
                $arr = array();
                foreach ($values as $innerKey=>$innerValue) {
                    $strlenPlus2 = strlen($key)+2;
                    if (substr($innerKey, 0, $strlenPlus2) == $key.'__') {
                        $arrayKey = substr($innerKey, $strlenPlus2);
                        $arr[$arrayKey] = $innerValue;
                    }
                }
                $obj->{$key} = $arr;
            }
        }
        $this->save($obj);
        if ($returnObj) {
            return $obj;
        }
        else {
            return $this;
        }
    }

    public function changeSorting($method, $position)
    {
        $coll = $this->getAll(Model_Object_Interface::STYLE_COMPLEX);
        $coll->changeSorting($coll->findOneIndexByMethod($method), $position);
        $config = $this->getMapper()->unmapComplexCollection($coll);
        $this->_getBruleService()->save($config, NULL, Checkout_Model_Service_Brule::TYPE_SHIPMENT);
        return $this;
    }

}
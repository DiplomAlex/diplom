<?php

class Checkout_Model_Mapper_XML_Payment extends Model_Mapper_XML_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface'     => 'Checkout_Model_Object_Payment',
        'Model_Collection_Interface' => 'Checkout_Model_Collection_Payment',
    );

    /**
     * @var array
     */
    protected $_arrayFields = array(
        'client_requisites',
        'seller_requisites',
        'params',
        'allowed_shipments'
    );    
    
    protected function _isArray($xml)
    {
        $result = FALSE;
        if (( ! empty($xml) AND ($attrs = $xml->attributes()) AND ($class = $attrs['class']) AND ($class=='array')) 
            OR (in_array($xml->getName(), $this->_arrayFields))) {
            $result = TRUE;
        }
        return $result;
    }
    
    public function makeSimpleObject($xml, $addedPrefix = TRUE)
    {
        $attrs = $xml->attributes();
        if (( ! $objClass = $attrs['class']) OR empty($objClass)) {
            $objClass = 'Model_Object_Interface';
        }
        $object = $this->getInjector()->getObject($this->getInjector()->getInjectionKey($objClass));
        foreach ($object->getElements() as $name=>$val) {
            if ($this->_isArray($xml->{$name})) {
                $object->{$name} = unserialize( (string) $xml->{$name});
            }
            else if ($xml->{$name} AND count($xml->{$name}->children()) AND $this->_isCollection($xml->{$name})) {
                $object->{$name} = $this->makeSimpleCollection($xml->{$name});
            }
            else if (($xml->{$name}) AND (count($xml->{$name}->children()))) {
                    $object->{$name} = $this->makeSimpleObject($xml->{$name});
            }
            else {
                $object->{$name} = (string) $xml->{$name};
            }
        }
        return $object;
    }
        

}
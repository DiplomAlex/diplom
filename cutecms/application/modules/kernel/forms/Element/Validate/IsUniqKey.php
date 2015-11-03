<?php

/**
 * Checks if the field value is uniq in db  
 */

class Form_Element_Validate_IsUniqKey extends Zend_Validate_Abstract
{
    
    const NOT_UNIQ = 'This field should be uniq';

    /**
     * @var Model_Service_Interface
     */
    protected $_service = NULL;
    
    /**
     * @var string
     */
    protected $_fieldToMatch = NULL;
    
    /**
     * @var array
     */
    protected $_addonFields = array();
    
    /**
     * @var bool
     */
    protected $_translitAddonFields = TRUE;
    
    /**
     * @var string
     */
    protected $_fieldId = 'id';
    
    /**
     * @param Model_Service_Interface $service
     * @param string $fieldToMatch
     * @param array $addonFields
     * @param bool $translit 
     */
    public function __construct($service, $fieldToMatch, array $addonFields = array(), $translit = TRUE, $fieldId = 'id')
    {
        $this->_service = $service;
        $this->_fieldToMatch = $fieldToMatch;
        $this->_addonFields = $addonFields;
        $this->_translitAddonFields = $translit;
        $this->_fieldId = $fieldId;        
    }
    
    /**
     * (non-PHPdoc)
     * @see application/library/Zend/Validate/Zend_Validate_Interface::isValid()
     */
    public function isValid($value, $context = NULL)
    {
        if (empty($value)) {
            foreach ($this->_addonFields as $field) {
                if ( ! empty($field)) {
                    $value = $context[$field];
                    if ($this->_translitAddonFields) {
                        $value = App_Utf8::clean($value);
                    }
                    break;
                }
            }
        }
        $cnt = $this->_service->countFieldValues($value, $this->_fieldToMatch, $context[$this->_fieldId]);
        $isValid = ($cnt == 0);
        if ( ! $isValid) {
            $this->_error(self::NOT_UNIQ);
        }
        return $isValid;
    } 
    
}
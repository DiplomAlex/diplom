<?php

class Model_Object_Observer_SeoId extends App_Event_Observer
{
    
    protected $_fieldsToProcess = array(
        'seo_id', 'name', 'title', 
    );

    public function onBeforeSave()
    {
        $obj = $this->getData(0);
        $values = $this->getData(0);
        if ($obj->hasElement('seo_id')) {
            $found = FALSE;
            foreach ($this->_fieldsToProcess as $field) {
                if ($obj->hasElement($field) AND ! empty($obj->{$field})) {
                    $val = $obj->{$field};
                    $found = TRUE;
                    break;
                }
                else if (array_key_exists($field, $values) AND ! empty($values[$field])) {
                    $val = $values[$field];
                    $found = TRUE;
                    break;
                }
            }
            if ($found) {
				$seoId = App_Utf8::urlClean($val);
				
				$filter = new Zend_Filter_Word_CamelCaseToDash;
				$serviceName = strtolower($filter->filter(substr(get_class($obj), 13))); // 13 is length of 'Model_Object_' string

				while ( ($obj->id == NULL and Model_Service::factory($serviceName)->checkSeoId($seoId) > 0)
                     or (isset($obj->id) and Model_Service::factory($serviceName)->checkSeoId($seoId) > 1)) {
					$seoId .= '-';
					
				}
				
				$obj->seo_id = $seoId;
            }
        }
    }

}
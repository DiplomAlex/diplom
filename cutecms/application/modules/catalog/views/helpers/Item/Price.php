<?php

class Catalog_View_Helper_Item_Price extends Zend_View_Helper_Abstract
{

	/**
	 * if first param is Model_Object_Interface then price is calculated
	 * @param mixed -  Model_Object_Interface|float  $item|$price	 	
	 * @return string
	 */
    public function item_Price($item, $valueOnly = FALSE, $inDefaultCurrency = FALSE)
    {
    	if ($item instanceof Model_Object_Interface) {
        	$svc = Model_Service::factory('catalog/item');
        	$value = $svc->calculatePrice($item, $inDefaultCurrency);
    	}
    	else {
    		$value = $item;
    	}
        $curr = Model_Service::factory('currency')->getCurrent();
        if ($valueOnly === TRUE) {
        	$result = round($value, 2);
        }
        else {
        	$result = $curr->signPre.sprintf('%01.2f', $value).' '.$curr->signPost;
        }        
        return $result;
    }

}
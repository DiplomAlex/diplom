<?php

class Catalog_Model_Service_Helper_Brule_Item extends Model_Service_Helper_Abstract
{

    public function calculatePrice(Model_Object_Interface $item)
    {
        $allBrules = Model_Service::factory('catalog/brule')->getAllAvailableForItem(FALSE);
        $rawPrice = $item->price;
        if ($item->brules->count()) {
            $price = NULL;
            foreach ($item->brules as $brule) {
                $class = $allBrules->{$brule->code}->class;
                $brObj = $this->getService()->getInjector()->getObject($class, $brule);
                $price = $brObj->setRawPrice($rawPrice)->setPrevPrice($price)->calculatePrice($item);
            }
        }
        else {
            $price = $rawPrice;
        }        
        return $price;
    }



    public function calculateDiscountPercent(Model_Object_Interface $item)
    {
        $allBrules = Model_Service::factory('catalog/brule')->getAllAvailableForItem(FALSE);
        $rawPrice = $item->price;
        $price = NULL;
        $discount = 0;
        foreach ($item->brules as $brule) {
            $class = $allBrules->{$brule->code}->class;
            $brObj = $this->getService()->getInjector()->getObject($class, $brule);
            $price = $brObj->setRawPrice($rawPrice)->setPrevPrice($price)->calculatePrice($item);
            $discount = $brObj->calculateDiscountPercent($item);
        }
        return $discount;
    }


    public function calculateDiscountValue(Model_Object_Interface $item)
    {
        $allBrules = Model_Service::factory('catalog/brule')->getAllAvailableForItem(FALSE);
        $rawPrice = $item->price;
        $price = NULL;
        $discount = 0;
        foreach ($item->brules as $brule) {
            $class = $allBrules->{$brule->code}->class;
            $brObj = $this->getService()->getInjector()->getObject($class, $brule);
            $price = $brObj->setRawPrice($rawPrice)->setPrevPrice($price)->calculatePrice($item);
            $discount = $brObj->calculateDiscountValue($item);
        }
        return $discount;
    }


}
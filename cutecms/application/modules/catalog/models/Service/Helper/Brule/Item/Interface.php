<?php

interface Catalog_Model_Service_Helper_Brule_Item_Interface
{

    /**
     * @param Catalog_Model_Object_Brule
     */
    public function __construct(Model_Object_Interface $obj);


    /**
     * @param Catalog_Model_Object_Item
     */
    public function calculatePrice(Model_Object_Interface $obj);
    public function calculateDiscountValue(Model_Object_Interface $obj);
    public function calculateDiscountPercent(Model_Object_Interface $obj);

    /**
     * @param raw value of items price
     */
    public function setRawPrice($value);

    /**
     * @param value of price calculated by previous rule
     */
    public function setPrevPrice($value);

}
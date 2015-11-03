<?php

interface Checkout_Model_Service_Helper_Brule_Total_Interface
{

    /**
     * set order of rule
     */
    public function setOrder(Model_Object_Interface $order = NULL);

    /**
     * set item collection of rule
     */
    public function setItems(Model_Collection_Interface $items = NULL);

    /**
     * set raw summ of all items in collection without total brule
     * @param float
     * @return $this
     */
    public function setRawSumm($summ);

    /**
     * set summ as the result of previous total business rule
     * @param float
     * @return $this
     */
    public function setPrevSumm($summ);

    /**
     * returns total qty in order or items collection
     */
    public function calculateTotalQty();


    /**
     * returns new result of calculations (=prevSumm-calculateDiscountValue())
     */
    public function calculateTotal();

    /**
     * the value to decrease/increase prevSumm
     */
    public function calculateDiscountValue();

    /**
     * percent to increase rawSumm
     */
    public function calculateDiscountPercent();

    /**
     * returns the mark if the results of the brule calculations should be outputted
     */
    public function isVisible();

    /**
     * returns the summ that should be shown in results view
     */
    public function getOutputSumm();
}

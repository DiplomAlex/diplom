<?php

class Checkout_Model_Service_Helper_Brule_Total extends Model_Service_Helper_Abstract
{

    protected $_items = NULL;

    protected $_order = NULL;

    /**
     * set order of rule
     * @param Model_Object_Interface
     * @return $this
     */
    public function setOrder(Model_Object_Interface $order = NULL)
    {
        $this->_order = $order;
        if ($order !== NULL) {
            $this->_items = $order->items;
        }
        return $this;
    }

    /**
     * @return Model_Object_Interface
     */
    public function getOrder()
    {
        return $this->_order;
    }


    /**
     * set items of rule
     * @param Model_Collection_Interface
     * @return $this
     */
    public function setItems(Model_Collection_Interface $items)
    {
        $this->_items = $items;
        return $this;
    }

    /**
     * get items
     * @return Model_Collection_Interface
     */
    public function getItems()
    {
        foreach ($this->_items as $item) {
            $item->price = $item->remain_price;
        }
        unset($item);

        return $this->_items;
    }

    public function calculateTotal()
    {
        if (!$this->getItems()) {
            return 0;
        }
        $rawSumm = null;
        $summ = null;
        $allBrules = Model_Service::factory('checkout/brule')->getAll(
            Checkout_Model_Service_Brule::TYPE_TOTAL
        );
        foreach ($allBrules as $brule) {
            if ((int)$brule->active) {
                $brObj = $this->_createBruleCalculator($brule);

                $summ = $brObj
                    ->setItems($this->getItems())->setOrder($this->getOrder())
                    ->setRawSumm($rawSumm)->setPrevSumm($summ)
                    ->calculateTotal();
            }
        }
        return $summ;
    }

    public function calculateTotalQty()
    {
        $qty = 0;
        if ($items = $this->getItems()) {
            foreach ($items as $key=>$item) {
                $qty += (float) $item->qty;
            }
        }
        return $qty;

    }

    public function getRows()
    {
        $brules = Model_Service::factory('checkout/brule')->getAll(Checkout_Model_Service_Brule::TYPE_TOTAL);
        $lang = Model_Service::factory('language')->getCurrent();
        $rawSumm = NULL;
        $summ = NULL;
        $rows = array();
        foreach ($brules as $brule) {
            if ($brule->active > 0) {
                /** @var $brObj Checkout_Model_Service_Helper_Brule_Total_Shipment */
                $brObj = $this->_createBruleCalculator($brule);
                $brObj->setOrder($this->getOrder())->setItems($this->getItems());
                if ($rawSumm === NULL) {
                    $rawSumm = $brObj->getRawSumm();
                }
                $brObj->setRawSumm($rawSumm)->setPrevSumm($summ);
                $summ = $brObj->calculateTotal();
                if ($brObj->isVisible()) {
                    $rows[] = array(
                        'title' => $brule->title->{$lang->code2},
                        'percent' => $brObj->calculateDiscountPercent(),
                        'value' => $brObj->calculateDiscountValue(),
                        'total' => $summ,
                        'summ' => $brObj->getOutputSumm(),
                    );
                }
            }
        }
        return $rows;
    }

    protected function _createBruleCalculator($brule)
    {
        return $this->getService()->getInjector()->getObject($brule->class, $brule);
    }

}
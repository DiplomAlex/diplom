<?php

abstract class Checkout_Model_Service_Helper_Brule_Total_Abstract implements Checkout_Model_Service_Helper_Brule_Total_Interface
{


    /**
     * @var Model_Collection_Interface
     */
    protected $_items = NULL;

    /**
     * @var Model_Object_Interface
     */
    protected $_order = NULL;

    /**
     * calculating results caching
     */
    protected $_cache = array('total' => NULL, 'totalQty' => NULL,  'discountValue' => NULL, 'discountPercent' => NULL,);

    /**
     * "raw" summ of order - usually sum(price*qty) of all order's items
     * @var float
     */
    protected $_rawSumm = NULL;
    /**
     * "previous" summ means the result of calculateTotal of previous rule
     * @var float
     */
    protected $_prevSumm = NULL;



    /**
     * @param float
     */
    public function setRawSumm($summ)
    {
        $this->_rawSumm = $summ;
        return $this;
    }

    /**
     * @param float
     */
    public function setPrevSumm($summ)
    {
        $this->_prevSumm = $summ;
        return $this;
    }

    /**
     * @return float
     */
    public function getRawSumm()
    {
        if ($this->_rawSumm === NULL) {
            $this->_rawSumm = 0;
            foreach ($this->getItems() as $item) {
                $this->_rawSumm += (float) $item->price * (float) $item->qty;
            }
        }
        return $this->_rawSumm;
    }

    /**
     * @return float
     */
    public function getPrevSumm()
    {
        if ($this->_prevSumm === NULL) {
            $this->_prevSumm = $this->getRawSumm();
        }
        return $this->_prevSumm;
    }


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
        $this->_cache['total'] = NULL;
        $this->_cache['discountValue'] = NULL;
        $this->_cache['discountPercent'] = NULL;
        return $this;
    }

    /**
     * @return Model_Object_Interface
     */
    public function getOrder()
    {
        if ($this->_order === NULL) {
            throw new Model_Service_Helper_Exception('order is empty for :'.get_class($this));
        }
        return $this->_order;
    }


    /**
     * set items of rule
     * @param Model_Collection_Interface
     * @return $this
     */
    public function setItems(Model_Collection_Interface $items = NULL)
    {
        if ($items !== NULL) {
            $this->_items = $items;
        }
        $this->_cache['total'] = NULL;
        $this->_cache['discountValue'] = NULL;
        $this->_cache['discountPercent'] = NULL;
        return $this;
    }

    /**
     * get items
     * @return Model_Collection_Interface
     */
    public function getItems()
    {
        if ($this->_items === NULL) {
            throw new Model_Service_Helper_Exception('items collection is empty for :'.get_class($this));
        }
        return $this->_items;
    }

    /******************************* interface methods *************************************/


    public function calculateTotalQty()
    {
        if ($this->_cache['totalQty'] === NULL) {
            $this->_cache['totalQty'] = 0;
            foreach ($this->getItems() as $item) {
                $this->_cache['totalQty'] += $item->qty;
            }
        }
        return $this->_cache['totalQty'];
    }

    public function calculateTotal()
    {
        if ($this->_cache['total'] === NULL) {
            $this->_cache['total'] = $this->getPrevSumm() - $this->calculateDiscountValue();
        }
        return $this->_cache['total'];
    }


    public function calculateDiscountValue()
    {
        if ($this->_cache['discountValue'] === NULL) {
            $this->_cache['discountValue'] = $this->getPrevSumm() * $this->calculateDiscountPercent() / 100;
        }
        return $this->_cache['discountValue'];
    }

    public function calculateDiscountPercent()
    {
        if ($this->_cache['discountPercent'] === NULL) {
            $this->_cache['discountPercent'] = 0;
        }
        return $this->_cache['discountPercent'];
    }

    public function isVisible()
    {
        return FALSE;
    }

    public function getOutputSumm()
    {
        return $this->calculateTotal();
    }




}
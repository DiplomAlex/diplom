<?php

class Checkout_Model_Service_Observer_Cart extends App_Event_Observer
{

    public function onAddToCart()
    {
        /**
         * @var array
         */
        $catalogItemArr = $this->getEvent()->getData(0);
        /**
         * @var Checkout_Model_Object_CartItem
         */
        $cartItem = $this->_arrayToCartItem($catalogItemArr[0]);
        /**
         * add item to cart
         */
        Model_Service::factory('checkout/cart')->add($cartItem);
        $this->getEvent()->addResponse(array(TRUE));
    }

    public function onRecalculatePrice()
    {
        /**
         * @var Checkout_Model_Object_CartItem
         */
        $cartItem = $this->getEvent()->getData(0);
        /**
         * @var array
         */
        $catalogItemArr = $this->_cartItemToArray($cartItem[0]);
        /**
         * call event from catalog module to calculate price
         */
        $price = App_Event::factory('Catalog_Model_Service_Item__onRecalculatePrice', array($catalogItemArr))->dispatch()->getResponse();
        /**
         * return price
         */
        $this->getEvent()->addResponse($price[0]);
    }

    public function onRecalculatePrices()
    {
        /**
         * @var Checkout_Model_Collection_CartItem
         */
        $cartItems = $this->getEvent()->getData(0);
        /**
         * @var array
         */
        $catalogItemsArr = array();
        foreach ($cartItems[0] as $cartItem) {
            $catalogItemsArr []= $this->_cartItemToArray($cartItem);
        }
        /**
         * call event from catalog module to calculate prices
         */
        $itemPrices = App_Event::factory('Catalog_Model_Service_Item__onRecalculatePrices', array($catalogItemsArr))->dispatch()->getResponse();
        /**
         * return prices array
         */
        $this->getEvent()->addResponse($itemPrices[0]);
    }
    
    public function onGetCatalogItems()
    {
        $data = $this->getEvent()->getData(0);
        /**
         * current page to select
         * @var int
         */
        $page = $data[0];
        /**
         * rows per page
         * @var int
         */
        $rowsPerPage = $data[1];
        /**
         * search query
         * @var string
         */
        $searchQ = $data[2];
        /**
         * search criteria
         * @var string
         */
        $searchBy = $data[3];
        /**
         * sorting criteria
         * @var string
         */        
        $sortBy = $data[4];
        /**
         * sorting direction - asc/desc
         * @var string
         */
        $sortDirection = $data[5];
        /**
         * array of arrays
         */
        $itemsData = App_Event::factory('Catalog_Model_Service_Item__onGetCatalogItems', array($page, $rowsPerPage, $searchQ, $searchBy, $sortBy, $sortDirection))->dispatch()->getResponse();
        $coll = Model_Service::factory('checkout/cart')->getInjector()->getObject('Model_Collection_Interface');
        foreach ($itemsData['items'] as $itemArr) {
            $itemObj = $this->_arrayToCartItem($itemArr);
            $coll->add($itemObj);
        }
        $resp = array(
            'totalItems' => $itemsData['totalItems'],
            'totalPages' => $itemsData['totalPages'],
            'items' => $coll,
        );
        $this->getEvent()->setResponse($resp);
    } 
    
    /**
     * @param array
     * @return Model_Object_Interface
     */
    protected function _arrayToCartItem(array $arr)
    {
        $item = Model_Service::factory('checkout/cart')->createItem($arr);
        $item->price = $arr['price'];
        $item->qty = $arr['qty'];
        $item->stock_qty = $arr['stock_qty'];
        return $item;
    }

    /**
     * @param Model_Object_Interface
     * @return array
     */
    protected function _cartItemToArray(Model_Object_Interface $item)
    {
        $arr = $item->toArray();
        $arr['id'] = $arr['catalog_item_id'];
        return $arr;
    }

}
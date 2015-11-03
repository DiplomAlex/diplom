<?php

class Catalog_Model_Service_Observer_Item extends App_Event_Observer
{

    public function onRecalculatePrice()
    {
        $arr = $this->getEvent()->getData(0);
        $item = $this->_arrayToItem($arr[0]);
        $service = Model_Service::factory('catalog/item');
        $item->price = $service->get($item->id)->price;
        $price =  $service->calculatePrice($item);
        $this->getEvent()->addResponse(array($price));
    }

    public function onRecalculatePrices()
    {
        $arr = $this->getEvent()->getData(0);
        $service = Model_Service::factory('catalog/item');
        $items = $this->_arrayToCollection($arr[0]);
        $prices = array();
        foreach ($items as $item) {            
            $prices[$item->id] = $service->calculatePrice($item);
        }
        $this->getEvent()->addResponse(array($prices));
    }

    public function onAddToCart()
    {
        /** @var $itemService Catalog_Model_Service_Item */
        $itemService = Model_Service::factory('catalog/item');
        $item = $this->getEvent()->getData(0);
        $arr = $itemService->itemToArray($item[0]);
        $resp = App_Event::factory('Checkout_Model_Service_Cart__onAddToCart', array($arr))->dispatch()->getResponse();
        $this->getEvent()->addResponse(array($resp));
    }
    
    public function onItemToArray()
    {
        /** @var $itemService Catalog_Model_Service_Item */
        $itemService = Model_Service::factory('catalog/item');

        $item = $this->getEvent()->getData(0);
        $resp = $itemService->itemToArray($item[0]);
        $this->getEvent()->setResponse($resp);
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
        $sortDirection = strtoupper($data[5]);
        
        Zend_Controller_Front::getInstance()->getRequest()->setParam('filter_'.$searchBy, $searchQ);
        /** @var $service Catalog_Model_Service_Item */
        $service = Model_Service::factory('catalog/item');
        if (empty($sortBy)) {
            $sortBy = 'name';
        }
        if (empty($sortDirection)) {
            $sortDirection = 'ASC';
        }
        $service->getMapper()->getPlugin('Sorting')->setCurrentSortingMode($sortBy, $sortDirection);
        $paginator = $service->paginatorGetAllByCategory(NULL, $rowsPerPage, $page, TRUE);
        $items = array();
        foreach ($paginator as $itemObj) {
            $itemArr = $service->itemToArray($itemObj);
            $items []= $itemArr;
        }
        $resp = array(
            'totalItems' => $paginator->getTotalItemCount(),
            'totalPages' => $paginator->count(),
            'items' => $items,
        );
        $this->getEvent()->setResponse($resp);
    }
    

    /**
     * @param array
     * @return Model_Object_Interface
     */
    protected function _arrayToItem(array $arr)
    {
        $itemService = Model_Service::factory('catalog/item');
        $bundleService = Model_Service::factory('catalog/item-bundle');
        $item = $itemService->createFromArray($arr);
        if (is_array($arr['bundles'])) {
            $item->current_bundles = $bundleService->mergeAllWithCurrentsFromArray($item, $arr['bundles']);
        }
        return $item;
    }
    

    /**
     * @param array
     * @return Model_Collection_Interface
     */
    protected function _arrayToCollection(array $arr)
    {
        $itemService = Model_Service::factory('catalog/item');
        $bundleService = Model_Service::factory('catalog/item-bundle');
        $items = $itemService->createCollectionFromArray($arr);
        foreach ($arr as $itemValues) {
            if (is_array($itemValues['bundles'])) {
                $item = $items->findOneById($itemValues['id']);
                $item->current_bundles = $bundleService->mergeAllWithCurrentsFromArray($item, $itemValues['bundles']);
            }
        }
        return $items;
    }

}

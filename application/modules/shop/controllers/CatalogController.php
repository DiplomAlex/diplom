<?php
class Shop_CatalogController extends Zend_Controller_Action
{
    public function init()
    {
        App_Event::factory('Shop_Controller__init', array($this))->dispatch();
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->view->layout()->disableLayout();
        }
        $this->view->headTitle('');
    }

    public function indexAction()
    {
        $params = $this->getRequest()->getParams();
        $category = Model_Service::factory('catalog/category')->getComplexBySeoId($params['seo_id']);
        $itemService = Model_Service::factory('catalog/item');

        $items = $itemService->getAllActiveByCategory($category->id, 1, 56,true);
        foreach ($items as $item){
            $galleries[$item->id] = $itemService->getHelper('Gallery')->getGalleryWithActiveImage($item['id']);
        }
        if(strlen($category->html_title)>0){
            $this->view->headTitle($category->html_title);
        } else {
            $this->view->headTitle('Каталог');
        }

        $this->view->items = $items;
        $this->view->items_count = $itemService->getTotalCountByCategoryt($category->id);
        $this->view->category = $category;
        $this->view->galleries = $galleries;
        $this->view->headdescription = $category->meta_description;
        $this->view->headkeywords = $category->meta_keywords;
    }

    public function itemAction()
    {
        $params = $this->getRequest()->getParams();
        /** @var $itemService Catalog_Model_Service_Item */
        $itemService = Model_Service::factory('catalog/item');
        /** @var $remainService Api_Model_Service_Remain */
        $remainService = Model_Service::factory('api/remain');

        $item = $itemService->getComplexBySeoId($params['seo_id']);
        $this->view->remains = $remainService->getRemainsBySeoId($item->sku);

        $this->view->item = $item;
        $this->view->gallery = $itemService->getHelper('Gallery')
            ->getGalleryWithActiveImage($item['id']);

        if (strlen($item->html_title) > 0) {
            $this->view->headTitle($item->html_title);
        } else {
            $this->view->headTitle('Каталог');
        }

        $this->view->headdescription = $item->meta_description;
        $this->view->headkeywords = $item->meta_keywords;

        $item = $itemService->getEditFormValues($item['id']);

        $this->view->other = $itemService->getComplexByCategories(
            $item['item_categories'], 10
        );

        $galleries = array();
        foreach ($this->view->other as $item) {
            $galleries[$item->id] = $itemService->getHelper('Gallery')
                ->getGalleryWithActiveImage($item['id']);
        }
        $this->view->galleries = $galleries;

        if ($params['category']) {
            $category = Model_Service::factory('catalog/category')
                ->getComplexBySeoId($params['category']);
            $this->view->category = $category;
        }
    }

    public function exclusiveAction()
    {
        $params = $this->getRequest()->getParams();
        /** @var $service Catalog_Model_Service_Item */
        $service = Model_Service::factory('catalog/item');
        /** @var $remainService Api_Model_Service_Remain */
        $remainService = Model_Service::factory('api/remain');

        $item = $service->getComplexBySeoId($params['seo_id']);
        $this->view->remains = $remainService->getRemainsBySeoId($item->sku);

        if (strlen($item->html_title) > 0) {
            $this->view->headTitle($item->html_title);
        } else {
            $this->view->headTitle('Эксклюзивные вещи');
        }
        $this->view->headdescription = $item->meta_description;
        $this->view->headkeywords = $item->meta_keywords;

        $this->view->item = $item;
        $this->view->gallery = $service->getHelper('Gallery')
            ->getGalleryWithActiveImage($item['id']);

        $item = $service->getEditFormValues($item['id']);

        $this->view->other = $service->getComplexByCategories(
            $item['item_categories'], 10
        );

        foreach ($this->view->other as $item) {
            $galleries[$item->id] = $service->getHelper('Gallery')
                ->getGalleryWithActiveImage($item['id']);
        }
        $this->view->galleries = $galleries;
    }

    public function ajaxGetNextItemsAction()
    {
        $params = $this->getRequest()->getParams();
        $this->_helper->json(
            array(
                'success' => true,
                'items'   => $this->view->box_Items(
                    $params['page'], $params['param'], $params['page_type']
                ),
            )
        );
    }
}
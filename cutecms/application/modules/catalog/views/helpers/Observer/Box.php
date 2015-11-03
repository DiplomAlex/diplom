<?php

class Catalog_View_Helper_Observer_Box extends View_Helper_Observer_Abstract
{

    public function popularItems()
    {
        $html = $this->view->box_PopularItems();
        $this->getEvent()->addResponse($html);
    }

    public function newItems()
    {
        $html = $this->view->box_NewItems();
        $this->getEvent()->addResponse($html);
    }

    public function searchItems()
    {
        $html = $this->view->box_SearchItems();
        $this->getEvent()->addResponse($html);
    }

}
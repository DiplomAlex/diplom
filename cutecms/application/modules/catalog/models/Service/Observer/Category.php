<?php

class Catalog_Model_Service_Observer_Category extends App_Event_Observer
{

    public function getMenuList()
    {
        $cats = Model_Service::factory('catalog/category')->getAllByParent(NULL);
        $list = array();
        foreach ($cats as $cat) {
            $list []= array(
                'label' => $cat->name,
                'level' => $cat->tree_level,
                'route' => 'category',
                'params' => array(
                    'seo_id' => $cat->seo_id,
                ),
            );
        }
        $this->getEvent()->addResponse($list);
    }

}
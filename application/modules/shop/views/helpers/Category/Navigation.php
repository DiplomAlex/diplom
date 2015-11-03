<?php

class Shop_View_Helper_Category_Navigation extends Zend_View_Helper_Abstract
{
    
    public function category_Navigation(Model_Object_Interface $category = NULL)
    {
        $service = Model_Service::factory('catalog/category');
        if ($category) {
            $parents = $service->getParentsOf($category->id, TRUE);
        }   
        else {
            $parents = array();
        }     
        $parentIds = array();
        $parentTreeIds = array();
        foreach ($parents as $parent) {
            $parentIds[]=$parent->id;
            $parentTreeIds[]=$parent->tree_id;
        }
        $allCategories = $service->getFullTreeSortedByLevel(TRUE);
        $level1 = array();
        foreach ($allCategories as $key=>$cat) {
            if ( (int) $cat['tree_level'] == 1) {
                $level1[$cat->tree_id]=$cat;
            }
        }
        
        $html = $this->view->partial('catalog/category/navigation.phtml', array(
            'allCategories' => $allCategories,
            'topLevel' => $level1,
            'currentCategory' => $category,
            'parentIds' => $parentIds,
            'parentTreeIds' => $parentTreeIds,
        ));
        return $html;
    }
    
}
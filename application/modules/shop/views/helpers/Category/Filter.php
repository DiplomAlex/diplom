<?php

class Shop_View_Helper_Category_Filter extends Zend_View_Helper_Abstract
{
    
    public function category_Filter(Model_Object_Interface $category = null)
    {
        $formParams = array();
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();

        if ($category->filter_id) {
            $attributeGroup = Model_Service::factory('catalog/attribute')->getAllByGroup($category->filter_id);
            $formParams = array('attributes' => $attributeGroup);
        }

        $filterForm = new Shop_Form_Catalog_Filter($formParams);
        $filterForm->populate($params);

        return $this->view->partial('catalog/filter.phtml', array(
            'category' => $category,
            'filterForm' => $filterForm
        ));
    }
    
}
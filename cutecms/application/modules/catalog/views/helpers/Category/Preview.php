<?php

class Catalog_View_Helper_Category_Preview extends Zend_View_Helper_Abstract
{

    public function category_Preview(Model_Object_Interface $item)
    {
        return $this->view->html_Img($item['rc_id_preview'], 'width="134" height="134" border="0" class="thumb"');
    }

}
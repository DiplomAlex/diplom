<?php

class Catalog_View_Helper_Item_Preview extends Zend_View_Helper_Abstract
{

    public function item_Preview(Model_Object_Interface $item)
    {
        return $this->view->html_Img($item['rc_id_preview'], 'width="116" height="75" border="0" ');
    }

}
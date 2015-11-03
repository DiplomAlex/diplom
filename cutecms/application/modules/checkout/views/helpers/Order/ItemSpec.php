<?php

class Checkout_View_Helper_Order_ItemSpec extends Zend_View_Helper_Abstract
{
    
    public function order_ItemSpec($item)
    {
        $bundles = $item['bundles_html'];
        if ( ! empty($bundles)) {
            $bundles = '<br/>'.$bundles;
        }
        $html = $item['attributes_html'].$bundles;
        return $html;
    }
    
}
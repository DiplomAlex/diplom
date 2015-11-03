<?php

class Checkout_View_Helper_Cart_ItemSpec extends Zend_View_Helper_Abstract
{
    
    public function cart_ItemSpec($item)
    {
        $bundles = $item['bundles_html'];
        if ( ! empty($bundles)) {
            $bundles = '<br/>'.$bundles;
        }
        $html = $item['attributes_html'].$bundles;
        return $html;
    }
    
}
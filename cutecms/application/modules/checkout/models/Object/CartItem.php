<?php

class Checkout_Model_Object_CartItem extends Model_Object_Abstract
{

    public function init()
    {

        $this->addElements(array(
            'id', 'seo_id', 'hash',
            'sku', 'code', 'unit', 'name', 'brief', 'full',
            'date_added', 'date_changed',
            'rc_id', 'rc_id_filename', 'rc_id_preview', 'rc_id_preview2', 'rc_id_preview3', 'rc_id_preview4',
            'rc_id_preview5', 'rc_id_preview6', 'rc_id_preview7', 'rc_id_preview8', 'rc_id_preview9', 'rc_id_preview10',
            'param1', 'param2', 'param3',
            'attributes', 'attributes_html', 'attributes_text',
            'bundles', 'bundles_html', 'bundles_text',             
            'price', 'qty', 'stock_qty',
            'catalog_item_id',

            /* From remains */
            'size',
            'weight',
            'characteristics',
            'probe',
            'material',
            'remain_price'
        ));

    }


    public function getHash()
    {
        if ( ! isset($this->_elements['hash'])) {
            $this->_elements['hash'] = uniqid('CartItem');
        }
        return $this->_elements['hash'];
    }

}
<?php

class Shop_View_Helper_Item_ConfiguratorJs extends Zend_View_Helper_Abstract
{
    
    protected $_script = 'catalog/item/configurator.js.phtml';
    
    public function item_ConfiguratorJs(Model_Object_Interface $item, $addToHeadSript = TRUE)
    {        
        $js = $this->view->partial($this->_script, array('item'=>$item));
        $boxJs = $this->view->jquery_Alert('alert', $this->view->translate('Товар добавлен в корзину'), $addToHeadSript);
        $errorJs = $this->view->jquery_Alert('alertError', $this->view->translate('Невозможно добавить товар в корзину - выбраны не все обязательные опции'), $addToHeadSript);
        if ($addToHeadSript) {
            $this->view->headScript('SCRIPT', $js);            
        }
        else {
            $js .= $boxJs . $errorJs;
        }
        return $js;
    }
    
}
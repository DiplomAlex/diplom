<?php

class Checkout_Form_AdminOrderItemSelect extends App_Form
{
    
    public function init()
    {
        $items = $this ->createElement('flexiGrid', 'items_list')
                       ->setRequired(FALSE)
                       ->setAttribs(array(
                           'width' => 452,
                           'height' => 325,
                           'colModel' => $this->_prepareItemsColModel(),
                           'url' => $this->_prepareItemsUrlGet(),
                           'buttons' => array(),
                           'buttonsJs' => '',
                           'searchItems' => array(
                               array('display' => $this->getTranslator()->_('Артикул'), 'name' => 'sku'),
                               array('display' => $this->getTranslator()->_('Название'), 'name' => 'name', 'isdefault' => TRUE),
                           ),
                           'usepager' => TRUE,
                           'rp' => 10,
                       ))
                       ;
        $this->addElement($items);
        
    }
    


    protected function _prepareItemsColModel()
    {
        return
            array(
                array('name' =>'name', 'display'=>$this->getTranslator()->_('Название'),
                      'width'=>'300', 'sortable'=>TRUE, 'align'=>'left'),
                array('name' =>'price', 'display'=>$this->getTranslator()->_('Цена'),
                      'width'=>'50', 'sortable'=>TRUE, 'align'=>'right'),
                array('name' =>'sku', 'display'=>$this->getTranslator()->_('Артикул'),
                      'width'=>'50', 'sortable'=>FALSE, 'align'=>'left'),                
                array('name' =>'attributes_text', 'display'=>$this->getTranslator()->_('Атрибуты'),
                      'width'=>'100', 'sortable'=>TRUE, 'align'=>'left', 'hide' => TRUE,),                
            );
    }

    protected function _prepareItemsUrlGet()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-get-catalog-items', 'admin-order', 'checkout');
    }
    
    
    
}


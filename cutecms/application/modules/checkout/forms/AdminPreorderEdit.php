<?php

class Checkout_Form_AdminPreorderEdit extends App_Form
{

    public function init()
    {

        $this->setMethod('POST');


        $this->addElement('text', 'id', array(
            'label'=>$this->getTranslator()->_('Номер'),
            'attribs'=>array(
                'readonly'=>TRUE,
            ),
            'required' => TRUE,
        ));

        $this->addElement('text', 'date_added', array(
            'label'=>$this->getTranslator()->_('Дата оформления'),
            'attribs'=>array(
                'readonly'=>TRUE,
            ),
            'required' => TRUE,
        ));

        $this->addElement('text', 'client_name', array(
            'label'=>$this->getTranslator()->_('Клиент'),
            'attribs'=>array(
                'readonly'=>TRUE,
            ),
            'required' => TRUE,
        ));


        $items = $this ->createElement('flexiGrid', 'items')
                       ->setLabel($this->getTranslator()->_('Содержимое заказа'))
                       ->setRequired(FALSE)
                       ->setAttribs(array(
                            'width' => 452,
                            'height' => 250,
                            'colModel' => $this->_prepareItemsColModel(),
                            'url' => $this->_prepareItemsUrlGet(),
                            'buttons' => array(
                                array('name'=>$this->getTranslator()->_('Edit'), 'bclass'=>'edit', 'onpress'=>'flexigridItemsEdit'),
                                array('name'=>$this->getTranslator()->_('New'), 'bclass'=>'add', 'onpress'=>'flexigridItemsNew'),
                                array('name'=>$this->getTranslator()->_('Delete'), 'bclass'=>'delete', 'onpress'=>'flexigridItemsDelete'),
                            ),
                            'buttonsJs' => $this->_prepareItemsButtonsJs(),
                       ))
                       ;
        $this->addElement($items);


        /*
        $this->addElement('text', 'total', array(
            'label'=>$this->getTranslator()->_('Сумма к оплате'),
            'attribs'=>array(
                'readonly'=>TRUE,
            ),
            'required' => TRUE,
        ));
        */


        $brules = $this->createElement('flexiGrid', 'brules')
                       ->setLabel($this->getTranslator()->_('Итоги'))
                       ->setRequired(FALSE)
                       ->setAttribs(array(
                            'width' => 452,
                            'height' => 200,
                            'colModel' => $this->_prepareBrulesColModel(),
                            'url' => $this->_prepareBrulesUrlGet(),
                            'buttons' => array(),
                       ))
                       ;
        $this->addElement($brules);


        $this->addElement('submitLink', 'save', array(
            'label' => $this->getTranslator()->_('Save'),
        ));
        $this->addElement('submitLink', 'cancel', array(
            'label' => $this->getTranslator()->_('Cancel'),
        ));

    }


    protected function _prepareItemsColModel()
    {
        return
            array(
                array('name' =>'code', 'display'=>$this->getTranslator()->_('#'),
                      'width'=>'50', 'sortable'=>TRUE, 'align'=>'center'),
                array('name' =>'name', 'display'=>$this->getTranslator()->_('Название'),
                      'width'=>'100', 'sortable'=>TRUE, 'align'=>'left'),
                array('name' =>'current_value', 'display'=>$this->getTranslator()->_('Параметры'),
                      'width'=>'50', 'sortable'=>TRUE, 'align'=>'center'),
                array('name' =>'current_value', 'display'=>$this->getTranslator()->_('Цена за ед.'),
                      'width'=>'50', 'sortable'=>TRUE, 'align'=>'center'),
                array('name' =>'current_value', 'display'=>$this->getTranslator()->_('Кол-во'),
                      'width'=>'50', 'sortable'=>TRUE, 'align'=>'center'),
                array('name' =>'current_value', 'display'=>$this->getTranslator()->_('Стоимость'),
                      'width'=>'50', 'sortable'=>TRUE, 'align'=>'center'),
            );
    }

    protected function _prepareItemsUrlGet()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-get-item', 'admin-preorder', 'checkout');
    }

    protected function _prepareItemsButtonsJs()
    {
        $js = $this->getView()->partial('admin-order/items-js.phtml', array(
            'id'=>'items',
            'urlGet'=>$this->_prepareItemsUrlGet(),
            'urlNew'=>$this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-new-item', 'admin-preorder', 'checkout'),
            'urlEdit'=>$this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-edit-item', 'admin-preorder', 'checkout'),
            'urlDelete'=>$this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-delete-item', 'admin-preorder', 'checkout'),
            'formNew'=> new Checkout_Form_AdminOrderItemEdit,
            'formEdit'=> new Checkout_Form_AdminOrderItemEdit,
            /*'formDelete'=> new Checkout_Form_AdminOrderItemDelete,*/
        ));
        return $js;
    }



    protected function _prepareBrulesColModel()
    {
        return
            array(
                array('name' =>'name', 'display'=>'Заголовок',
                      'width'=>'250', 'sortable'=>TRUE, 'align'=>'left'),
                array('name' =>'param1', 'display'=>$this->getTranslator()->_('Скидка'),
                      'width'=>'50', 'sortable'=>TRUE, 'align'=>'right'),
                array('name' =>'param2', 'display'=>$this->getTranslator()->_('Сумма'),
                      'width'=>'50', 'sortable'=>TRUE, 'align'=>'right'),
            );
    }



    protected function _prepareBrulesUrlGet()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-get-brule', 'admin-preorder', 'checkout');
    }


}

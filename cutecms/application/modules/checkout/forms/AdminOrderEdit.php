<?php

class Checkout_Form_AdminOrderEdit extends App_Form
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

        $selectStatus = new Zend_Form_Element_Select('status');
        $this->addElement( $selectStatus
                        -> setLabel($this->getTranslator()->_('Status'))
                        -> setRequired(TRUE)
                        -> addMultiOptions($this->_prepareStatuses())
                    );

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
            'required' => FALSE,
        ));
        
        $this->addElement('textarea', 'client_requisites_spec', array(
            'label'=>$this->getTranslator()->_('Реквизиты клиента'),
            'attribs'=>array(
                'readonly'=>TRUE,
            ),
            'required' => FALSE,
        ));

        $this->addElement('textarea', 'client_comment', array(
            'label'=>$this->getTranslator()->_('Комментарий клиента'),
            'attribs'=>array(
                'readonly'=>TRUE,
            ),
            'required' => FALSE,
        ));

        $items = $this ->createElement('flexiGrid', 'items')
                       ->setLabel($this->getTranslator()->_('Содержимое заказа'))
                       ->setRequired(FALSE)
                       ->setAttribs(array(
                            'width' => 725,
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
                            'width' => 725,
                            'height' => 200,
                            'colModel' => $this->_prepareBrulesColModel(),
                            'url' => $this->_prepareBrulesUrlGet(),
                            'buttons' => array(),
                       ))
                       ;
        $this->addElement($brules);
        
        $this->addElement('checkbox', 'send_mail_to_client', array(
            'label' => $this->getTranslator()->_('Известить клиента об изменении заказа'),
        ));


        $this->addElement('submitLink', 'save', array(
            'label' => $this->getTranslator()->_('Save'),
            'class' => 'btn-primary',
        ));
        $this->addElement('submitLink', 'cancel', array(
            'label' => $this->getTranslator()->_('Cancel'),
        ));

    }


    protected function _prepareStatuses()
    {
        $list = array();
        foreach (Model_Service::factory('checkout/order')->getStatusesList() as $status => $value) {
            $list[$value] = $this->getTranslator()->_('orderStatus.'.$status);
        }
        return $list;
    }


    protected function _prepareItemsColModel()
    {
        return
            array(
                array('name'    => 'code',
                      'display' => $this->getTranslator()->_('#'),
                      'width'   => '70', 'sortable' => true,
                      'align'   => 'center'),
                array('name'    => 'name',
                      'display' => $this->getTranslator()->_('Название'),
                      'width'   => '100', 'sortable' => true,
                      'align'   => 'left'),
                array('name'    => 'current_value',
                      'display' => $this->getTranslator()->_('Параметры'),
                      'width'   => '70', 'sortable' => true,
                      'align'   => 'center'),
                array('name'    => 'current_value',
                      'display' => $this->getTranslator()->_('Штрих-код'),
                      'width'   => '70', 'sortable' => true,
                      'align'   => 'center'),
                array('name'    => 'current_value',
                      'display' => $this->getTranslator()->_('Цена за ед.'),
                      'width'   => '70', 'sortable' => true,
                      'align'   => 'center'),
                array('name'    => 'current_value',
                      'display' => $this->getTranslator()->_('Кол-во'),
                      'width'   => '70', 'sortable' => true,
                      'align'   => 'center'),
                array('name'    => 'current_value',
                      'display' => $this->getTranslator()->_('Стоимость'),
                      'width'   => '70', 'sortable' => true,
                      'align'   => 'center'),
                array('name'    => 'current_value',
                      'display' => $this->getTranslator()->_('Материал'),
                      'width'   => '70', 'sortable' => true,
                      'align'   => 'center'),
                array('name'    => 'current_value',
                      'display' => $this->getTranslator()->_('Проба'),
                      'width'   => '70', 'sortable' => true,
                      'align'   => 'center'),
                array('name'    => 'current_value',
                      'display' => $this->getTranslator()->_('Размер'),
                      'width'   => '70', 'sortable' => true,
                      'align'   => 'center'),
            );
    }

    protected function _prepareItemsUrlGet()
    {
        return $this->getView()->stdUrl(array('reset' => TRUE), 'ajax-get-item', 'admin-order', 'checkout');
    }

    protected function _prepareItemsButtonsJs()
    {
        $js = $this->getView()->partial('admin-order/items-js.phtml', array(
            'id'=>'items',
            'urlGet'=>$this->_prepareItemsUrlGet(),
            /*'urlNew'=>$this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-new-item', 'admin-order', 'checkout'),*/
        	'urlNew'=>$this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-edit-item', 'admin-order', 'checkout'),
            'urlEdit'=>$this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-edit-item', 'admin-order', 'checkout'),
            'urlDelete'=>$this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-delete-item', 'admin-order', 'checkout'),
            'formNew'=> new Checkout_Form_AdminOrderItemEdit,
            'formEdit'=> new Checkout_Form_AdminOrderItemEdit,
        	'formSelect'=> new Checkout_Form_AdminOrderItemSelect,
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
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-get-brule', 'admin-order', 'checkout');
    }


}

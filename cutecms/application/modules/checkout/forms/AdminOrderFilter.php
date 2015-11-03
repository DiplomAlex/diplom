<?php

class Checkout_Form_AdminOrderFilter extends App_Form
{

    public function init()
    {

        $this->setMethod('GET');

        $this->addElement('text', 'filter_number', array(
            'label' => $this->getTranslator()->_('Номер'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 20,
                'class' => 'input',
            ),
            'validators' => array(
                array('StringLength', false, array(3,200))
            ),
            'filters' => array('StringTrim'),
        ));


        $selectStatus = new Zend_Form_Element_Select('filter_status');
        $this->addElement( $selectStatus
                        -> setLabel($this->getTranslator()->_('Status'))
                        -> setAttrib('class', 'select')
                        -> addMultiOptions($this->_prepareStatuses())
                    );

        $this->addElement('text', 'filter_client', array(
            'label' => $this->getTranslator()->_('Client'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 20,
                'class' => 'input',
            ),
            'validators' => array(
                array('StringLength', false, array(3,200))
            ),
            'filters' => array('StringTrim'),
        ));

        $this->addElement('submitLink', 'filter', array(
            'label' => $this->getTranslator()->_('Filter'),
        ));


        $urlParams = array();

        foreach ($this->getElements() as $el) {
            $el->setDecorators(array('ViewHelper'));
            $urlParams[$el->getName()] = NULL;
        }

        $this->setAction($this->getView()->url($urlParams));
    }

    /**
     * get statuses list
     * @return array
     */
    protected function _prepareStatuses()
    {
        $list = array(''=>$this->getTranslator()->_('All'));
        foreach (Model_Service::factory('checkout/order')->getStatusesList() as $status => $value) {
            $list[$value] = $this->getTranslator()->_('orderStatus.'.$status);
        }
        return $list;
    }

}
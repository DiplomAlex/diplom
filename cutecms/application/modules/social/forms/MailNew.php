<?php

class Social_Form_MailNew extends App_Form
{

    public function init()
    {
        $this->setMethod('POST');
        $this->setAction($this->getView()->url(array(), 'social_mail_new'));

/*
        $select = new Zend_Form_Element_Select('status');
        $this->addElement( $select
                        -> setLabel($this->getTranslator()->_('Status'))
                        -> setRequired(TRUE)
                        -> addMultiOptions($this->_prepareStatuses())
                    );
*/

        $this->addElement('hidden', 'recipient_id');
        $this->addElement('text', 'recipient_name', array(
            'label' => $this->getTranslator()->_('Recipient'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 90,
                'class' => 'input',
            ),
            'validators' => array(
                array('StringLength', false, array(3,200)),
            ),
            'required' => TRUE
        ));
        $this->addElement('hidden', 'parent_id');
        $this->addElement('hidden', 'talking_id');



        $this->addElement('text', 'subject', array(
            'label' => $this->getTranslator()->_('Subject'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 50,
                'class' => 'input',
            ),
            'validators' => array(
                array('StringLength', false, array(3,200)),
            ),
            'required' => TRUE
        ));

        $this->addElement('textarea', 'body', array(
            'label' => $this->getTranslator()->_('Message'),
            'attribs' => array(
                'maxlength' => 1000,
                'cols' => 50,
                'rows' => 4,
                'class' => 'textarea',
            ),
            'validators' => array(
                array('StringLength', false, array(3,1000))
            ),
            'filters' => array('StringTrim'),
            'required' => TRUE
        ));

        $this->addElement('submitLink', 'send', array(
            'label' => $this->getTranslator()->_('Send'),
            'attribs' => array(
                'noAjaxSubmit' => TRUE,
            ),
        ));
        $this->addElement('submitLink', 'cancel', array(
            'label' => $this->getTranslator()->_('Cancel'),
            'attribs' => array(
                'noAjaxSubmit' => TRUE,
            ),
        ));


    }


    public function populate(array $values)
    {
        if (isset($values['recipient_id'])) {
            /*$this->recipient_name->setAttrib('disabled', 'disabled');*/
            $this->removeElement('recipient_name');
        }
        return parent::populate($values);
    }

    protected function _prepareStatuses()
    {
        $list = array();
        foreach (Model_Service::factory('social/mail')->getStatusesList() as $status => $value) {
            $list[$value] = $this->getTranslator()->_($status);
        }
        return $list;
    }

}
<?php

class Form_UserRegister extends App_Form
{

    public function init()
    {

        $this->setMethod('POST');

        $this->addElement('hidden', 'id');
        
        $this->addElement('hidden', 'redirect');

        $this->addElement('text', 'login', array(
            'label' => $this->getTranslator()->_('Login'),
            'attribs' => array(
                'maxlength' => 50,
                'size' => 30
            ),
            'validators' => array(
                array('StringLength', FALSE, array(3,50)),
                array('Alnum'),
                array('NotInArray', FALSE, array(Model_Service::factory('user')->getAllLogins()))
            ),
            'required' => TRUE
        ));


        $this->addElement('password', 'password', array(
            'label' => $this->getTranslator()->_('Пароль'),
            'attribs' => array(
                'maxlength' => 50,
                'size' => 30
            ),
            'validators' => array(
                array('StringLength', FALSE, array(3,20)),
                array('PasswordRepeat', FALSE, array('password2')),
            ),
            'filters' => array('StringTrim'),
            'required' => TRUE
        ));

        $this->addElement('password', 'password2', array(
            'label' => $this->getTranslator()->_('Повторите пароль'),
            'attribs' => array(
                'maxlength' => 50,
                'size' => 30
            ),
            'filters' => array('StringTrim'),
            'required' => TRUE
        ));


        $this->addElement('text', 'email', array(
            'label' => $this->getTranslator()->_('E-mail'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 30
            ),
            'validators' => array(
                array('StringLength', false, array(1,200)),
                array('EmailAddress'),
                array('NotInArray', FALSE, array(Model_Service::factory('user')->getAllEmails()))
            ),
            'required' => TRUE
        ));


        $this->addElement('text', 'name', array(
            'label' => $this->getTranslator()->_('Name'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 30
            ),
            'validators' => array(
                array('StringLength', false, array(1,200))
            ),
            'filters' => array('StringTrim'),
            'required' => TRUE
        ));

        /*
        $this->addElement('text', 'dob', array(
            'label' => $this->getTranslator()->_('Date of birth'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 45,
                'readonly' => TRUE,
            ),
            'validators' => array(
                array('StringLength', false, array(1,200)),
            ),
            'required' => TRUE
        ));
        */

        /*
        $this->addElement('text', 'phone', array(
            'label' => $this->getTranslator()->_('Tel.'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 45,
            ),
            'validators' => array(
                array('StringLength', false, array(1,200)),
            ),
            'required' => FALSE
        ));
        */


/*
        $this->addElement('resource', 'resource_rc_id', array(
            'label' => $this->getTranslator()->_('Image'),
            'required' => FALSE
        ));


        $this->addElement('text', 'skype', array(
            'label' => $this->getTranslator()->_('Skype'),
            'attribs' => array(
                'maxlength' => 100,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(3,100)),
            ),
        ));


        $this->addElement('text', 'firm_short', array(
            'label' => $this->getTranslator()->_('Firm short'),
            'attribs' => array(
                'maxlength' => 500,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(3,500)),
            ),
        ));



        $this->addElement('text', 'firm_long', array(
            'label' => $this->getTranslator()->_('Firm long'),
            'attribs' => array(
                'maxlength' => 500,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(3,500)),
            ),
        ));


        $this->addElement('text', 'address_phis', array(
            'label' => $this->getTranslator()->_('Address de-facto'),
            'attribs' => array(
                'maxlength' => 500,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(1,500)),
            ),
        ));



        $this->addElement('text', 'address_jur', array(
            'label' => $this->getTranslator()->_('Address de-jure'),
            'attribs' => array(
                'maxlength' => 500,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(1,500)),
            ),
        ));


        $this->addElement('text', 'inn', array(
            'label' => $this->getTranslator()->_('INN'),
            'attribs' => array(
                'maxlength' => 100,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(3,100)),
            ),
        ));


        $this->addElement('text', 'kpp', array(
            'label' => $this->getTranslator()->_('KPP'),
            'attribs' => array(
                'maxlength' => 100,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(3,100)),
            ),
        ));


        $this->addElement('text', 'rs', array(
            'label' => $this->getTranslator()->_('RS'),
            'attribs' => array(
                'maxlength' => 100,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(3,100)),
            ),
        ));


        $this->addElement('text', 'ks', array(
            'label' => $this->getTranslator()->_('KS'),
            'attribs' => array(
                'maxlength' => 100,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(3,100)),
            ),
        ));



        $this->addElement('text', 'bank', array(
            'label' => $this->getTranslator()->_('Bank'),
            'attribs' => array(
                'maxlength' => 100,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(3,100)),
            ),
        ));

        $this->addElement('text', 'phone', array(
            'label' => $this->getTranslator()->_('Tel.'),
            'attribs' => array(
                'maxlength' => 100,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(1,100)),
            ),
        ));

        $this->addElement('text', 'fax', array(
            'label' => $this->getTranslator()->_('Fax'),
            'attribs' => array(
                'maxlength' => 100,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(1,100)),
            ),
        ));
*/

        $this->addElement('submitLink', 'save', array(
            'label' => $this->getTranslator()->_('Save'),
        ));
        $this->addElement('submitLink', 'cancel', array(
            'label' => $this->getTranslator()->_('Cancel'),
        ));


    }


}
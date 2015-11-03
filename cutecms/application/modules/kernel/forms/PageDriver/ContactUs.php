<?php

class Form_PageDriver_ContactUs extends App_Form
{
    
    public function init()
    {        
		$this->setMethod('POST');
        $this->setAttrib('id', 'contact_form');
        
        $this->addElement('text', 'name', array(
            'label' => 'ВАШЕ ИМЯ',
            'validators' => array(
                array('StringLength', FALSE, array(1,200)),
            ),
            'attribs' => array(
                        'size' => '40',
                    ),
            'required' => TRUE,
        ));
        
        $this->addElement('text', 'email', array(
            'label' => 'ЭЛ. ПОЧТА',
            'validators' => array(
                array('StringLength', FALSE, array(1,200)),
                array('EmailAddress'),
            ),
            'attribs' => array(
                        'size' => '40',
                    ),
            'required' => TRUE,
        ));

        $this->addElement('textarea', 'text', array(
            'label' => 'СООБЩЕНИЕ',
            'validators' => array(
                array('StringLength', FALSE, array(1,5000)),
            ),
            'attribs' => array(
                        'cols' => '40',
                        'rows' => '5',
                    ),
            'required' => TRUE,
        ));

        $captcha = new App_Form_Element_Captcha(
               'captcha',
                array(
                    'label' => 'Введите код на картинке',
                    'attribs' => array(
                        'size' => '8',
                        'maxlen' => '8',
                        'tabIndex' => '10'
                    ),
                    'required' => TRUE,
                )
            );
        $this->addElement($captcha);
        
        $this->addElement('submit', 'send', array(
            'label' => 'ОТПРАВИТЬ',
        ));        
    }
    
}
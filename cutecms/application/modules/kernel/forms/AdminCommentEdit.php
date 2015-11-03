<?php

class Form_AdminCommentEdit extends App_Form
{

    public function init()
    {

        $this->setMethod('POST');

        
        $this->addElement('hidden', 'id');
        $this->addElement('hidden', 'parent_id');
        $this->addElement('hidden', 'content_type');
        $this->addElement('hidden', 'content_id');
        
        $selectStatus = new Zend_Form_Element_Select('status');
        $this->addElement( $selectStatus
                        -> setLabel($this->getTranslator()->_('Status'))
                        -> setRequired(TRUE)
                        -> addMultiOptions($this->_prepareStatuses())
                    );



        $this->addElement('text', 'date_added_text', array(
            'label' => $this->getTranslator()->_('Добавлен'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 45,
                'readonly' => TRUE,
            ),
        ));

        $this->addElement('text', 'author_text', array(
            'label' => $this->getTranslator()->_('Автор'),
            'attribs' => array(
                'maxlength' => 500,
                'size' => 45,
                'readonly' => TRUE,
            ),
        ));
        
        
        $this->addElement('text', 'subject', array(
            'label' => $this->getTranslator()->_('Заголовок'),
            'attribs' => array(
                'maxlength' => 500,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(1,500)),
            ),
            'required' => FALSE
        ));
        


        $this->addElement('resource', 'resource_rc_id', array(
            'label' => $this->getTranslator()->_('Картинка'),
            'required' => FALSE
        ));

        
        
        $this->addElement('fckeditor', 'text', array(
            'label' => $this->getTranslator()->_('Текст'),
            'attribs' => array(
                'width' => 550,
                'height_koef' => 2,
            ),
            'required' => TRUE
        ));
        

        $this->addElement('submitLink', 'save', array(
            'label' => $this->getTranslator()->_('Save'),
        ));
        $this->addElement('submitLink', 'cancel', array(
            'label' => $this->getTranslator()->_('Cancel'),
        ));

    }



    protected function _prepareStatuses()
    {
        $list = array();
        foreach (Model_Service::factory('comment')->getStatusesList() as $status => $value) {
            $list[$value] = $this->getTranslator()->_($status);
        }
        return $list;
    }

}

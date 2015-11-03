<?php

class Form_AdminRoleEdit extends App_Form
{

    public function init()
    {

        $this->setMethod('POST');

        $selectStatus = new Zend_Form_Element_Select('status');
        $this->addElement( $selectStatus
                        -> setLabel($this->getTranslator()->_('Status'))
                       // -> setRequired(TRUE)
                        -> addMultiOptions($this->_prepareStatuses())
                    );


        $this->addElement('resource', 'resource_rc_id', array(
            'label' => $this->getTranslator()->_('Image'),
            'required' => FALSE,
            'validators' => array(
				array('Extension', false, Zend_Registry::get('config')->imageValidExtension->toArray())
			)
        ));


        $selectStatus = new Zend_Form_Element_Select('acl_role');
        $this->addElement( $selectStatus
                        -> setLabel($this->getTranslator()->_('Acl Role'))
                        -> setRequired(TRUE)
                        -> addMultiOptions($this->_prepareAclRole())
                    );
                    
                    
        $selectStatus = new Zend_Form_Element_Select('param1');
        $this->addElement( $selectStatus
                        -> setLabel($this->getTranslator()->_('Сайт'))
                        -> setRequired(FALSE)
                        -> addMultiOptions($this->_prepareParam1List())
                    );
                    

        foreach (Model_Service::factory('language')->getAllActive() as $lang) {

            $this->addElement('text', 'description_language_'.$lang->id.'_name', array(
                'label' => $this->getTranslator()->_('Name').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 200,
                    'size' => 90
                ),
                'validators' => array(
                    array('StringLength', false, array(3,200))
                ),
                'filters' => array('StringTrim'),
                'required' => TRUE
            ));

            $this->addElement('textarea', 'description_language_'.$lang->id.'_brief', array(
                'label' => $this->getTranslator()->_('Brief').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 1000,
                    'cols' => 90,
                    'rows' => 4,
                ),
                'validators' => array(
                    array('StringLength', false, array(3,1000))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));

        }


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
        foreach (Model_Service::factory('role')->getStatusesList() as $status => $value) {
            $list[$value] = $this->getTranslator()->_($status);
        }
        return $list;
    }

    protected function _prepareAclRole()
    {
        $list = array();
        foreach (Model_Service::factory('role')->getAclRolesList() as $id => $code) {
            $list[$code] = $this->getTranslator()->_($code);
        }
        return $list;
    }
    
    protected function _prepareParam1List()
    {
        $list = array('' => ' -- ');
        $sites = Model_Service::factory('site')->getAll();
        foreach ($sites as $site) {
            $list[$site->id] = $site->specification;
        }
        return $list;        
    }

}

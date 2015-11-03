<?php

class Form_AdminUserFilter extends App_Form
{

    public function init()
    {

        $this->setMethod('GET');

        $selectStatus = new Zend_Form_Element_Select('filter_status');
        $this->addElement( $selectStatus
                        -> setLabel($this->getTranslator()->_('Status'))
                        -> setAttrib('class', 'select')
                        -> addMultiOptions($this->_prepareStatuses())
                    );

        $selectStatus = new Zend_Form_Element_Select('filter_role_id');
        $this->addElement( $selectStatus
                        -> setLabel($this->getTranslator()->_('Role'))
                        -> setAttrib('class', 'select')
                        -> addMultiOptions($this->_prepareRoles())
                    );

        $this->addElement('text', 'filter_login', array(
            'label' => $this->getTranslator()->_('Login'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 20,
                'class' => 'input',
            ),
            'validators' => array(
                array('StringLength', false, array(3,200)),
                array('EmailAddress'),
            ),
        ));

        $this->addElement('text', 'filter_name', array(
            'label' => $this->getTranslator()->_('Name'),
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

        $this->addElement('text', 'filter_email', array(
            'label' => $this->getTranslator()->_('E-mail'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 20,
                'class' => 'input',
            ),
            'validators' => array(
                array('StringLength', false, array(3,200)),
                array('EmailAddress'),
            ),
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
        foreach (Model_Service::factory('user')->getStatusesList() as $status => $value) {
            $list[$value] = $this->getTranslator()->_($status);
        }
        return $list;
    }

    protected function _prepareRoles()
    {
        $list = array(''=>$this->getTranslator()->_('All'));
        foreach (Model_Service::factory('role')->getAll() as $role) {
            $list[$role->id] = $role->name;
        }
        return $list;
    }

}
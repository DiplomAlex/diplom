<?php

class Form_AdminUserEdit extends App_Form
{

    protected $_aclPrefix = 'Form_AdminUserEdit';

    public function init()
    {

        $this->setMethod('POST');

        $selectStatus = new Zend_Form_Element_Select('status');
        $this->addElement(
            $selectStatus
                ->setLabel($this->getTranslator()->_('Status'))
                ->addMultiOptions($this->_prepareStatuses())
        );


        $this->addElement('text', 'login', array(
            'label'      => $this->getTranslator()->_('Login'),
            'attribs'    => array(
                'maxlength' => 50,
                'size'      => 90
            ),
            'validators' => array(
                array('StringLength', false, array(3, 50)),
            ),
            'required'   => true
        ));


        $selectStatus = new Zend_Form_Element_Select('role_id');
        $this->addElement(
            $selectStatus
                ->setLabel($this->getTranslator()->_('Role'))
                ->addMultiOptions($this->_prepareRoles())
        );

        /**
         * binded roles
         */
        $multiRoles = new Zend_Form_Element_MultiCheckbox('roles');
        $this->addElement(
            $multiRoles
                ->setLabel($this->getTranslator()->_('Binded roles'))
                ->setMultiOptions($this->_prepareRoles())
        );

        $this->addElement('password', 'password', array(
            'label'      => $this->getTranslator()->_('Password'),
            'attribs'    => array(
                'maxlength' => 50,
                'size'      => 90
            ),
            'validators' => array(
                array('StringLength', false, array(4, 50)),
                array('PasswordRepeat', false, array('password2')),
            ),
            'filters'    => array('StringTrim'),
            'required'   => false
        ));

        $this->addElement('password', 'password2', array(
            'label'      => $this->getTranslator()->_('Repeat password'),
            'attribs'    => array(
                'maxlength' => 50,
                'size'      => 90
            ),
            'validators' => array(
                array('StringLength', false, array(4, 50))
            ),
            'filters'    => array('StringTrim'),
            'required'   => false
        ));


        $this->addElement('text', 'email', array(
            'label'      => $this->getTranslator()->_('E-mail'),
            'attribs'    => array(
                'maxlength' => 200,
                'size'      => 90
            ),
            'validators' => array(
                array('StringLength', false, array(3, 200)),
                array('EmailAddress'),
            ),
            'required'   => true
        ));


        $this->addElement('text', 'name', array(
            'label'      => $this->getTranslator()->_('Name'),
            'attribs'    => array(
                'maxlength' => 200,
                'size'      => 90
            ),
            'validators' => array(
                array('StringLength', false, array(3, 200))
            ),
            'filters'    => array('StringTrim'),
            'required'   => false
        ));

        $this->addElement('text', 'address', array(
            'label'      => 'Адресс',
            'attribs'    => array(
                'maxlength' => 200,
                'size'      => 90
            ),
            'validators' => array(
                array('StringLength', false, array(3, 200))
            ),
            'filters'    => array('StringTrim'),
            'required'   => false
        ));

        $this->addElement('text', 'tel', array(
            'label'      => 'Телефон',
            'attribs'    => array(
                'maxlength' => 200,
                'size'      => 90
            ),
            'validators' => array(
                array('StringLength', false, array(3, 200)),
                array('Digits'),
            ),
            'filters'    => array('StringTrim'),
            'required'   => false
        ));

        $this->addElement('text', 'dob', array(
            'label'      => $this->getTranslator()->_('Date of birth'),
            'attribs'    => array(
                'maxlength' => 200,
                'size'      => 45,
                'readonly'  => true,
            ),
            'validators' => array(
                array('StringLength', false, array(3, 200)),
            ),
            'required'   => false
        ));


        $this->addElement('resource', 'resource_rc_id', array(
            'label'      => $this->getTranslator()->_('Image'),
            'required'   => false,
            'validators' => array(
                array('Extension', false, Zend_Registry::get('config')->imageValidExtension->toArray())
            )
        ));

        $this->addElement('textarea', 'comment', array(
            'label'      => $this->getTranslator()->_('Примечание'),
            'attribs'    => array(
                'maxlength' => 200,
                'size'      => 90
            ),
            'validators' => array(
                array('StringLength', false, array(3, 200))
            ),
            'filters'    => array('StringTrim'),
            'required'   => false
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
        foreach (Model_Service::factory('user')->getStatusesList() as $status => $value) {
            $list[$value] = $this->getTranslator()->_($status);
        }

        return $list;
    }

    protected function _prepareRoles($except = null)
    {
        $list = array();
        foreach (Model_Service::factory('role')->getAll() as $role) {
            if (($except === null) OR ($role->id != $except)) {
                $list[$role->id] = $role->name;
            }
        }

        return $list;
    }


    public function populate(array $values)
    {
        /*if (isset($values['role_id'])) {
            $this->roles->setMultiOptions($this->_prepareRoles($values['role_id']));
        }*/
        $this->_clearMultiOptions(array('status', 'role_id', 'roles'), $values);

        return parent::populate($values);
    }

    protected function _clearMultiOptions(array $elemNames, array $values)
    {
        $userService = Model_Service::factory('user');
        $user = $userService->getCurrent();

        /**
         * if not allowed selects change - remove all options except current
         */
        foreach ($elemNames as $field) {
            if (($elem = $this->getElement($field)) AND (!$userService->isAllowedByAcl(
                    $user, $this->_aclPrefix . '__' . $field, 'update'
                ))
            ) {
                $options = $elem->getMultiOptions();
                if ($values[$field]) {
                    if (is_array($values[$field])) {
                        $nOpts = array();
                        foreach ($values[$field] as $key => $val) {
                            $nOpts[$val] = $options[$val];
                        }
                        $options = $nOpts;
                    } else {
                        @$options = array($values[$field] => $options[$values[$field]]);
                    }
                } else {
                    $options = array();
                }
                $elem->setMultiOptions($options);
            }
        }
    }

}

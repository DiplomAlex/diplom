<?php

class Catalog_Form_AdminAttributeGroupEdit extends App_Form
{

    public function init()
    {

        $this->setMethod('POST');


        $this->addElement('hidden', 'id', array('required' => FALSE));

        $selectStatus = new Zend_Form_Element_Select('status');
        $this->addElement( $selectStatus
                        -> setLabel($this->getTranslator()->_('Status'))
                        -> setRequired(TRUE)
                        -> addMultiOptions($this->_prepareStatuses())
                    );



        $selectStatus = new Zend_Form_Element_Select('parent_id');
        $this->addElement( $selectStatus
                        -> setLabel($this->getTranslator()->_('Parent group'))
                        /*-> setRequired(TRUE)*/
                        /*-> addMultiOptions($this->_prepareParentId())*/
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
        foreach (Model_Service::factory('catalog/attribute-group')->getStatusesList() as $status => $value) {
            $list[$value] = $this->getTranslator()->_($status);
        }
        return $list;
    }

    /**
     * get tree of categories with root and without current category and its children
     * prepare list for select and init options of parent_id select
     * @param int id of current category
     */
    protected function _initParentId($id)
    {
        $list = Model_Service::factory('catalog/attribute-group')->getFullTreeAsSelectOptions($id);
        $this->parent_id->addMultiOptions($list);
        return $list;
    }


    public function populate(array $values)
    {
        $result = parent::populate($values);
        $this->_initParentId($values['id']);
        return $result;
    }



}

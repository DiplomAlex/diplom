<?php

class Form_AdminArticleTopicEdit extends App_Form
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



        $select = new Zend_Form_Element_Select('parent_id');
        $this->addElement( $select
                        -> setLabel($this->getTranslator()->_('Родительская рубрика'))
                        /*-> setRequired(TRUE)*/
                        /*-> addMultiOptions($this->_prepareParentId())*/
                    );


        $langs = Model_Service::factory('language')->getAllActive();                    
                    
        $seoIdFromFields = array();
        foreach ($langs as $lang) {
            $seoIdFromFields []= 'description_language_'.$lang->id.'_name';
        }
        $this->addElement('text', 'seo_id', array(
            'label' => $this->getTranslator()->_('Seo Id'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(0,200)),
                array(new Form_Element_Validate_IsUniqKey(Model_Service::factory('article-topic'), 'seo_id', array($seoIdFromFields), TRUE, 'id'))
            ),
            'filters' => array('StringTrim'),
            'required' => FALSE
        ));
                               
                            

        foreach ($langs as $lang) {

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
        $all = Model_Service::factory('article-topic')->getStatusesList();
        foreach ($all as $status => $value) {
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
        $list = Model_Service::factory('article-topic')->getFullTreeAsSelectOptions($id);
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

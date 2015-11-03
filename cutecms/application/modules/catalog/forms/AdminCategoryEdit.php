<?php

class Catalog_Form_AdminCategoryEdit extends App_Form
{

    public function init()
    {

        $this->setMethod('POST');


        $this->addElement('hidden', 'id', array('required' => FALSE));

        $selectStatus = new Zend_Form_Element_Select('status');
        $this->addElement( $selectStatus
                        -> setLabel($this->getTranslator()->_('Status'))
                      //  -> setRequired(TRUE)
                        -> addMultiOptions($this->_prepareStatuses())
                    );

               /*
        $this->addElement('multiCheckbox', 'site_ids', array(
            'label' => $this->getTranslator()->_('Web-сайты'),
        ));
        $this->site_ids->addMultiOptions($this->_prepareSiteIds());
                * 
                */
                    
                    

        $this->addElement('resource', 'resource_rc_id', array(
            'label' => $this->getTranslator()->_('Изображение'),
            'required' => FALSE,
            'validators' => array(
				array('Extension', false, Zend_Registry::get('config')->imageValidExtension->toArray())
			)
        ));
        
        $this->addElement('radio', 'design', array(
            'label' => $this->getTranslator()->_('Дизайн'),
            'required' => FALSE,
        ));
        $this->design->setMultiOptions($this->_prepareDesignOptions());

        $selectFilter = new Zend_Form_Element_Select('filter_id');
        $this->addElement(
            $selectFilter
                ->setLabel($this->getTranslator()->_('Фильтр из группы'))
                ->addMultiOptions($this->_prepareAllGroups())
        );

        $selectStatus = new Zend_Form_Element_Select('parent_id');
        $this->addElement( $selectStatus
                        -> setLabel($this->getTranslator()->_('Родительская категория'))
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
                array('StringLength', false, array(0,100)),
                array(new Form_Element_Validate_IsUniqKey(Model_Service::factory('catalog/category'), 'seo_id', array($seoIdFromFields), TRUE, 'id'))
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
                    array('StringLength', false, array(1,200))
                ),
                'filters' => array('StringTrim'),
                'required' => TRUE
            ));
/*
            $this->addElement('textarea', 'description_language_'.$lang->id.'_brief', array(
                'label' => $this->getTranslator()->_('Brief').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 1000,
                    'cols' => 90,
                    'rows' => 4,
                ),
                'validators' => array(
                    array('StringLength', false, array(1,1000))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));
 * 
 */

            $this->addElement('fckeditor', 'description_language_'.$lang->id.'_brief', array(
                'label' => $this->getTranslator()->_('Brief').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 1000,
                    'width' => '80%',
                    'height_koef' => 2,
                ),
                'validators' => array(
                    array('StringLength', false, array(1,1000))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));

            $this->addElement('fckeditor', 'description_language_'.$lang->id.'_full', array(
                'label' => $this->getTranslator()->_('Full').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 2000,
                    'width' => '80%',
                    'height_koef' => 2,
                ),
                'validators' => array(
                    array('StringLength', false, array(1,2000))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));


            $this->addElement('text', 'description_language_'.$lang->id.'_html_title', array(
                'label' => $this->getTranslator()->_('Html title').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 1000,
                    'size' => 90
                ),
                'validators' => array(
                    array('StringLength', false, array(1,1000))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));

            $this->addElement('textarea', 'description_language_'.$lang->id.'_meta_keywords', array(
                'label' => $this->getTranslator()->_('Meta-keywords').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 3000,
                    'cols' => 90,
                    'rows' => 4,
                ),
                'validators' => array(
                    array('StringLength', false, array(1,3000))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));

            $this->addElement('textarea', 'description_language_'.$lang->id.'_meta_description', array(
                'label' => $this->getTranslator()->_('Meta-description').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 5000,
                    'cols' => 90,
                    'rows' => 4,
                ),
                'validators' => array(
                    array('StringLength', false, array(1,5000))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));


        }

        $this->addElement('submitLink', 'save', array(
            'label' => $this->getTranslator()->_('Save'),
            'class' => 'btn-primary',
        ));
        $this->addElement('submitLink', 'cancel', array(
            'label' => $this->getTranslator()->_('Cancel'),
        ));

    }
    
    protected function _prepareSiteIds()
    {
        $list = array();
        $sites = Model_Service::factory('site')->getAll();
        foreach ($sites as $site) {
            $list[$site->id] = $site->specification;
        }
        return $list;
    }


    protected function _prepareStatuses()
    {
        $list = array();
        foreach (Model_Service::factory('catalog/category')->getStatusesList() as $status => $value) {
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
        $list = Model_Service::factory('catalog/category')->getFullTreeAsSelectOptions($id);
        $this->parent_id->addMultiOptions($list);
        return $list;
    }

    protected function _prepareDesignOptions()
    {
        $config = Zend_Registry::get('catalog_config')->categoryDesign;
        $list = array();
        foreach ($config as $dCode => $dValue) {
            $list [$dValue] = $this->getTranslator()->_('categoryDesign.'.$dCode);
        }
        return $list;
    }

    public function _prepareAllGroups()
    {
        $list = array(' ' => '<< Не выводить фильтр >>');
        foreach (Model_Service::factory('catalog/attribute-group')->getAll() as $group) {
            $list[$group->id] = $group->name;
        }

        return $list;
    }
    
    public function populate(array $values)
    {
        if (empty($values['design'])) {
            $values['design'] = Zend_Registry::get('catalog_config')->categoryDesign->items;
        }
        $result = parent::populate($values);
        $this->_initParentId($values['id']);
        return $result;
    }
    

}

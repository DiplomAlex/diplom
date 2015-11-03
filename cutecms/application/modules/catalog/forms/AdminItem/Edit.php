<?php

class Catalog_Form_AdminItem_Edit extends Catalog_Form_AdminItem_Abstract
{
    
    public function init()
    {

        $langs = Model_Service::factory('language')->getAllActive();

        $this->setMethod('POST');


        $this->addElement('hidden', 'id', array('required' => FALSE));
        $this->addElement('hidden', 'is_configurable', array('required' => FALSE));
        $this->addElement('hidden', 'is_downloadable', array('required' => FALSE));


        $this->addElement('text', 'sku', array(
            'label' => $this->getTranslator()->_('SKU'),
            'attribs' => array(
                'maxlength' => 20,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(0,30)),
                array('NotInArray', false, array(Model_Service::factory('catalog/item')->getAllSkuWithoutCurrent(
                    Zend_Controller_Front::getInstance()->getRequest()->getParam('id', null)
                ))),
            ),
            'filters' => array('StringTrim'),
            'required' => FALSE
        ));
/*
        $this->addElement('text', 'code', array(
            'label' => $this->getTranslator()->_('Краткий код'),
            'attribs' => array(
                'maxlength' => 10,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(0,10))
            ),
            'filters' => array('StringTrim'),
            'required' => FALSE
        ));
 *
 */

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
                array(new Form_Element_Validate_IsUniqKey(Model_Service::factory('catalog/item'), 'seo_id', array($seoIdFromFields), TRUE, 'id'))
            ),
            'filters' => array('StringTrim'),
            'required' => FALSE
        ));
                


        $selectStatus = new Zend_Form_Element_Select('status');
        $this->addElement( $selectStatus
                        -> setLabel($this->getTranslator()->_('Status'))
                      //  -> setRequired(TRUE)
                        -> addMultiOptions($this->_prepareStatuses())
                    );

                    
/*
        $select = new Zend_Form_Element_Select('manufacturer_id');
        $this->addElement( $select
                        -> setLabel($this->getTranslator()->_('Производитель'))
                        -> setRequired(FALSE)
                        -> addMultiOptions($this->_prepareManufacturers())
                    );
 *
 */

                    

/*
        $this->addElement('resource', 'resource_rc_id', array(
            'label' => $this->getTranslator()->_('Image'),
            'required' => FALSE
        ));
 * 
 */



        foreach ($langs as $lang) {

            $this->addElement('text', 'description_language_'.$lang->id.'_name', array(
                'label' => $this->getTranslator()->_('Name').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 200,
                    'size' => 90
                ),
                'validators' => array(
                    array('StringLength', false, array(1,1000))
                ),
                'filters' => array('StringTrim'),
                'required' => TRUE
            ));
/*
            $this->addElement('text', 'description_language_'.$lang->id.'_unit', array(
                'label' => $this->getTranslator()->_('Ед. изм.').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 200,
                    'size' => 90
                ),
                'validators' => array(
                    array('StringLength', false, array(1,1000))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));
 * 
 */
            
        }

        /*$this->addElement('checkbox', 'is_new', array(
            'label' => $this->getTranslator()->_('Новинка'),
        ));
         
        $this->addElement('checkbox', 'is_popular', array(
            'label' => $this->getTranslator()->_('Популярный'),
        ));*/
		
        $this->addElement('checkbox', 'home_page_our_collections', array(
            'label' => $this->getTranslator()->_('Отображать на главной в блоке Наши коллекции'),
        ));
         
        $this->addElement('checkbox', 'home_page_item_slider', array(
            'label' => $this->getTranslator()->_('Отображать на главной в слайдере товаров'),
        ));
        
        $attrGroups = $this->createElement('jsTree', 'item_categories')
                           ->setLabel($this->getTranslator()->_('Categories'))
                           ->setRequired(FALSE)
                           ->setAttrib('style', 'width: 200px')
                           ->setMultiOptions($this->_prepareAllCategoriesOptions())
                           ;
        $this->addElement($attrGroups);



        foreach ($langs as $lang) {
/*
            $this->addElement('textarea', 'description_language_'.$lang->id.'_brief', array(
                'label' => $this->getTranslator()->_('Brief').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 1000,
                    'cols' => 90,
                    'rows' => 4,
                ),
                'validators' => array(
                    array('StringLength', false, array(3,2000))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));
 *
 */
            $this->addElement('fckeditor', 'description_language_'.$lang->id.'_material', array(
                'label' => $this->getTranslator()->_('Material').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 1000,
                    'width' => '80%',
                    'height_koef' => 2,
                ),
                'validators' => array(
                    array('StringLength', false, array(3,2000))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));
            
            $this->addElement('fckeditor', 'description_language_'.$lang->id.'_brief', array(
                'label' => $this->getTranslator()->_('Brief').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 1000,
                    'width' => '80%',
                    'height_koef' => 2,
                ),
                'validators' => array(
                    array('StringLength', false, array(3,2000))
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
                    array('StringLength', false, array(1,20000))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));

            $this->addElement('fckeditor', 'description_language_'.$lang->id.'_full2', array(
                'label' => $this->getTranslator()->_('Full top').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 2000,
                    'width' => '80%',
                    'height_koef' => 2,
                ),
                'validators' => array(
                    array('StringLength', false, array(1,20000))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));

            $this->addElement('fckeditor', 'description_language_'.$lang->id.'_full4', array(
                'label' => $this->getTranslator()->_('Full bottom').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 2000,
                    'width' => '80%',
                    'height_koef' => 2,
                ),
                'validators' => array(
                    array('StringLength', false, array(1,20000))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));

/*
            $this->addElement('fckeditor', 'description_language_'.$lang->id.'_more', array(
                'label' => $this->getTranslator()->_('Софт').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 1000,
                    //'width' => 750,
                    'height_koef' => 1,
                ),
                'validators' => array(
                    array('StringLength', false, array(1,20000))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));
 * 
 */

            
            
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


        /*
        $this->addElement('text', 'price', array(
            'label' => $this->getTranslator()->_('Базовая цена'),
            'attribs' => array(
                'maxlength' => 10,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(0,10))
            ),
            'filters' => array('StringTrim'),
            'required' => FALSE
        ));
        */

        $this->addElement('text', 'price', array(
            'label' => $this->getTranslator()->_('Цена'),
            'attribs' => array(
                'maxlength' => 10,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(0,10))
            ),
            'filters' => array('StringTrim'),
            'required' => FALSE
        ));
        
/*
        $this->addElement('text', 'price2', array(
            'label' => $this->getTranslator()->_('Цена-Опт'),
            'attribs' => array(
                'maxlength' => 10,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(0,10))
            ),
            'filters' => array('StringTrim'),
            'required' => FALSE
        ));

        $this->addElement('text', 'price3', array(
            'label' => $this->getTranslator()->_('Цена-Дилер'),
            'attribs' => array(
                'maxlength' => 10,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(0,10))
            ),
            'filters' => array('StringTrim'),
            'required' => FALSE
        ));
        
        $this->addElement('text', 'old_price', array(
            'label' => $this->getTranslator()->_('Старая цена'),
            'attribs' => array(
                'maxlength' => 10,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(0,10))
            ),
            'filters' => array('StringTrim'),
            'required' => FALSE
        ));
        

        $this->addElement('text', 'stock_qty', array(
            'label' => $this->getTranslator()->_('Количество на складе'),
            'attribs' => array(
                'maxlength' => 10,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', false, array(0,10))
            ),
            'filters' => array('StringTrim'),
            'required' => FALSE
        ));
 * 
 */



/*
        $brules = $this ->createElement('jqGrid', 'brules')
                       ->setLabel($this->getTranslator()->_('Бизнес-правила'))
                       ->setRequired(FALSE)
                       ->setAttribs(array(
                                'width' => 642,
                                'height' => 200,
                                'colModel' => $this->_prepareBrulesColModel(),
                                'colNames' => $this->_prepareBrulesColNames(),
                                'url' => $this->_prepareBrulesUrlGet(),
                                'editurl' => $this->_prepareBrulesUrlEdit(),
                                'deleteurl' => $this->_prepareBrulesUrlDelete(),
                                'nopager' => TRUE,
                       ))
                       ;
        $this->addElement($brules);
 *
 */
        $remains = $this->createElement('jqGrid', 'remain')
            ->setLabel($this->getTranslator()->_('Остатки'))
            ->setRequired(false)
            ->setAttribs(
                array(
                    'width'             => 720,
                    'height'            => 300,
                    'nopager'           => true,
                    'pginput'           => 'true',
                    'pgbuttons'         => 'true',
                    'colModel'          => $this->_prepareRemainsColModel(),
                    'colNames'          => $this->_prepareRemainsColNames(),
                    'url'               => $this->_prepareRemainsUrlGet(),
                    'noStandartButtons' => true,
                )
            );
        $this->addElement($remains);

        $this->addElement('submitLink', 'save', array(
            'label' => $this->getTranslator()->_('Save'),
            'class' => 'btn-primary',
        ));
        $this->addElement('submitLink', 'cancel', array(
            'label' => $this->getTranslator()->_('Cancel'),
        ));


        /**
         * create groups
         */

        /* shared */
        /*
        $sharedGroup = array();
        $sharedGroup []= 'id';
        $sharedGroup []= 'is_configurable';
        $sharedGroup []= 'is_downloadable';
        $sharedGroup []= 'status';
        $sharedGroup []= 'sku';
        $sharedGroup []= 'code';
        $sharedGroup []= 'seo_id';
        foreach ($langs as $lang) {
            $sharedGroup []= 'description_language_'.$lang->id.'_name';
            $sharedGroup []= 'description_language_'.$lang->id.'_unit';
        }
        $sharedGroup []= 'resource_rc_id';
        $sharedGroup []= 'stock_qty';
        $sharedGroup []= 'manufacturer_id';
        $sharedGroup []= 'is_new';
        $sharedGroup []= 'is_popular';
        $sharedGroup []= 'main_page_slider';
        // $sharedGroup []= 'main_left_baner';
        $this->addDisplayGroup($sharedGroup, 'tab_shared', array('label' => $this->getTranslator()->_('Общие параметры')));
         * 
         */

        /* descriptions */
        /*
        $descGroup = array();
        foreach ($langs as $lang) {
            $descGroup []= 'description_language_'.$lang->id.'_brief';
            $descGroup []= 'description_language_'.$lang->id.'_full';
            $descGroup []= 'description_language_'.$lang->id.'_more';
            $descGroup []= 'description_language_'.$lang->id.'_html_title';
            $descGroup []= 'description_language_'.$lang->id.'_meta_keywords';
            $descGroup []= 'description_language_'.$lang->id.'_meta_description';
        }
        $this->addDisplayGroup($descGroup, 'tab_descriptions', array('label' => $this->getTranslator()->_('Описания')));
         *
         */

        /* categories */
        /*
        $catGroup = array();
        $catGroup []= 'item_categories';
        $this->addDisplayGroup($catGroup, 'tab_categories', array('label' => $this->getTranslator()->_('Категории')));
         *
         */
        
        
        /* price */
        /*
        $priceGroup = array();
        $priceGroup []= 'price';
        $priceGroup []= 'price2';
        $priceGroup []= 'price3';
        $priceGroup []= 'old_price';
        $priceGroup []= 'brules';
        $this->addDisplayGroup($priceGroup, 'tab_price', array('label' => $this->getTranslator()->_('Цены')));
         *
         */

        /* buttons */
        /*
        $btnGroup = array();
        $btnGroup []= 'save';
        $btnGroup []= 'cancel';
        $this->addDisplayGroup($btnGroup, 'tab_buttons', array('isButtonsGroup' => TRUE));
        
        
         * 
         */
        $this->_prepareAddonTabs();
    }

    /**
     * Подготовить колонки таблицы остатков
     *
     * @return array
     */
    protected function _prepareRemainsColModel()
    {
        $colModel = array(
            array(
                'index'    => 'sku', 'name' => 'sku',
                'sortable' => false, 'editable' => false,
                'width'    => '65px', 'align' => 'center', 'fixed' => true,
            ),
            array(
                'index'    => 'code', 'name' => 'code',
                'sortable' => false, 'editable' => false,
                'width'    => '65px', 'align' => 'center', 'fixed' => true,
            ),
            array(
                'index'    => 'material', 'name' => 'material',
                'sortable' => false, 'editable' => false, 'fixed' => true,
                'width'    => '110px',
            ),
            array(
                'index'    => 'probe', 'name' => 'probe',
                'sortable' => false, 'editable' => false,
                'width'    => '50px', 'align' => 'center', 'fixed' => true,
            ),
            array(
                'index'    => 'size', 'name' => 'size',
                'sortable' => false, 'editable' => false,
                'width'    => '60px', 'align' => 'center', 'fixed' => true,
            ),
            array(
                'index'    => 'characteristics', 'name' => 'characteristics',
                'sortable' => false, 'editable' => false,
                'width'    => '114px', 'fixed' => true,
            ),
            array(
                'index'    => 'weight', 'name' => 'weight',
                'sortable' => false, 'editable' => false,
                'width'    => '60px', 'align' => 'center', 'fixed' => true,
            ),
            array(
                'index'    => 'price', 'name' => 'price',
                'sortable' => false, 'editable' => false,
                'width'    => '60px', 'align' => 'center', 'fixed' => true,
            ),
            array(
                'index'    => 'in_stock', 'name' => 'in_stock',
                'sortable' => false, 'editable' => false,
                'width'    => '60px', 'align' => 'center', 'fixed' => true,
            ),
        );

        return $colModel;
    }

    /**
     * Подготовить названия колонок таблици остатков
     *
     * @return array
     */
    protected function _prepareRemainsColNames()
    {
        $tr = $this->getTranslator();
        $colNames = array(
            $tr->_('Артикул'),
            $tr->_('Штрих-код'),
            $tr->_('Материал'),
            $tr->_('Проба'),
            $tr->_('Размер'),
            $tr->_('Характеристики'),
            $tr->_('Вес'),
            $tr->_('Цена'),
            $tr->_('Наличие'),
        );

        return $colNames;
    }

    /**
     * Возвращает ссылку на получение остатков
     *
     * @return mixed
     */
    protected function _prepareRemainsUrlGet()
    {
        $id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id');
        if ($id) {
            $sku = Model_Service::factory('catalog/item')->get($id)->sku;
        }
        return $this->getView()->stdUrl(array('sku' => $sku ? $sku : null), 'ajax-get-remains', 'admin-item_index', 'catalog');
    }

    protected function _prepareStatuses()
    {
        $list = array();
        foreach (Model_Service::factory('catalog/item')->getStatusesList() as $status => $value) {
            $list[$value] = $this->getTranslator()->_($status);
        }
        return $list;
    }
    
    protected function _prepareManufacturers()
    {
        $list = array('' => ' -- '.$this->getTranslator()->_('None').' -- ');
        $all = Model_Service::factory('catalog/manufacturer')->getAll();
        foreach ($all as $man) {
            $list [$man->id] = $man->name;
        }
        return $list;
    }


    protected function _prepareAllCategoriesOptions()
    {
        return Model_Service::factory('catalog/category')->getFullTree(FALSE)->toArray();
    }


    /********************   brules   ****************************************/
    
    protected function _prepareBrulesColModel()
    {
        $model = $this->_getGridsConfig('brules')->colModel->toArray();
        foreach ($model as $idx=>$col) {
            if ($col['name'] == 'code') {
                $model[$idx]['editoptions'] = array('value'=>Model_Service::factory('catalog/brule')->getAllAvailableForItem());
            }
        }
        return $model;
    }


    protected function _prepareBrulesColNames()
    {
        return $this->_getGridsConfig('brules')->colNames->toArray();
    }



    protected function _prepareBrulesUrlGet()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-get-brule', 'admin-item_brule', 'catalog');
    }

    protected function _prepareBrulesUrlAdd()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-new-brule', 'admin-item_brule', 'catalog');
    }

    protected function _prepareBrulesUrlEdit()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-edit-brule', 'admin-item_brule', 'catalog');
    }

    protected function _prepareBrulesUrlDelete()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-delete-brule', 'admin-item_brule', 'catalog');
    }


    /***********************   addonTabs    ***************/
    
    protected function _prepareAddonTabs()
    {
        $tabs = $this->_getGridsConfig('addonTabs');
        $arr = $tabs->tab;
        if (is_string($arr)) {
            $arr = array($arr);
        }
        foreach ($arr as $tab) {
            $form = new $tab;
            foreach ($form->getElements() as $el) {
                $this->addElement($el);
            } 
            $this->addDisplayGroups($form->getDisplayGroups());
        }
    }
    
    public function populate(array $values)
    {
        if ( ! (array_key_exists('is_configurable', $values)) OR ! (int) $values['is_configurable']) {
            $this->removeDisplayGroup('tab_bundles');
        }
        return parent::populate($values);
    }
    
    
      
}

<?php

class Catalog_Form_AdminItemFilter extends App_Form
{

    public function init()
    {

        $this->setMethod('GET');

        $this->addElement('text', 'filter_name', array(
            'label' => $this->getTranslator()->_('Name'),
            'attribs' => array(
                'size' => 20,
                'class' => 'input',
            ),
            'filters' => array('StringTrim'),
        ));

        // $this->addElement('text', 'filter_sku', array(
            // 'label' => $this->getTranslator()->_('SKU'),
            // 'attribs' => array(
                // 'size' => 20,
                // 'class' => 'input',
            // ),
            // 'filters' => array('StringTrim'),
        // ));

        $this->addElement('text', 'filter_seo_id', array(
            'label' => $this->getTranslator()->_('Seo id'),
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
        
        /*$this->addElement('checkbox', 'filter_is_popular', array(
            'label' => $this->getTranslator()->_('Популярный'),
        )); 
        
        $this->addElement('checkbox', 'filter_is_new', array(
            'label' => $this->getTranslator()->_('Новинка'),
        )); 
        
        $this->addElement('checkbox', 'filter_main_page_slider', array(
            'label' => $this->getTranslator()->_('В слайдере на главной'),
        )); 
        
        $this->addElement('checkbox', 'filter_main_left_baner', array(
            'label' => $this->getTranslator()->_('Банер слева на главной'),
        ));*/

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
}
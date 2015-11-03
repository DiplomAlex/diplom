<?php

class Catalog_Form_AdminItem_Attribute_Edit extends App_Form
{

    public function init()
    {

        $this->setName('attribute_edit');
        $this->setAttrib('id', 'attribute_edit');
        
        $this->setMethod('POST');


        /*$this->addElement('hidden', 'hash', array('required' => FALSE));*/
        
        $this->addElement('hidden', 'code');

        /*
        $this->addElement('text', 'code', array(
            'label' => $this->getTranslator()->_('Code'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 90
            ),
            'validators' => array(
                array('Alnum', FALSE, array()),
                array('StringLength', FALSE, array(1,64))
            ),
            'filters' => array('StringTrim'),
            'required' => FALSE
        ));
        */

        $this->addElement('hidden', 'status');
        
        /*
        $selectStatus = new Zend_Form_Element_Select('status');
        $this->addElement( $selectStatus
                        -> setLabel($this->getTranslator()->_('Status'))
                        -> setRequired(FALSE)
                        -> addMultiOptions($this->_prepareStatuses())
                    );
        */

        foreach (Model_Service::factory('language')->getAllActive() as $lang) {

            $this->addElement('text', 'description_language_'.$lang->id.'_name', array(
                'label' => $this->getTranslator()->_('Name').'('.strtoupper($lang->code2).')',
                'attribs' => array(
                    'maxlength' => 200,
                    'size' => 90
                ),
                'validators' => array(
                    array('StringLength', FALSE, array(3,200))
                ),
                'filters' => array('StringTrim'),
                'required' => FALSE
            ));

        }



        $types = $this  ->createElement('select', 'type')
                          ->setLabel($this->getTranslator()->_('Type'))
                          ->setRequired(FALSE)
                          ->setMultiOptions($this->_prepareTypes())
                          ;
        $this->addElement($types);



        $attrVars = $this ->createElement('jqGrid', 'variants')
                          ->setLabel($this->getTranslator()->_('Варианты значений'))
                          ->setRequired(FALSE)
                          ->setAttribs(array(
                                'width' => 450,
                                'height' => 200,
                                'colModel' => $this->_prepareVariantsColModel(),
                                'colNames' => $this->_prepareVariantsColNames(),
                                'url' => $this->_prepareVariantsUrlGet(),
                                'editurl' => $this->_prepareVariantsUrlEdit(),
                                'deleteurl' => $this->_prepareVariantsUrlDelete(),
                                'nopager' => TRUE,
                                'loadComplete' => 'function(){reloadAttributeValueFields();}',
                          ))
                          ;
        $this->addElement($attrVars);


        $this->addElement('text', 'current_value', array(
            'label' => $this->getTranslator()->_('Текущее значение'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', FALSE, array(1,1000))
            ),
            'filters' => array('StringTrim'),
            'required' => FALSE
        ));
        
/*        
        $this->addElement('text', 'default_value', array(
            'label' => $this->getTranslator()->_('Default value'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', FALSE, array(1,1000))
            ),
            'filters' => array('StringTrim'),
            'required' => FALSE
        ));
*/
    }



    protected function _prepareStatuses()
    {
        return array(
            '0' => $this->getTranslator()->_('invisible'),
            '1' => $this->getTranslator()->_('visible'),
        );
    }


    protected function _prepareTypes()
    {
        $types = Model_Service::factory('catalog/attribute')->getAllTypes();
        $result = array('string'=>$types['string'], 'variant'=>$types['variant']);
        return $result;
    }

    protected function _prepareVariantsColNames()
    {
        return $this->_getGridsConfig('variants')->colNames->toArray();
    }

    protected function _prepareVariantsColModel()
    {
        return $this->_getGridsConfig('variants')->colModel->toArray();
    }





    protected function _prepareVariantsUrlGet()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-get-variant', 'admin-item_attribute', 'catalog');
    }

    protected function _prepareVariantsUrlEdit()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-edit-variant', 'admin-item_attribute', 'catalog');
    }

    protected function _prepareVariantsUrlDelete()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-delete-variant', 'admin-item_attribute', 'catalog');
    }

    protected function _getGridsConfig($section)
    {
        $conf = Model_Service::factory('config')->read('catalog/grids.xml', __CLASS__)->{$section};
        return $conf;
    }

}

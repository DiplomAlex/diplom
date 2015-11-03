<?php

class Catalog_Form_AdminAttributeEdit extends App_Form
{

    public function init()
    {

        $this->setMethod('POST');
        $this->setAttrib('id', 'attribute_edit');
        $this->setName('attribute_edit');

        $this->addElement('hidden', 'id');
        $this->addElement('hidden', 'code');

/*
        $this->addElement('text', 'code', array(
            'label' => $this->getTranslator()->_('Code'),
            'attribs' => array(
                'maxlength' => 200,
                'size' => 90
            ),
            'validators' => array(
                array('StringLength', FALSE, array(1,64))
            ),
            'filters' => array('StringTrim'),
            'required' => TRUE
        ));
*/

        $selectStatus = new Zend_Form_Element_Select('status');
        $this->addElement( $selectStatus
                        -> setLabel($this->getTranslator()->_('Status'))
                        -> setRequired(TRUE)
                        -> addMultiOptions($this->_prepareStatuses())
                    );


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
                'required' => TRUE
            ));

        }


        $attrGroups = $this->createElement('jsTree', 'attribute_groups')
                           ->setLabel($this->getTranslator()->_('Attribute groups'))
                           ->setRequired(FALSE)
                           ->setAttrib('style', 'width: 200px')
                           ->setMultiOptions($this->_prepareAllGroupsOptions())
                           ;
        $this->addElement($attrGroups);

        $types = $this  ->createElement('select', 'type')
                          ->setLabel($this->getTranslator()->_('Type'))
                          ->setRequired(TRUE)
                          ->setMultiOptions($this->_prepareTypes())
                          ;
        $this->addElement($types);

/*
        $attrVars = $this  ->createElement('flexiGrid', 'variants')
                           ->setLabel($this->getTranslator()->_('Attribute value variants'))
                           ->setRequired(FALSE)
                           ->setAttribs(array(
                                'colModel' => $this->_prepareVariantsColModel(),
                                'searchItems' => $this->_prepareVariantsSearchItems(),
                                'url' => $this->_prepareVariantsUrlGet(),
                                'urlAdd' => $this->_prepareVariantsUrlAdd(),
                                'urlEdit' => $this->_prepareVariantsUrlEdit(),
                                'urlDelete' => $this->_prepareVariantsUrlDelete(),
                                'editForm' => new Catalog_Form_AdminAttributeVariantEdit,
                           ))
                           ->setMultiOptions($this->_prepareVariants())
                           ;
*/


        $attrVars = $this ->createElement('jqGrid', 'variants')
                          ->setLabel($this->getTranslator()->_('Варианты значений'))
                          ->setRequired(FALSE)
                          ->setAttribs(array(
                                'width' => 450,
                                'height' => 200,
                                'rowNum' => 250,
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



        $this->addElement($attrVars);

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

        $this->addElement('submitLink', 'save', array(
            'label' => $this->getTranslator()->_('Save'),
        ));
        $this->addElement('submitLink', 'cancel', array(
            'label' => $this->getTranslator()->_('Cancel'),
        ));

    }



    protected function _prepareStatuses()
    {
        return array(
            '0' => $this->getTranslator()->_('invisible'),
            '1' => $this->getTranslator()->_('visible'),
        );
    }



    protected function _prepareAllGroupsOptions()
    {
        return Model_Service::factory('catalog/attribute-group')->getAll()->toArray();
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
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-get-variant', 'admin-attribute', 'catalog');
    }

    protected function _prepareVariantsUrlEdit()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-edit-variant', 'admin-attribute', 'catalog');
    }

    protected function _prepareVariantsUrlDelete()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-delete-variant', 'admin-attribute', 'catalog');
    }

    protected function _getGridsConfig($section)
    {
        $conf = Model_Service::factory('config')->read('catalog/grids.xml', __CLASS__)->{$section};
        return $conf;
    }

}

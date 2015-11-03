<?php

class Catalog_Form_AdminItem_Attribute_AddToExistGroups extends Catalog_Form_AdminItem_Abstract
{

    public function init()
    {
        $attrGroups = $this->createElement('jsTree', 'groups')
                           ->setLabel($this->getTranslator()->_('Attributes to exist groups'))
                           ->setRequired(FALSE)
                           ->setAttrib('style', 'width: 500px')
                           ->setDisableTranslator(TRUE)
                           ->setMultiOptions($this->_prepareOptions())
                           ->setAttrib('types', array(
                                'group'=>array(
                                    'clickable' => TRUE,
                                    'renameable' => FALSE,
                                    'deletable' => FALSE,
                                    'creatable' => FALSE,
                                    'draggable' => FALSE,
                                ),
                           ))
                           ;
        $this->addElement($attrGroups);
    }


    protected function _prepareOptions()
    {
        return Model_Service::factory('catalog/attribute-group')->getGroupsList();
    }

    public function getGroupsElement()
    {
        return $this->groups;
    }



}
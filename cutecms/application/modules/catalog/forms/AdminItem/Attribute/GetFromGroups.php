<?php

class Catalog_Form_AdminItem_Attribute_GetFromGroups extends Catalog_Form_AdminItem_Abstract
{

    public function init()
    {
        $attrGroups = $this->createElement('jsTree', 'attributes_from_groups')
                           /*->setLabel($this->getTranslator()->_('Attributes from groups'))*/
                           ->setRequired(FALSE)
                           ->setAttrib('style', 'width: 500px')
                           ->setDisableTranslator(TRUE)
                           ->setMultiOptions($this->_prepareOptions())
                           ->setAttrib('nodeScript', 'admin-attribute-from-group/node-html.phtml')
                           ->setAttrib('types', array(
                                'group'=>array(
                                    'clickable' => FALSE,
                                    'renameable' => FALSE,
                                    'deletable' => FALSE,
                                    'creatable' => FALSE,
                                    'draggable' => FALSE,
                                ),
                                'attr'=>array(
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
        return Model_Service::factory('catalog/attribute-group')->getGroupsWithAttributesList();
    }

    public function getGroupsElement()
    {
        return $this->attributes_from_groups;
    }

}
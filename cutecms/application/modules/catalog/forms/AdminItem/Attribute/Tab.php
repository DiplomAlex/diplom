<?php

class Catalog_Form_AdminItem_Attribute_Tab extends Catalog_Form_AdminItem_Abstract
{
    
    protected $_defaultInjections = array(
        'Element_Attribute' => 'Catalog_Form_Element_AdminItem_Attribute',
        'Form_AttributeEdit' => 'Catalog_Form_AdminItem_Attribute_Edit',
        'Form_AttributeGetFromGroups' => 'Catalog_Form_AdminItem_Attribute_GetFromGroups', 
    );
    
    public function init()
    {        
        $this->addElement($this->getInjector()->getObject('Element_Attribute', 'attributes', array(
            'labelAttrib' => 'style="width: 0px;"',
            'href' => $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-get-all', 'admin-item_attribute', 'catalog'),
            'classWrap' => 'attributes-wrap',
            'classWrapEdit' => 'attribute-edit-dialog-wrap',
            'classList' => 'attribute-list',
            'classLinkCreate' => 'attribute-create',
            'classLinkAddFromGroup' => 'attribute-add-from-group',
            'classLinkDelete' => 'attribute-delete',
            'classLinkSorting' => 'attribute-sorting',
            'classBtnSubmit' => 'ok',
            'classBtnCancel' => 'cancel',
            'contentDialogEdit' => $this->_prepareContentDialogEdit(),
            'titleEdit' => $this->getTranslator()->_('Редактирование атрибута'),
        )));

        /* attributes */
        $attrGroup = array();
        $attrGroup []= 'attributes';
        $this->addDisplayGroup($attrGroup, 'tab_attributes', array('label' => $this->getTranslator()->_('Атрибуты')));
    }
    
    protected function _prepareContentDialogEdit()
    {
        $view = $this->getView();
        $view->form = $this->getInjector()->getObject('Form_AttributeEdit');
        $xhtml = $view->render('admin-item/attribute/dialog-edit.phtml');
        return $xhtml;
    }
    
    protected function _prepareContentDialogGetFromGroups()
    {
        $view = $this->getView();
        $view->form = $this->getInjector()->getObject('Form_AttributeGetFromGroups');
        $xhtml = $view->render('admin-item/attribute/dialog-get-from-groups.phtml');
        return $xhtml;
    }
}


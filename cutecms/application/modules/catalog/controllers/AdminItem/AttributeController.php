<?php

class Catalog_AdminItem_AttributeController extends Zend_Controller_Action
{
    
    protected $_defaultInjections = array();
    
    public function init()
    {
        $this->_helper->AdminItem($this->_defaultInjections);        
    }
    


    /*********************************************************************************/
    /********************* attributes ************************************************/
    /*********************************************************************************/

    public function ajaxGetGroupsAction()
    {
        $type = $this->_getParam('type');
        if ($type == 'tree') {
            $form = new Catalog_Form_AdminItemAttributeAddToExistGroups;
        }
        else if ($type == 'select'){
            $form = new Catalog_Form_AdminItemAttributeAddToNewGroup;
        }
        else {
            $form = new Catalog_Form_AdminItemAttributeGetFromGroups;
        }
        $xhtml = $form->getGroupsElement()->render();
        echo $xhtml;
    }

    public function ajaxGetFromGroupsAction()
    {
        $ids = $this->_getParam('attributes_from_groups');
        $values = $this->_getParam('default_value');
        $attrService = Model_Service::factory('catalog/attribute');
        if (is_array($ids) AND ( ! empty($ids))) {
            $i = 0;
            foreach ($ids as $id) {
                $attr = $attrService->getComplex($id);
                if (is_array($values)) {
                    $attr->current_value = $values[$i];
                }
                $isNew = TRUE;
                foreach ($this->getHelper('AdminItem')->session()->editingAttributes as $ea) {
                    if ($ea['code']==$attr['code']) {
                        if (is_array($values)) {
                            $ea->current_value = $values[$i];
                        }
                        $isNew = FALSE;
                        break;
                    }
                }
                if ($isNew === TRUE) {
                    $this->getHelper('AdminItem')->session()->editingAttributes->add($attr);
                }
                $i++;
            }
        }
        echo 'ok';
    }


    public function ajaxAddToExistGroupsAction()
    {
        $groups = $this->_getParam('groups');
        $attrs = $this->_getParam('rows');
        $service = Model_Service::factory('catalog/attribute');
        if (is_array($groups) AND is_array($attrs)) {
            $idStartPos = strlen('group_');
            foreach ($groups as $key=>$groupId) {
                $groups[$key] = substr($groupId, $idStartPos);
            }
            foreach ($attrs as $attrId) {
                if ($editAttr = $this->getHelper('AdminItem')->session()->editingAttributes->findOneByHash($attrId)) {
                    if ( ! $attr = $service->getOneByCode($editAttr->code)) {
                        $attr = $editAttr;
                    }
                    $attr->attribute_groups = $groups;
                    $service->saveComplex($attr);
                }
            }
        }
        echo 'ok';
    }


    public function ajaxAddToNewGroupAction()
    {
        $attrService = Model_Service::factory('catalog/attribute');
        $groupService = Model_Service::factory('catalog/attribute-group');
        $attrs = $this->_getParam('rows');
        $group = $groupService->saveFromValues($this->getRequest()->getParams(), TRUE);
        if (is_array($attrs)) {
            foreach ($attrs as $attrHash) {
                if ($editAttr = $this->getHelper('AdminItem')->session()->editingAttributes->findOneByHash($attrHash)) {
                    if ( ! $attr = $attrService->getOneByCode($editAttr->code)) {
                        $attr = $editAttr;
                    }
                    $attr->attribute_groups = array($group->id);
                    $attrService->saveComplex($attr);
                }
            }
        }
        echo 'ok';
    }



    public function ajaxGetValueFieldsAction()
    {
        $type = $this->_getParam('type', 'variant');
        $curValue = $this->_getParam('current_value', NULL);
        $defValue = $this->_getParam('default_value', NULL);
        if ($type == 'variant') {
            $variantsArr = array('' => ' -- ');
            foreach ($this->getHelper('AdminItem')->session()->editingVariants as $var) {
                $variantsArr[$var->value] = $var->text;
            }
            $answer = array(
                'fieldDefaultValue' => $this->view->formSelect('default_value', $defValue, array(
                    'class' => 'select',
                ), $variantsArr),
                'fieldCurrentValue' => $this->view->formSelect('current_value', $curValue, array(
                    'class' => 'select',
                ), $variantsArr),
            );            
        }
        else if ($type == 'text') {
            $answer = array(
                'fieldDefaultValue' => $this->view->formTextarea('default_value', $defValue, array(
                    'class' => 'input',
                    'rows' => 2,
                ), $variantsArr),
                'fieldCurrentValue' => $this->view->formTextarea('current_value', $curValue, array(
                    'class' => 'input',
                    'rows' => 2,
                ), $variantsArr),
            );                        
        }
        else {
            if ($type == 'int') {
                $defValue = (int) $defValue;
                $curValue = (int) $curValue;
            }
            else if ($type == 'decimal') {
                $defValue = (float) $defValue;
                $curValue = (float) $curValue;                
            }
            else if ($type == 'datetime') {
                if ( ! strtotime($defValue)) {
                    $defValue = '';
                }
                if ( ! strtotime($curValue)) {
                    $curValue = '';
                }
            }
            $answer = array(
                'fieldDefaultValue' => $this->view->formText('default_value', $defValue, array(
                    'maxlength' => 200,
                    'size' => 90,
                    'class' => 'input',            
                )),
                'fieldCurrentValue' => $this->view->formText('current_value', $curValue, array(
                    'maxlength' => 200,
                    'size' => 90,
                    'class' => 'input',                
                )),
            );
        }
        echo Zend_Json::encode($answer);
    }
    
    public function ajaxAddFromGroupAction()
    {
        $groupId = $this->_getParam('group');
        $service = Model_Service::factory('catalog/attribute');
        $attrs = $service->getAllByGroup($groupId);
        $editingAttrs = $this->getHelper('AdminItem')->session()->editingAttributes;
        foreach ($attrs as $attr) {
            if ( ! $editingAttrs->findByCode($attr->code)) {
                $attrDescs = $service->getMapper()->getPlugin('Description')->fetchDescriptions($attr->id);
                foreach ($attrDescs as $field=>$value) {
                    if ($attr->hasElement($field)) {
                        $attr->{$field} = $value;
                    }
                }
                if ($attr->default_value) {
                    $attr->current_value = $attr->default_value;
                }
                $editingAttrs->add($attr);
            }
        }
        echo 'ok';
    }


    /**
     * get all attributes of currently editing item
     */
    public function ajaxGetAllAction()
    {
        $this->getHelper('ViewRenderer')->setNoRender(FALSE);
        $this->view->attributes = $this->getHelper('AdminItem')->session()->editingAttributes;
        $this->view->groupsList = Model_Service::factory('catalog/attribute-group')
                                    ->getFullTreeAsSelectOptions(NULL, TRUE, ' -- ');
        $this->view->attribute_Draw()->setScriptInputRequired('admin-item/attribute/input-required.phtml')
                                     ->setScriptInputNotRequired('admin-item/attribute/input-not-required.phtml')
                                     ->setAddNoneVariant(TRUE)
                                     ->setInputOnlyMode(TRUE)
                                     ;                                    
    }
    
    /**
     * change sorting of attributes in collection
     */
    public function ajaxChangeSortingAction()
    {
        $pos = $this->_getParam('position');
        $hash = $this->_getParam('hash');
        $attrs = $this->getHelper('AdminItem')->session()->editingAttributes;
        $index = $attrs->findOneIndexByHash($hash);
        $attrs->changeSorting($index, $pos);
        echo 'ok';
    }
    
    /**
     * get one attribute of currently editing item
     */
    public function ajaxGetAction()
    {
        if ($hash = $this->_getParam('hash')) {
            $answer = array();
            foreach ($this->getHelper('AdminItem')->session()->editingAttributes as $key=>$attr) {
                if ($attr->hash == $hash) {
                    $answer = $attr->toArray();
                    $this->getHelper('AdminItem')->session()->editingVariants = $attr->variants;
                    break;
                }
            }
        }
        else {
            $attrService = Model_Service::factory('catalog/attribute');
            $attr = $attrService->create();
            $this->getHelper('AdminItem')->session()->editingVariants = $attr->variants;
            $answer = $attr->toArray();
        }
        echo Zend_Json::encode($answer);
    }
    

    /**
     * edit existing attribute of item
     * checks duplications by code
     * $_REQUEST should contain a map of the attribute ($field=>$value)
     */
    public function ajaxEditAction()
    {
        $service = Model_Service::factory('catalog/attribute');
        $hash = $this->_getParam('hash');
        $values = $this->getRequest()->getParams();
        $duplicate = FALSE;
        $found = FALSE;
        foreach ($this->getHelper('AdminItem')->session()->editingAttributes as $attr) {
            if ((isset($values['code'])) AND ($attr['code']==$values['code']) AND ($attr['hash'] != $hash)) {
                $duplicate = TRUE;
                break;
            }
            else if ($attr['hash'] == $hash) {
                foreach ($values as $key=>$val) {
                    if ($attr->hasElement($key)) {
                        $attr->{$key} = $val;
                    }
                }
                $attr->variants = $this->getHelper('AdminItem')->session()->editingVariants;
                $found = TRUE;
                break;
            }
        }
        if ($duplicate) {
            echo $this->view->translate('Attribute code duplicated');
        }
        else {
            if ( ! $found) {
                $attr = $service->createAttributeFromValues($values + array(
                    'variants' => $this->getHelper('AdminItem')->session()->editingVariants,
                ));
                $this->getHelper('AdminItem')->session()->editingAttributes->add($attr);
            }
            $answer = $attr->toArray();
            echo Zend_Json::encode($answer);
        }
    }

    /**
     * $_REQUEST['rows'] contains array of attributes hashes (each added with prefix "row")
     */
    public function ajaxDeleteAction()
    {
        $hash = $this->_getParam('hash');
        foreach ($this->getHelper('AdminItem')->session()->editingAttributes as $key=>$attr) {
            if ($attr['hash'] == $hash) {
                $this->getHelper('AdminItem')->session()->editingAttributes->remove($key);
            }
        }
        echo 'ok';
    }



    /*********************************************************************************/
    /********************* variants ************************************************/
    /*********************************************************************************/


    /**
     * get all variants of attribute
     */
    public function ajaxGetVariantAction()
    {
        $rowId = $this->_getParam('row_id');
        $isSimple = (bool) $this->_getParam('simple');
        $answer = array();
        $variants = $this->getHelper('AdminItem')->session()->editingVariants;
        foreach ($variants as $row) {
            $rows[] = array(
                'id' => $row['hash'],
                'cell' => array($row['text'], $row['value'], $row['param1'], $row['param2'],),
            );
            if ($isSimple) {
                $answer[$row['value']] = $row['text'];
            }
        }
        if ( ! $isSimple) {
            $answer = array(
                'page' => '1',
                'total' => $variants->count(),
                'rows' => $rows,
            );
        }
        echo Zend_Json::encode($answer);
    }



    /**
     * edit existing variant of attribute
     */
    public function ajaxEditVariantAction()
    {
        $service = Model_Service::factory('catalog/attribute');
        $rowId = $this->_getParam('id');
        $values = $this->getRequest()->getParams();
        $answer = array();
        $found = FALSE;
        $variants = $this->getHelper('AdminItem')->session()->editingVariants;
        foreach ($variants as $var) {
            if ($var['hash'] == $rowId) {
                $var['value'] = $values['value'];
                $var['text'] = $values['value'];
                $var['param1'] = $values['param1'];
                $var['param2'] = $values['param2'];
                $found = TRUE;
                break;
            }
        }
        if ( ! $found) {
            $var = $service->createVariantFromValues(array(
                'value'  => $values['value'],
                'text'   => $values['value'],
                'param1' => $values['param1'],
                'param2' => $values['param2'],
            ));
            $variants->add($var);
            echo 'added';
        }        
        $answer = $var->toArray();
        echo Zend_Json::encode($answer);
    }

    /**
     * delete variant of attribute
     */
    public function ajaxDeleteVariantAction()
    {
        $rows = $this->_getParam('rows');
        $variants = $this->getHelper('AdminItem')->session()->editingVariants;
        foreach ($rows as $row) {
            $variants->removeByElement('hash', $row);
        }
        echo 'ok';
        
    }



    
    
    
}
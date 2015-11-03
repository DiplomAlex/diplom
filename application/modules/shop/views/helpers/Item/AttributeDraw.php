<?php

class Shop_View_Helper_Item_AttributeDraw extends Catalog_View_Helper_Attribute_Draw
{
    
    protected $_scriptInputRequired = 'catalog/item/attribute/input-required.phtml';
    protected $_scriptInputNotRequired = 'catalog/item/attribute/input-not-required.phtml';
    
    protected $_scriptColorInputRequired = 'catalog/item/attribute/color-input-required.phtml';
    protected $_scriptColorInputNotRequired = 'catalog/item/attribute/color-input-not-required.phtml';
    
    public function item_AttributeDraw(Model_Object_Interface $attr = NULL)
    {
        $colorCode = $this->view->item_AttributeCodeByAlias('color');
        if (($attr) AND ($attr->code == $colorCode)) {
            $result = $this->_drawColor($attr);
        }
        else {
            $result = $this->attribute_Draw($attr);
        }
        return $result; 
    }
    
    protected function _drawColor(Model_Object_Interface $attr) 
    {
        if ($attr->isInputRequired()) {
            $result = $this->view->partial($this->_scriptColorInputRequired, array(
                'attr' => $attr,
                'inputType' => 'radio',
            ));
        }
        else {
            $result = $this->view->partial($this->_scriptColorInputNotRequired, array('attr'=>$attr));
        }
        return $result;
    }
    
} 
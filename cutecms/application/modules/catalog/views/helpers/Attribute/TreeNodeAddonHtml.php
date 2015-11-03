<?php

class Catalog_View_Helper_Attribute_TreeNodeAddonHtml extends Zend_View_Helper_Abstract
{

    protected $_defaultOptions = array(
        'nodeTypeField' => 'type',
        'nodeVariantsField' => 'variants',
        'nodeValueField' => 'default_value',
        'name' => 'default_value[]',
        'class' => 'treeNodeInput',
    );

    protected $_options = NULL;

    public function attribute_TreeNodeAddonHtml($name = NULL, $value, $node, $fields, $attribs)
    {
        if ($name === NULL) {
            return $this;
        }

        if ($node[$fields['rel']] !== 'attr') {
            return '';
        }

        $rowId = $node[$fields['id']];
        $inputAttribs = array('rowId' => $rowId, 'class' => $this->getOptions('class'));
        App_Debug::dump($node);
        switch($node[$this->getOptions('nodeTypeField')]) {
            case 'int':
            case 'decimal':
            case 'string':
                $html = $this->view->formText($this->getOptions('name'), $node[$this->getOptions('nodeValueField')], $inputAttribs);
                break;
            case 'datetime':
                $html = $this->view->formText($this->getOptions('name'), $node[$this->getOptions('nodeValueField')], $inputAttribs)
                      /*. $this->view->jquery_Datepicker(array('.'.$this->getOptions('class').'[rowId='.$rowId.']'), FALSE)*/
                      ;
                break;
            case 'text':
                $inputAttribs['cols'] = 10;
                $inputAttribs['rows'] = 1;
                $html = $this->view->formTextarea($this->getOptions('name'), $node[$this->getOptions('nodeValueField')], $inputAttribs);
                break;
            case 'variant':
                if ($node[$this->getOptions('nodeVariantsField')] instanceof Model_Collection_Interface) {
                    $node[$this->getOptions('nodeVariantsField')] = $this->_parseVariantsFromCollection($node[$this->getOptions('nodeVariantsField')]);
                }
                $inputAttribs['multiple'] = FALSE;
                $html = $this->view->formSelect($this->getOptions('name'), $node[$this->getOptions('nodeValueField')], $inputAttribs, $node[$this->getOptions('nodeVariantsField')]);
                break;
        }
        return $html;
    }

    /**
     * return all options, array of options, or a single option value
     * @param mixed string|array|NULL
     * @return mixed array|string
     */
    public function getOptions($option = NULL)
    {
        if ($this->_options === NULL) {
            $this->_options = $this->_defaultOptions;
        }
        if ($option === NULL) {
            $result = $this->_options;
        }
        else if (is_array($option)) {
            $result = array();
            foreach ($option as $opt) {
                $result []= $this->_options[$opt];
            }
        }
        else {
            $result = $this->_options[$option];
        }
        return $result;
    }

    public function setOptions(array $options)
    {
        $this->_options = array_merge($this->_defaultOptions, $options);
        return $this;
    }

    protected function _parseVariantsFromCollection(Model_Collection_Interface $vars)
    {
        $result = array('' => ' -- ');
        foreach ($vars as $var) {
            $result[$var->value] = $var->text;
        }
        return $result;
    }

}
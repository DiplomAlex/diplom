<?php

class App_Form_Element_JsTree extends Zend_Form_Element_Multi
{

	public $helper = 'formJsTree';


    public function isValid($value, $context = null)
    {
        return TRUE;
    }

}
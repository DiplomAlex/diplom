<?php

class App_Form_Element_JqGrid extends Zend_Form_Element_Multi
{

    public $helper = 'formJqGrid';

    public function isValid($value, $context = null)
    {
        return TRUE;
    }

}
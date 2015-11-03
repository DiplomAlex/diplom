<?php

class App_Form_Element_FlexiGrid extends Zend_Form_Element_Multi
{

    public $helper = 'formFlexiGrid';

    public function isValid($value, $context = null)
    {
        return TRUE;
    }

}
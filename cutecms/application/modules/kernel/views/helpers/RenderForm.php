<?php

class View_Helper_RenderForm extends Zend_View_Helper_Abstract
{

    public function renderForm(App_Form $form = NULL, $script = 'form.phtml', $viewParams = array())
    {
        if ($form === NULL) {
            echo __CLASS__.' says that form is NULL ! ';
            App_Debug::backtrace();exit;
        }
        return $this->view->partial($script, array('form'=>$form)+$viewParams);
    }

}
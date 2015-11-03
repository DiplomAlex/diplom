<?php

class Checkout_View_Helper_Brule_OnOff extends Zend_View_Helper_Abstract
{

    public function brule_OnOff(Model_Object_Interface $brule, $hrefOn, $hrefOff, $imgOn = NULL, $imgOff = NULL)
    {
        if ($brule->active) {
            if ($imgOn === NULL) {
                $imgOn = $this->view->skin()->url().'images/on.png';
            }
            $xhtml = '<a href="'.$hrefOff.'" title="'.$this->view->translate('Включено. Выключить?').'"><img src="'.$imgOn.'"></a>';
        }
        else {
            if ($imgOff === NULL) {
                $imgOff = $this->view->skin()->url().'images/off.png';
            }
            $xhtml = '<a href="'.$hrefOn.'" title="'.$this->view->translate('Выключено. Включить?').'"><img src="'.$imgOff.'"></a>';
        }
        return $xhtml;
    }

}
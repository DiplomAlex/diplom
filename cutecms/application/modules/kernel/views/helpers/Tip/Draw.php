<?php

class View_Helper_Tip_Draw extends Zend_View_Helper_Abstract
{

    public function tip_Draw($script = 'tip/draw.phtml')
    {
        if ($tips = Model_Service::factory('tip')->getCurrent()) {
            $html = $this->view->partial($script, array('tips'=>$tips));
            App_Debug::dump($tips->toArray());
        }
        else {
            $html = '';
        }
        return $html;
    }

}
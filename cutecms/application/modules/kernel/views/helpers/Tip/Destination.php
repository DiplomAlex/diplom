<?php

class View_Helper_Tip_Destination extends Zend_View_Helper_Abstract
{

    public function tip_Destination(Model_Object_Interface $tip)
    {
        $dests = Model_Service::factory('menu')->getFlatStructure();
        $html = $this->view->translate($dests[$tip->destination]['label']);
        return $html;
    }

}
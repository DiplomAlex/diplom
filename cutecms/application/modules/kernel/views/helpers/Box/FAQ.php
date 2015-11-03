<?php

class View_Helper_Box_FAQ extends Zend_View_Helper_Abstract
{

    public function box_FAQ()
    {
        $faqs = Model_Service::factory('faq')->getAllActive(Zend_Registry::get('config')->boxes->faqs->limit);
        $html = $this->view->partial('box/faq.phtml', array('faqs'=>$faqs->toArray()));
        return $html;
    }

}
<?php

class View_Helper_Box_NewsAnnounce extends Zend_View_Helper_Abstract
{

    public function box_NewsAnnounce()
    {

        $data = Model_Service::factory('news') -> getLatestActive(
                    Zend_Registry::get('config')->boxes->newsAnnounce->limit
                );
        return $this->view->partial('box/news-announce.phtml', array('data'=>$data));
    }

}
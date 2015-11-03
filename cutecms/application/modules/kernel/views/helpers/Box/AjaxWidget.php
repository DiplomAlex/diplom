<?php

class View_Helper_Box_AjaxWidget extends Zend_View_Helper_Abstract
{

    /**
     * @param string
     * @return xhtml
     */
    public function box_AjaxWidget($url)
    {
        $id = 'box_ajax_wj_' . md5($url) . (string) rand(0, 1000); /* why 1000?? ) */
        $html = $this->view->partial('box/ajax-widget.phtml', array('href'=>$url, 'wjID'=>$id));
        return $html;
    }

}
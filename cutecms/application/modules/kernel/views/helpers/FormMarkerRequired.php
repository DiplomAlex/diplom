<?php

class View_Helper_FormMarkerRequired extends Zend_View_Helper_Abstract
{

    public function formMarkerRequired()
    {
        return ' <span class="red">(*)</span>';
    }

}

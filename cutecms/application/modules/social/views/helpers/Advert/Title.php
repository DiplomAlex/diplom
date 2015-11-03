<?php

class Social_View_Helper_Advert_Title extends Zend_View_Helper_Abstract
{

    public function advert_Title(Model_Object_Interface $advert)
    {
        return Model_Service::factory('social/advert')->getTitle($advert);
    }

}
<?php

class Social_Model_Service_Helper_AdvertCategoryFields extends Model_Service_Helper_Abstract
{

    public function getAdvertTitle(Model_Object_Interface $advert)
    {
        $title = $advert->category_name;
        $title .= ': ' . $advert->automate;
        /*if ($titleField = $advert->fields->findOneByCode('title')) {
            $title .= ': ' . $titleField->value;
        }*/
        return $title;
    }

}
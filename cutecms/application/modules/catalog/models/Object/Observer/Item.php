<?php

class Catalog_Model_Object_Observer_Item extends App_Event_Observer
{

    public function onBeforeDelete()
    {
        $content       = $this->getData(0);
        $galleryHelper = Model_Service::factory('catalog/item')->getHelper('Gallery');
        $galleryHelper->clearLinkedToContent($galleryHelper->getContentType(), $content->id);
    }

    public function onAfterSave()
    {
        $content = $this->getData(0);
        $service = Model_Service::factory('catalog/item-search');

        $service->deleteAllValues($content->id);
        foreach ($content->attributes as $attribute) {
            if ($attribute->type == 'variant') {
                if (!empty($attribute->current_value)) {
                    $service->setValue($content->id, $attribute->id, $attribute->current_value);
                }
            }
        }
    }

}
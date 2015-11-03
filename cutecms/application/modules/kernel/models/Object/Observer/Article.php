<?php

class Model_Object_Observer_Article extends App_Event_Observer
{
    
    public function onBeforeDelete()
    {
        $content = $this->getData(0);
        $galleryHelper = Model_Service::factory('article')->getHelper('Gallery');
        $contentType = $galleryHelper->getContentType();
        $galleryHelper->clearLinkedToContent($contentType, $content->id);
    }
    
}
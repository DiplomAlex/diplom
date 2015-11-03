<?php

class Model_Service_Helper_Content_Gallery extends Model_Service_Helper_Content_Abstract implements Model_Service_Helper_Content_Interface
{
    
    protected function _postInit()
    {
        $this->getLinkedService()->getMapper()->setRefTableLinkedField('gallery_id');
    }
            
    public function getGalleryWithActiveImage($contentId)
    {
        $result = array();
        foreach($this->getLinkedService()->getMapper()->fetchComplexByContent($this->getContentType(), $contentId) as $pic){
            if( $pic->status == 1 )
                $result[] = $pic;
        }
        return $result;
    }
    
}
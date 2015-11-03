<?php

interface Model_Service_Helper_Content_Interface extends Model_Service_Helper_Interface
{
    
    public function getLinkedToContent($contentId);
    
    public function clearLinkedToContent($contentId);
    
}
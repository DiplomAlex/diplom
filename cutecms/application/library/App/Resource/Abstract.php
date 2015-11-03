<?php

class App_Resource_Abstract
{
    
    
    /**
     * @param array file info 
     * @return array
     */
    public function onMoveUploaded($file)
    {
        return $file;
    }
    
    /**
     * @param array file info 
     * @return array preview info
     */
    public function onPreparePreview($file)
    {
        return NULL;
    }
    
    /**
     * checks if plugin should process this file
     * 
     * @param mixed array|string fileinfo|filename 
     * @return array
     */
    public function isProcessable($file)
    {
        return FALSE;
    }
    
}
<?php

class Model_Service_Helper_Multisite extends Model_Service_Helper_Abstract
{
    
    public function setCurrentSiteId($siteId)
    {
        if ( ! (int) $siteId) {
            $siteId = NULL;
        }
        $plugin = $this->getService()->getMapper()->getPlugin('Multisite')->setCurrentSiteId($siteId);
        return $this;
    }
    
    public function linkToSiteByIdArray(array $ids, array $siteIds)
    {
        $this->getService()->getMapper()->getPlugin('Multisite')->linkToSiteByIdArray($ids, $siteIds);
        return $this;
    }

    public function linkToSite($id, array $siteIds)
    {
        return $this->linkToSiteByIdArray(array($id), $siteIds);
    }
    
    public function unlinkFromSiteByIdArray(array $ids, array $siteIds)
    {
        $this->getService()->getMapper()->getPlugin('Multisite')->unlinkFromSiteByIdArray($ids, $siteIds);
        return $this;
    }    
    
    public function unlinkFromSite($id, array $siteIds)
    {
        return $this->unlinkFromSiteByIdArray(array($id), $siteIds);
    }
    
    public function getLinkedSites($obj)
    {
        return $this->getService()->getMapper()->getPlugin('Multisite')->fetchLinkedSites($obj);
    }
    
}

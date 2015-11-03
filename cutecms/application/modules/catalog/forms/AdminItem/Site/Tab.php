<?php

class Catalog_Form_AdminItem_Site_Tab extends App_Form
{
    
    public function init()
    {
        $this->addElement('multiCheckbox', 'site_ids', array(
            'label' => $this->getTranslator()->_('Привязанные сайты'),
        ));
        $this->site_ids->setMultiOptions($this->_prepareSiteIds());
        
        $group = array();
        $group []= 'site_ids';
        $this->addDisplayGroup($group, 'tab_site_ids', array('label' => $this->getTranslator()->_('Веб-сайты')));       
    }
    
    private function _prepareSiteIds()
    {
        $list = array();
        $sites = Model_Service::factory('site')->getAll();
        foreach ($sites as $site) {
            $list[$site->id] = trim($site->host.'/'.$site->base_url, '/');
        }
        return $list;
    }
    
}
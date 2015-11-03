<?php

class Catalog_Form_AdminItem_Create extends App_Form
{
    
    public function init()
    {        
        $this->addElement('radio', 'is_configurable', array(
            'required' => TRUE,
        ));
        $this->is_configurable->addMultiOptions($this->_prepareOptionsIsConfigurable());
        
        $this->addElement('radio', 'is_downloadable', array(
            'required' => TRUE,
        ));
        $this->is_downloadable->addMultiOptions($this->_prepareOptionsIsDownloadable());        
        
        $this->setMethod('GET');
        $this->setAction($this->getView()->stdUrl(NULL, 'edit', 'admin-item_index', 'catalog'));                
    }
    
    protected function _prepareOptionsIsConfigurable()
    {        
        $translator = $this->getTranslator();
        return  array(
            0 => $translator->_('Обычный'), 
            1 => $translator->_('Составной'),
        );
    }
    
    protected function _prepareOptionsIsDownloadable()
    {        
        $translator = $this->getTranslator();
        return  array(
            0 => $translator->_('Обычный'), 
            1 => $translator->_('Цифровой (скачиваемый)'),
        );
    }
    
    
}
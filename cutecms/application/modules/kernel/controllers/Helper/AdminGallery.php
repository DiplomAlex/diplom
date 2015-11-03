<?php

class Controller_Action_Helper_AdminGallery extends Controller_Action_Helper_Content_Abstract
{
    const SESSION_NAMESPACE = 'AdminGallery';
    const EDITING_COLLECTION_PROPERTY = 'editingGallery';
    
    protected $_linkedCollectionPropertyName = self::EDITING_COLLECTION_PROPERTY;        
    
    protected $_sessionNamespaceName = self::SESSION_NAMESPACE;   

    protected $_defaultInjections = array(
        'Helper_Form_Edit' => 'Form_AdminGalleryEdit', 
    );
    
}
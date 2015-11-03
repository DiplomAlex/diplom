<?php

class Controller_Action_Helper_AdminTag extends Controller_Action_Helper_Content_Abstract
{

    protected $_linkedHelperName = 'Tag';
    
    protected $_linkedServiceName = 'tag';
    
    protected $_linkedCollectionPropertyName = AdminTagController::EDITING_COLLECTION_PROPERTY;        
    
    protected $_sessionNamespaceName = AdminTagController::SESSION_NAMESPACE;    
    
}


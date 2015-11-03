<?php

class Controller_Action_Helper_AdminComment extends Controller_Action_Helper_Content_Abstract
{

    const SESSION_NAMESPACE = 'AdminComment';
    const EDITING_COLLECTION_PROPERTY = 'editingComments';
    
    protected $_linkedCollectionPropertyName = self::EDITING_COLLECTION_PROPERTY;        
    
    protected $_sessionNamespaceName = self::SESSION_NAMESPACE;   

    protected $_defaultInjections = array();
    
    /**
     * sets sorting mode from array as ('field'=>'ASC', 'field2'=>'DESC', ...) or ('field1', 'field2', ...)
     * @param array $mode
     * @return $this
     */
    public function setSortingMode(array $mode)
    {
        $this->_getServiceHelper()->getService()->setSortingMode($mode);
        return $this;
    }
    
}
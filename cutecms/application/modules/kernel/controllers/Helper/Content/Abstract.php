<?php

class Controller_Action_Helper_Content_Abstract extends Zend_Controller_Action_Helper_Abstract
{
    
    /**
     * name of property containing collection of linked objects
     * @var string
     */
    protected $_linkedCollectionPropertyName = NULL;        
    
    /**
     * name of sessionNamespace of linked controller
     * @var unknown_type
     */
    protected $_sessionNamespaceName = NULL;
    
    /**
     * main controller's service helper
     * @var Model_Service_Helper_Interface
     */
    protected $_serviceHelper = NULL;

    /**
     * type of content main controller works with
     * @var string
     */
    protected $_contentType = NULL;
    
    /**
     * session of linked controller
     * @var Zend_Session_Namespace
     */
    protected $_session = NULL;
    
    /**
     * dependency injections container
     * @var App_DIContainer
     */
    protected $_injector = NULL;

    /**
     * @var array
     */
    protected $_defaultInjections = array(
        'Helper_Form_Edit',
    );
    
    
    public function direct(Model_Service_Helper_Interface $serviceHelper = NULL)
    {
        if ($serviceHelper !== NULL) {
            $this->setServiceHelper($serviceHelper);
        }
        return $this;
    }
    
    /* main operations public methods */
    
    public function clearLinked()
    {
        $this->_session()->{$this->_getLinkedCollectionPropertyName()} = $this->_getServiceHelper()->getLinkedService()->createCollection();
    }
    
    /**
     * gets collection of linked data to content
     * @param int $contentId
     * @return Model_Collection_Interface
     */
    public function loadLinked($contentId)
    {
        if ( ! $this->_getLinkedCollection()->count()) {
            $this->_session()->{$this->_getLinkedCollectionPropertyName()} = $this->_getServiceHelper()->getLinkedToContent($contentId);
        }
        return $this;
    }
    
    
    public function getLinked($contentId) 
    {
        $linkedColl = $this->_getServiceHelper()->getLinkedToContent($contentId);
        return $linkedColl;
    }
    
    /**
     * gets paginator of linked data to content
     * @param int $contentId
     * @return Zend_Paginator  
     */
    public function paginatorGetLinked($contentId, $rowsPerPage, $page)
    {
        $paginator = $this->_getServiceHelper()->paginatorGetLinkedToContent($contentId, $rowsPerPage, $page);
        return $paginator;        
    }
    
    /**
     * should be called when form object is created
     * @param Zend_Form form to extend
     * @return Zend_Form extended form (the same object)
     */
    public function extendEditForm(Zend_Form $form)
    {
        $els = $this->getInjector()->getObject('Helper_Form_Edit')->getElements();
        foreach ($els as $elName => $el) {
            $form->addElement($el, $elName);
        }
        return $form;
    }
    
    /**
     * should be called immediately after saving content
     * @param $content
     * @return $this
     */
    public function saveLinked(Model_Object_Interface $content)
    {
        $this->deleteLinked($content);
        $coll = $this->_getLinkedCollection();
        $svcHelper = $this->_getServiceHelper();
        $contentType = $svcHelper->getContentType();
        $service = $svcHelper->getLinkedService();
        $service->getMapper()->removePlugin('Resource'); 
        foreach ($coll as $item) {
            $item->id = NULL;
            $item->content_type = $contentType;
            $item->content_id = $content->id;
            $service->saveComplex($item);
        }
        return $this;
    }
    
    /**
     * should be called when deleting content
     * @param Model_Object_Interface $content
     * @return $this
     */
    public function deleteLinked(Model_Object_Interface $content)
    {
        $this->_getServiceHelper()->clearLinkedToContent($content->id);
        return $this;
    }
        
    
    /* getters and setters of properties that should be set in controllers init() */
    
    protected function _getServiceHelper()
    {
        if ($this->_serviceHelper === NULL) {
            $this->_throwException('serviceHelper was not inited in controller\'s init() method ($this->_helper->HelperName($serviceHelper)) ');
        }
        return $this->_serviceHelper;
    }
    
    public function setServiceHelper(Model_Service_Helper_Interface $serviceHelper)
    {
        $this->_serviceHelper = $serviceHelper;
        return $this;
    }
       
    /* getters of properties that should be set in helper\'s class definition */    
    
    protected function _getLinkedCollectionPropertyName()
    {
        if ($this->_linkedCollectionPropertyName === NULL) {
            $this->_throwException('$_linkedCollectionPropertyName should be set in class definition');
        }
        return $this->_linkedCollectionPropertyName;
    }
    
    protected function _getSessionNamespaceName()
    {
        if ($this->_sessionNamespaceName === NULL) {
            $this->_throwException('$_sessionNamespaceName should be set in class definition');
        }
        return $this->_sessionNamespaceName;
    }
        
    
    /* other methods */
    
    /**
     * @return Model_Collection_Interface
     */
    protected function _getLinkedCollection()
    {
        if ( ! $coll = $this->_session()->{$this->_getLinkedCollectionPropertyName()}) {
            $this->_session()->{$this->_getLinkedCollectionPropertyName()} = $this->_getServiceHelper()->getLinkedService()->createCollection();
            $coll = $this->_session()->{$this->_getLinkedCollectionPropertyName()};
        }
        return $coll;
    }

    protected function _session()
    {
        if ($this->_session === NULL) {
            $this->_session = new Zend_Session_Namespace($this->_getSessionNamespaceName());
        }
        return $this->_session;
    }
    
    public function getInjector()
    {
        if ($this->_injector === NULL) {
            $this->_injector = new App_DIContainer;
        }
        if (( ! $this->_injector->count()) AND count($this->_defaultInjections)) {
            foreach ($this->_defaultInjections as $iface=>$class) {
                $this->_injector->inject($iface, $class);
            }
        }
        return $this->_injector;
    }
    
    protected function _throwException($message)
    {
        $message = 'class '.get_class($this).' says: '.$message;
        throw new Zend_Controller_Action_Exception($message);
    }
    
}
<?php


/**
 * The purpose of this helper is to store and get currently selected site for administration
 * Example of using: 
 *     - in controller's indexAction 
 *           may be called $siteId = $this->getHelper('AdminMultisite')->getSiteId()
 *           for setting site id to service's helper: $service->getHelper('Multisite')->setCurrentSiteId($siteId)
 *           
 *     - in controller's editAction
 *           after form object created - $this->getHelper('AdminMultisite')->editFormExtend($form)
 *           when creating before populate - $values = $this->getHelper('AdminMultisite')->addDefaultSiteIdsValueToArray($values)
 *           when opening for edit -
 *                 if ($sites = $service->getHelper('Multisite')->getLinkedSites($values['id'])) {
 *                     $this->getHelper('AdminMultisite')->setSiteIdsValueToArray($values, $sites);
 *                 }
 *           when posted - $this->getHelper('AdminMultisite')->checkSiteIds($values);           
 *           when deleting - if ( ! $helperMulti->isAllowedMultisite() AND ! $helperMulti->isObjectLinkedToCurrentSite($object)) {disallow deleting}
 *       
 * @author ddv
 */

class Controller_Action_Helper_AdminMultisite extends Zend_Controller_Action_Helper_Abstract
{ 
    
    /**
     * name of request parameter where id of selected site is placed
     * @var string
     */
    protected $_paramNameSiteId = 'site_id';

    /**
     * name of user object field where linked site id presents
     * @var string
     */
    protected $_fieldNameUserSiteId = 'role_param1';
        
    /**
     * name of form element for selecting linked site_ids
     * @var string
     */
    protected $_formElementNameSiteIds = 'site_ids';
    
    /**
     * label of form element
     * @var string
     */
    protected $_formElementLabelSiteIds = 'Web-sites';
    
    /**
     * acl resource to check for
     * @var string
     */    
    protected $_aclResourceName = __CLASS__;

    /**
     * acl privilege to check for
     * @var string
     */    
    protected $_aclPrivilege = 'foreign_read';
    
    /**
     * session container
     * @var Zend_Session_Namespace
     */
    protected $_sessionNamespace = NULL;
    
    /**
     * currently logged in user
     * @var Model_Object_Interface
     */
    protected $_user = NULL;
    

    /**
     * session getter
     * @return Zend_Session_Namespace
     */
    protected function _session()
    {
        if ($this->_sessionNamespace === NULL) {
            $this->_sessionNamespace = new Zend_Session_Namespace(__CLASS__);
        }
        return $this->_sessionNamespace;
    } 
    
    /**
     * user getter
     * @return Model_Object_Interface
     */
    protected function _getUser()
    {
        if ($this->_user === NULL) {
            $this->_user = $this->_getUserService()->getCurrent();
        }
        return $this->_user;
    }
    
    /**
     * user service getter
     * @return Model_Service_Interface
     */
    protected function _getUserService()
    {
        return Model_Service::factory('user');
    }
    
    /**
     * @return string
     */
    public function getParamNameSiteId()
    {
        return $this->_paramNameSiteId;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setParamNameSiteId($value)
    {
        $this->_paramNameSiteId = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldNameUserSiteId()
    {
        return $this->_fieldNameUserSiteId;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setFieldNameUserSiteId($value)
    {
        $this->_fieldNameUserSiteId = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormElementNameSiteIds()
    {
        return $this->_formElementNameSiteIds;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setFormElementNameSiteIds($value)
    {
        $this->_formElementNameSiteIds = $value;
        return $this;
    }
    
    /**
     * @return string
     */    
    public function getFormElementLabelSiteIds()
    {
        return $this->getActionController()->view->translate($this->_formElementLabelSiteIds);        
    }
    
    /**
     * @param string $value
     * @return $this
     */
    public function setFormElementLabelSiteIds($value)
    {
        $this->_formElementLabelSiteIds = $value;
        return $this;
    }
    
    
    /**
     * @return string
     */
    public function getAclResourceName()
    {
        return $this->_aclResourceName;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setAclResourceName($value)
    {
        $this->_aclResourceName = $value;
        return $this;
    }
 
    /**
     * @return string
     */
    public function getAclPrivilege()
    {
        return $this->_aclPrivilege;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setAclPrivilege($value)
    {
        $this->_aclPrivilege = $value;
        return $this;
    }
    
    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        foreach ($options as $option=>$value) {
            $methodName = 'set'.ucfirst($option);
            if (property_exists($this, '_'.$option) AND (method_exists($this, $methodName))) {
                $this->{$methodName}($value);
            }
        }
        return $this;
    }
 
      
    /**
     * simple options setting
     * @param array $options
     * @return int current site id
     */
    public function direct(array $options = NULL)
    {
        if ($options != NULL) {
            $this->setOptions($options);
        }
        return $this->getSiteId();
    }
    
    /**
     * checks if user is allowed to selected sites to administrate
     * @return bool
     */
    public function isAllowedMultisite()
    {
        $result = $this->_getUserService()
                       ->isAllowedByAcl($this->_getUser(), $this->getAclResourceName(), $this->getAclPrivilege());
        return $result;
    }
    
    /**
     * returns id of currently selected site - selected by user or the only one allowed for current user
     * @return int
     */
    public function getSiteId()
    {
        $params = $this->getRequest()->getParams();
        $paramSiteId = $this->getParamNameSiteId();
        if (( ! $this->isAllowedMultisite()) AND ($this->_getUser())) {
            $this->_session()->siteId = $this->_getUser()->{$this->getFieldNameUserSiteId()};
        }
        else if (array_key_exists($paramSiteId, $params)) {
            $this->_session()->siteId = (int) $params[$paramSiteId];
        }
        return $this->_session()->siteId;
    }
    
    /**
     * add site_ids element to edit form according to user privileges
     * @param Zend_Form $form
     * @return Zend_Form
     */
    public function extendEditForm(Zend_Form $form)
    {
        $formElName = $this->getFormElementNameSiteIds();       
        if ($this->isAllowedMultisite()) {
            $form->addElement('multiCheckbox', $formElName, array(
                'label' => $this->getFormElementLabelSiteIds(),
            ));
            $form->{$formElName}->addMultiOptions($this->_getSiteIdsList());
        }
        /*else {
            $form->addElement('hidden', $formElName.'[]', array('id'=>$formElName));
        }*/
        return $form;
    }
    
    /**
     * get all sites as list for select  
     * @return array
     */
    protected function _getSiteIdsList()
    {
        $list = array();
        $sites = Model_Service::factory('site')->getAll();
        foreach ($sites as $site) {
            $list[$site->id] = $site->specification;
        }
        return $list;        
    }
        
    /**
     * get default value for field site_ids (usually when creating object)
     * @return mixed
     */
    public function getDefaultSiteIdsValue()
    {
        $result = array($this->getSiteId());
        $sites = Model_Service::factory('site')->getLinkedByDefault();
        foreach ($sites as $site) {
            $result[]=$site->id;
        }
        $result = array_unique($result);
        return $result;
    }
    
    /**
     * add site_ids default value to values array (before populating form when creating object)
     * @param array $values
     * @return array
     */
    public function addDefaultSiteIdsValueToArray(array &$values)
    {
        $values[$this->getFormElementNameSiteIds()] = $this->getDefaultSiteIdsValue();
        return $values;
    }

    /**
     * set site_ids default value to values array (before populating form when editing object)
     * @param array $values
     * @param mixed Model_Collection_Interface|FALSE
     * @return array
     */
    public function setSiteIdsValueToArray(array &$values, $sites)
    {
        $list = array();
        if ( ! empty($sites)) {
            foreach ($sites as $site) {
                $list[]=$site->id;
            }
        }        
        $values[$this->getFormElementNameSiteIds()] = $list;
        return $values;
    }
    
    /**
     * check for site_ids field existence in $values array 
     * @param array $values
     * @return array
     */
    public function checkSiteIds(array &$values)
    {
        $elName = $this->getFormElementNameSiteIds();
        if ( ! $this->isAllowedMultisite()) {
            if (array_key_exists($elName, $values)) {
                unset($values[$elName]);
            }
        }
        else if ( ! array_key_exists($elName, $values)) {
            $values[$elName] = array();
        }
        App_Debug::log($values);
        return $values;
    }
    
    /**
     * checks if object is linked to current site
     * @param Model_Object_Interface $obj
     * @return bool
     */
    public function isObjectLinkedToCurrentSite(Model_Object_Interface $obj)
    {
        $result = in_array($this->getSiteId(), $obj->{$this->getFormElementNameSiteIds()});
        return $result;
    }
    
    public function isObjectAllowedToDelete(Model_Object_Interface $obj)
    {
        $result = ($this->isAllowedMultisite()) OR ($this->isObjectLinkedToCurrentSite($obj));
        return $result;
    }
    
    
    /**
     * add multisiting to admin mass form if allowed
     * @return $this
     */
    public function extendMassForm()
    {
        $massHelper = $this->getActionController()->view->adminMassForm();
        if ($this->isAllowedMultisite()) {
            $massHelper->setMassActions(array('activate', 'deactivate', 'delete', 'linkToSite', 'unlinkFromSite'));
            $massHelper->setScriptActionsWithPath('admin-multisite/mass/actions.phtml');
        }
        return $this;
    }
    
}

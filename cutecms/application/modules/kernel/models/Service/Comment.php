<?php

class Model_Service_Comment extends Model_Service_Abstract 
{
    
    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Model_Object_Comment',
        'Model_Collection_Interface' => 'Model_Collection_Comment',
        'Model_Mapper_Interface' => 'Model_Mapper_Db_Comment',
    );
    
    /**
     * @var array(int=>string)
     */
    protected $_statusesList = array(
        'unapproved' => -1,
        'disabled' => 0,
        'enabled' => 1,
    );    
    
    public function createCollection()
    {
        return $this->getInjector()->getObject('Model_Collection_Interface');
    }
    
    
    public function createCommentFromValues(array $values)
    {
        $item = $this->create();
        foreach ($values as $key=>$val) {
            if ($item->hasElement($key)) {
                $item->{$key} = $val;
            }
        }
        return $item;
    }
    
    
    public function clearResource(Model_Object_Interface $obj)
    {
        if ($obj->rc_id) {
            $this->getMapper()->deleteResource($obj->rc_id);
        }
        return $this;
    }
    
    public function setResourceFromRequest(Model_Object_Interface $obj)
    {
        $this->getMapper()->setResourceFromRequest($obj);
    }
    
    /**
     * get collection containing all parents of current object
     * @param int object's id
     * @param bool wether include current object itself to collection
     * @return Model_Collection_Interface
     */
    public function getParentsOf($id, $includeSelf = FALSE)
    {
        if ( ! $id) {
            return $this->getInjector()->getObject('Model_Collection_Interface');
        }
        else {
            return $this->getMapper()->fetchComplexParentsOf($id, $includeSelf);
        }
    }
    
    public function getParentIds($id)
    {
        return $this->getMapper()->fetchParentIds($id);
    } 
    
    
    public function addFromValues(array $values)
    {
        $userService = Model_Service::factory('user');
        if ($userService->isAuthorized()) {
            $user = $userService->getCurrent();
            $values['adder_name'] = $user->name;
            $values['adder_email'] = $user->email;
        }
        $values['status'] = -1;
        if (empty($values['adder_name'])) {
            $this->_throwException('trying to add comment without author name');
        }
        $this->saveFromValues($values);
        return $this;
    }
    
}
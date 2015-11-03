<?php

class Model_Service_Gallery extends Model_Service_Abstract 
{
    
    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Model_Object_GalleryItem',
        'Model_Collection_Interface' => 'Model_Collection_GalleryItem',
        'Model_Mapper_Interface' => 'Model_Mapper_Db_GalleryItem',
        'Model_Service_Language',
    );
    
    public function init()
    {
        $lang = $this->getInjector()->getObject('Model_Service_Language');
        $this->getMapper()->getPlugin('Description')->setLanguages($lang->getAllActive())->setCurrentLanguage($lang->getCurrent());
    }
    
    public function createCollection()
    {
        return $this->getInjector()->getObject('Model_Collection_Interface');
    }
    
    
    public function createGalleryItemFromValues(array $values)
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
    
    
}
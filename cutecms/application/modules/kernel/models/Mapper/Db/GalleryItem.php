<?php

class Model_Mapper_Db_GalleryItem extends Model_Mapper_Db_ContentLinkable_Abstract
{
    
    protected $_refTableLinkedField = 'gallery_id';    
    
    protected $_relationMode = Model_Mapper_Db_ContentLinkable_Interface::RELATION_ONE_TO_MANY;
    
    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Model_Object_GalleryItem',
        'Model_Collection_Interface' => 'Model_Collection_GalleryItem',  
        'Model_Db_Table_Interface' => 'Model_Db_Table_GalleryItem',
        'Model_Mapper_Db_Plugin_Description',
        'Model_Db_Table_Description' => 'Model_Db_Table_GalleryItemDescription',
        'Model_Mapper_Db_Plugin_Resource',
        'Model_Db_Table_Resources',
    );
    
    public function init()
    {
        $this->addPlugin(
            'Description',
            $this ->getInjector()
                  ->getObject(
                    'Model_Mapper_Db_Plugin_Description',
                    array(
                        'mapper' => $this,
                        'table' => $this->getInjector()->getObject('Model_Db_Table_Description'),
                        'refColumn' => 'gallery_id',
                        'descFields' => array(
                            'name', 'description', 
                            /*'html_title', 'meta_keywords', 'meta_description',*/
                        ),
                    )
                  )
        )
        ->addPlugin('Resource',$this->getInjector()->getObject(
            'Model_Mapper_Db_Plugin_Resource', 
            array('rc_id'),
            Zend_Registry::get('config')->images->previewMaxCount 
        ))
        ;
        if ($config = Zend_Registry::get('config')->images->previewDimensions->{$this->getInjector()->getInjection('Model_Object_Interface')}) {
            $this->getPlugin('Resource')->setPreviewDimensions($config->toArray());
        }                                
    }
    
    public function deleteResource($rcId)
    {
        $table = $this->getInjector()->getObject('Model_Db_Table_Resources');
        $table->delete(array('rc_id = ?' => $rcId));
        return $this;
    }
    
    public function setResourceFromRequest(Model_Object_Interface $obj)
    {
        $this->getPlugin('Resource')->saveUploadedResource($obj, array('resource_rc_id_del'=>FALSE));
    }
    
    
   /**
     * set correct row values
     */
    protected function _prepareResourceRow(Zend_Db_Table_Row_Abstract $row, array $file = NULL, array $previews = NULL)
    {
        $mapArray = array(
            $this->getTable()->getColumnPrefix().'_filename' => $file['filename'],
            $this->getTable()->getColumnPrefix().'_source_filename' => $file['name'],
            $this->getTable()->getColumnPrefix().'_mime' => $file['type'],
            $this->getTable()->getColumnPrefix().'_width' => $file['width'],
            $this->getTable()->getColumnPrefix().'_height' => $file['height'],
            $this->getTable()->getColumnPrefix().'_size' => $file['size'],
        );
        foreach ($previews as $key=>$preview) {
            $suffix = $this->_getPreviewSuffix($key+1);
            $mapArray[$this->getTable()->getColumnPrefix().'_preview'.$suffix] = $preview['filename'];
            $mapArray[$this->getTable()->getColumnPrefix().'_prv'.$suffix.'_width'] = $preview['width'];
            $mapArray[$this->getTable()->getColumnPrefix().'_prv'.$suffix.'_height'] = $preview['height'];
        }
        $row->setFromArray($mapArray);
    }
    

    

    
}
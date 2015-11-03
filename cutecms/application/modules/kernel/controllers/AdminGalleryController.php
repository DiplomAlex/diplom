<?php

class AdminGalleryController extends Zend_Controller_Action
{ 
    
    protected $_session = NULL;
    
    public function init()
    {
        App_Event::factory('AdminController__init', array($this))->dispatch();
        
        /**
         * controller is fully for ajax requests - no layout or action script rendering needed 
         */
        $this->view->layout()->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();
    }
    
    protected function _getService()
    {
        return Model_Service::factory('gallery');
    }
    
    protected function _session()
    {
        if ($this->_session === NULL) {
            $this->_session = new Zend_Session_Namespace(Controller_Action_Helper_AdminGallery::SESSION_NAMESPACE);
        }
        return $this->_session;
    }
    
    protected function _getEditingGallery()
    {
        $prop = Controller_Action_Helper_AdminGallery::EDITING_COLLECTION_PROPERTY;
        if ( ! $this->_session()->{$prop}) {
            $this->_session()->{$prop} = $this->_getService()->createCollection();
        }
        return $this->_session()->{$prop};
    }
    
    /**
     * get all rows for jqGrid or single row for editing form
     * @return string json  
     */
    public function ajaxGetAction()
    {
        $rowId = $this->_getParam('id');
        if (empty($rowId)) {
            $rows = array();
            foreach ($this->_getEditingGallery() as $row) {
                $rows[] = array(
                    'id' => $row['hash'],
                    'cell' => array($row['status'], $row['name'], $row['description'], $this->_formatItemToHmtl($row),),
                );
            }
            $answer = array(
                'page' => max( (int) $this->_getParam('page'), 1),
                'total' => $this->_getEditingGallery()->count(),
                'rows' => $rows,
            );
        }
        else {
            $answer = array();
            foreach ($this->_getEditingGallery() as $key=>$item) {
                if ($item['hash'] == $rowId) {
                    $statuses = $this->_getService()->getAllStatuses(TRUE);
                    $answer = array(
                        'status'        => $statuses[$item['status']],
                        'name'          => (string) $item['name'],
                        'description'   => (string) $item['description'],
                        'item'          => $this->_formatItemToHmtl($item),
                    );                    
                    break;
                }
            }
        }
        echo Zend_Json::encode($answer);        
    }
    
    public function ajaxEditAction()
    {
        $rowId = $this->_getParam('id');
        $values = $this->getRequest()->getParams();
        $duplicate = FALSE;
        $found = FALSE;
        foreach ($this->_getEditingGallery() as $item) {
            if ($item['hash'] == $rowId) {
                $item->status = $values['gallery_status'];
                $item->name = $values['name'];
                $item->description = $values['description'];
                $found = TRUE;
                break;
            }
        }
        if ( ! $found) {
            $new = $this->_getService()->createGalleryItemFromValues(array(
                'status' => $values['gallery_status'],
                'name' => $values['name'],
                'description' => $values['description'],
            ));
            $this->_getEditingGallery()->add($new);
        }
        $answer = array(
            'name' => $values['name'],
            'status' => $values['gallery_status'],
            'description' => $values['description'],
        );
        if ( ! $found) {
            $answer['id'] = $new['hash'];
        }
        else {
            $answer['id'] = $item['hash'];
        }
        echo Zend_Json::encode($answer);
    }
    
    public function ajaxDeleteAction()
    {
        $rows = $this->_getParam('rows');
        foreach ($rows as $rowId) {
            foreach ($this->_getEditingGallery() as $key=>$item) {
                if ($item['hash'] == $rowId) {
                    $this->_getEditingGallery()->remove($key);
                }
            }
        }
        echo 'ok';        
    }
    
    public function ajaxUploadAction()
    {
        $hash = $this->_getParam('id');
        if ($hash == 'new_row') {
            $item = $this->_getService()->create();            
        }
        else {
            $item = $this->_getEditingGallery()->findOneByHash($hash);
        }
        $item->status = $this->_getParam('status', 0);
        $item->name = $this->_getParam('name');
        $item->description = $this->_getParam('description');
        $this->_getService()->setResourceFromRequest($item);
        if ($hash == 'new_row') {
            $this->_getEditingGallery()->add($item);
        }
    }

    
    /**
     * cretae html for gallery item - show preview for image, or link for other mime typs
     * @param Model_Object_Interface $item
     * @return string $html
     */
    protected function _formatItemToHmtl(Model_Object_Interface $item)
    {
        $mimeArr = explode('/', $item->rc_id_mime);
        $rcType = strtolower($mimeArr[0]);
        if (($rcType == 'image') OR (App_Resource::isImage($item->rc_id_filename))) {
            $html = $this->view->html_Img($item->rc_id_preview, 'border="0"', $item->rc_id_filename);
        }
        else {
            $text = $item->rc_id_source_filename?$item->rc_id_source_filename:basename($item->rc_id_filename);
            $html = '<a href="'.App_Resource::getUploadsUrl($item->rc_id_filename).'">'.$text.'</a>';
        }
        return $html;
    }
    
}
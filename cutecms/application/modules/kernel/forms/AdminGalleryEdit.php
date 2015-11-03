<?php

class Form_AdminGalleryEdit extends App_Form
{
    
    public function init()
    {
        $gallery = $this->createElement('jqGrid', 'gallery')
                        ->setLabel($this->getTranslator()->_('Галерея'))
                        ->setRequired(FALSE)
                        ->setAttribs(array(
                                    'width' => 720,
                                    'height' => 300,
                                    'nopager' => TRUE,
                                    'colModel' => $this->_prepareColModel(),
                                    'colNames' => $this->_prepareColNames(),
                                    'url' => $this->_prepareUrlGet(),
                                    'editurl' => $this->_prepareUrlEdit(),
                                    'deleteurl' => $this->_prepareUrlDelete(),
                                    'addonJs' => $this->_prepareAddonJs(array('deleteurl'=>$this->_prepareUrlDelete())),
                                    'onSelectRow' => $this->_prepareOnSelectRowJs(), 
                                    'noStandartButtons' => TRUE,
                        ))
                        ;
        $this->addElement($gallery);
    }
    
    protected function _prepareColModel()
    {
        $colModel = array(
            array(
                'index' => 'gallery_status', 'name' => 'gallery_status',
                'sortable' => TRUE, 'editable' => TRUE, 'edittype' => 'select',
                'editoptions' => array('value' => array(
                    0 => $this->getTranslator()->_('Выкл'),
                    1 => $this->getTranslator()->_('Вкл'),
                )),
                'width' => '60px',
            ),
            array(
                'index' => 'name', 'name' => 'name',
                'sortable' => TRUE, 'editable' => TRUE, 
                'width' => '150px',
            ),
            array(
                'index' => 'description', 'name' => 'description',
                'sortable' => TRUE, 'editable' => TRUE, 'edittype' => 'textarea',
                'width' => '200px',
            ),
            array(
                'index' => 'item', 'name' => 'item',
                'sortable' => FALSE, 'editable' => FALSE,
                'width' => '100px',
            ),
            array(
                'index' => 'upload', 'name' => 'upload',
                'sortable' => FALSE, 'editable' => TRUE, 'edittype' => 'button',
                'width' => '60px', 'align' => 'center', 'fixed' => TRUE,
            ),
        );
        return $colModel;
    }
    
    protected function _prepareColNames()
    {
        $tr = $this->getTranslator();
        $colNames = array(
            $tr->_('Статус'),
            $tr->_('Название'),
            $tr->_('Описание'),
            $tr->_('Объект'),
            '',
        );
        return $colNames;
    }
    
    protected function _prepareUrlGet()
    {
        $url = $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-get', 'admin-gallery');
        return $url;
    }
    
    protected function _prepareUrlEdit()
    {
        $url = $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-edit', 'admin-gallery');
        return $url;
    }
    
    protected function _prepareUrlDelete()
    {
        $url = $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-delete', 'admin-gallery');
        return $url;
    }
    
    protected function _prepareAddonJs(array $params = array())
    {
        $js = $this->getView()->partial('admin-gallery/grid-addon.js.phtml', array('params'=>$params));        
        return $js;
    }
    
    protected function _prepareOnSelectRowJs()
    {
        $js = $this->getView()->partial('admin-gallery/on-select-row.js.phtml', array());        
        return $js;
    }
      
}
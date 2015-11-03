<?php

class Catalog_Form_AdminItem_Bundle_Tab extends Catalog_Form_AdminItem_Abstract
{
    
    public function init()
    {


        $bundles = $this->createElement('jqGrid', 'bundles')
                        ->setLabel($this->getTranslator()->_('Вложенные товары'))
                        ->setRequired(FALSE)
                        ->setAttribs(array(
                                    'width' => 842,
                                    'height' => 300,
                                    'nopager' => TRUE,
                                    'colModel' => $this->_prepareBundlesColModel(),
                                    'colNames' => $this->_prepareBundlesColNames(),
                                    'url' => $this->_prepareBundlesUrlGet(),
                                    'editurl' => $this->_prepareBundlesUrlEdit(),
                                    'deleteurl' => $this->_prepareBundlesUrlDelete(),
                                    'subGrid' => TRUE,
                                    'subGridUrl' => $this->_prepareSubitemsUrlGet(),
                                    'subGridEditUrl' => $this->_prepareSubitemsUrlEdit(),
                                    'subGridDeleteUrl' => $this->_prepareSubitemsUrlDelete(),
                                    'subGridColNames' => $this->_prepareSubitemsColNames(),
                                    'subGridColModel' => $this->_prepareSubitemsColModel(),
                                    'addonJs' => $this->_prepareBundlesAddonJs(),
                        ))
                        ;
        $this->addElement($bundles);



        $items = $this->createElement('jqGrid', 'items_for_sub')
                        ->setAttribs(array(
                                    'width' => 642,
                                    'height' => 300,
                                    'colModel' => $this->_prepareItemsColModel(),
                                    'colNames' => $this->_prepareItemsColNames(),
                                    'url' => $this->_prepareItemsUrlGet(),
                                    'addonJs' => $this->_prepareItemsAddonJs(),
                                    'noStandartButtons' => TRUE,
                                    'pgbuttons' => 'true',
                                    'pginput' => 'true',
                                    'onSelectRow' => $this->_prepareItemsOnSelectRowJs(),
                        ))
                        ;
        $this->addElement($items);


        
        
        
        /* bundles */
        $bundleGroup = array();
        $bundleGroup []= 'bundles';
        $this->addDisplayGroup($bundleGroup, 'tab_bundles', array('label' => $this->getTranslator()->_('Вложенные товары')));

        
        $this->_appendTabAddonJs();
    }

    /********************* bundles ****************************************/
    
    
    protected function _prepareBundlesColModel()
    {
        $model = $this->_getGridsConfig('bundles')->colModel->toArray();
        $service = Model_Service::factory('catalog/item-bundle');
        foreach ($model as $idx=>$col) {
            if ($col['name'] == 'status') {
                $model[$idx]['editoptions'] = array('value'=>$service->getAllStatuses(TRUE));
            }
            if ($col['name'] == 'is_required') {
                $model[$idx]['editoptions'] = array('value'=>array(0=>$this->getTranslator()->_('нет'), 1=>$this->getTranslator()->_('да')));
            }
        }
        return $model;
        
    }


    protected function _prepareBundlesColNames()
    {
        return $this->_getGridsConfig('bundles')->colNames->toArray();
    }


    protected function _prepareBundlesUrlGet()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-get', 'admin-item_bundle', 'catalog');
    }

    protected function _prepareBundlesUrlEdit()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-edit', 'admin-item_bundle', 'catalog');
    }

    protected function _prepareBundlesUrlNew()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-new', 'admin-item_bundle', 'catalog');
    }

    protected function _prepareBundlesUrlDelete()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-delete', 'admin-item_bundle', 'catalog');
    }


    protected function _prepareBundlesAddonJs()
    {
        return $this->getView()->partial('admin-item/bundle/addon.js.phtml', array());
    }
    
    
    /******************* subitems *****************************************************/
  
    protected function _prepareSubitemsUrlGet()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-get-subitem', 'admin-item_bundle', 'catalog');
    }

    protected function _prepareSubitemsUrlEdit()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-edit-subitem', 'admin-item_bundle', 'catalog');
    }


    protected function _prepareSubitemsUrlDelete()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-delete-subitem', 'admin-item_bundle', 'catalog');
    }


    protected function _prepareSubitemsColModel()
    {
        $model = $this->_getGridsConfig('subitems')->colModel->toArray();
        foreach ($model as $idx=>$col) {
            if ($col['name'] == 'spec_as_html') {
                $model[$idx]['editoptions'] = array('readonly'=>TRUE);
            }
        }
        return $model;
        
    }


    protected function _prepareSubitemsColNames()
    {
        return $this->_getGridsConfig('subitems')->colNames->toArray();
    }



    
    /****************************  items  *********************************/
    
    public function _prepareItemsColModel()
    {
        $model = $this->_getGridsConfig('items')->colModel->toArray();
        return $model;
    }
    
    public function _prepareItemsColNames()
    {
        return $this->_getGridsConfig('items')->colNames->toArray();
    }
    
    public function _prepareItemsUrlGet()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-get-items', 'admin-item_bundle', 'catalog');
    }
    
    public function _prepareItemsAddonJs()
    {
        $js = $this->getView()->partial('admin-item/bundle/items-addon.js.phtml', array());
        return $js;
    }
    
    public function _prepareItemsOnSelectRowJs()
    {
        $js = $this->getView()->partial('admin-item/bundle/items-on-dblclick-row.js.phtml', array());
        return $js;        
    }
    
    public function _appendTabAddonJs()
    {
        $js = $this->getView()->render('admin-item/bundle/tab-addon.js.phtml');
        $this->getView()->headScript(Zend_View_Helper_HeadScript::SCRIPT, $js);
        return $js;
    }
    
}
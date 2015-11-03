<?php

class Catalog_Form_AdminItem_Xsell_Tab extends Catalog_Form_AdminItem_Abstract
{
    
    public function init()
    {

        $xsells = $this->createElement('jqGrid', 'xsells')
                       ->setLabel($this->getTranslator()->_('Сопутствующие товары'))
                       ->setRequired(FALSE)
                       ->setAttribs(array(
                                    'width' => 642,
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
                                    'onSelectRow' => $this->_prepareBundlesOnSelectRowJs(),
                       ))
                       ;
        $this->addElement($xsells);

        /* xsells */
        $xsellGroup = array();
        $xsellGroup []= 'xsells';
        $this->addDisplayGroup($xsellGroup, 'tab_xsells', array('label' => $this->getTranslator()->_('Сопутствующие товары')));

                
    }

    /********************* xsells ****************************************/
    
    
    protected function _prepareXsellsColModel()
    {
        /*
         * для сложной модели удобнее делать сразу js (для простых - grids.xml)
         */
        $service = Model_Service::factory('catalog/item');
        $model = $this->getView()->partial('admin-item-bundle/xsells/colmodel.js.phtml', array(
            'statusOptionsJson' => Zend_Json::encode($service->getAllStatuses(TRUE)),
            'typeOptionsJson' => Zend_Json::encode($service->getAllTypes(TRUE)),
        ));
        return $model;
    }


    protected function _prepareXsellsColNames()
    {
        return $this->_getGridsConfig('xsells')->colNames->toArray();
    }


    protected function _prepareXsellsUrlGet()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-get-xsell', 'admin-item-bundle', 'catalog');
    }

    protected function _prepareBundlesUrlEdit()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-edit-xsell', 'admin-item-complex', 'catalog');
    }

    protected function _prepareBundlesUrlNew()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-new-attribute', 'admin-item-complex', 'catalog');
    }

    protected function _prepareBundlesUrlDelete()
    {
        return $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-delete-attribute', 'admin-item-complex', 'catalog');
    }


    protected function _prepareBundlesAddonJs()
    {
        return $this->getView()->partial('admin-item-bundle/bundles/addon.js.phtml', array(
            'formGetFromGroups'=> new Catalog_Form_AdminItemAttributeGetFromGroups,
            'urlGetFromGroups'=>$this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-get-attribute-from-groups', 'admin-item-complex', 'catalog'),

            'formAddToExistGroups'=> new Catalog_Form_AdminItemAttributeAddToExistGroups,
            'urlAddToExistGroups'=>$this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-add-attribute-to-exist-groups', 'admin-item-complex', 'catalog'),

            'formAddToNewGroup'=> new Catalog_Form_AdminItemAttributeAddToNewGroup,
            'urlAddToNewGroup'=>$this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-add-attribute-to-new-group', 'admin-item-complex', 'catalog'),

            'urlGetGroups' => $this->getView()->stdUrl(array('reset'=>TRUE), 'ajax-get-attribute-groups', 'admin-item-complex', 'catalog'),
        ));
    }


    protected function _prepareBundlesOnSelectRowJs()
    {
        return $this->getView()->render('admin-item-bundle/bundles/on-select-row.js.phtml');
    }

        
    
}
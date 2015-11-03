<?php

class Catalog_View_Helper_AdminCategoryMassForm extends App_View_Helper_MassForm_Abstract
{

    protected $_massFormHelper = 'adminMassForm';
    protected $_scriptsPath = 'admin-index/mass';

    public function adminCategoryMassForm($type = NULL, $params = NULL)
    {
        return $this->massForm($type, $params);
    }

    protected function _formAction($params = NULL)
    {
        return $this->view->stdUrl(array('parent'=>$params['parent']), $this->_controllerAction);
    }


}
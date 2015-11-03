<?php

class Shop_Observer_Controller extends App_Event_Observer
{
    
   public function initAction()
   {
       $controller = $this->getData(0);
       $module = $controller->getRequest()->getParam('module');
       $this->_initLocale($module);
       $this->_initViewPaths($controller->view, $module);
       $this->_initLayoutJsHelpers($controller->view);
   }
   
   protected function _initLocale($module)
   {
        $code2 = Model_Service::factory('language')->getCurrent()->code2;        
        $filename = FRONT_APPLICATION_PATH . '/i18n/'.$code2.'/'.$module.'.mo';
        if (file_exists($filename)) {
            Zend_Registry::get('Zend_Translate')
                         ->addTranslation($filename);
        }       
   }
   
   protected function _initViewPaths(Zend_View_Interface $view, $module)
   {
       $moduleUcfirst = ucfirst($module);
       $view->layout()->setLayoutPath(FRONT_APPLICATION_PATH . '/modules/'.$module.'/views/layouts');
       $view->setBasePath(APPLICATION_PATH . '/modules/kernel/views', 'View_');
       $view->addBasePath(APPLICATION_PATH . '/modules/catalog/views', 'Catalog_View_');
       $view->addBasePath(APPLICATION_PATH . '/modules/checkout/views', 'Checkout_View_');
       $view->addBasePath(FRONT_APPLICATION_PATH . '/modules/'.$module.'/views', $moduleUcfirst.'_View_');

       $view->setHelperPath(APPLICATION_PATH . '/library/App/View/Helper','App_View_Helper_');
       $view->addHelperPath(APPLICATION_PATH . '/modules/kernel/views/helpers','View_Helper_');
       $view->addHelperPath(APPLICATION_PATH . '/modules/catalog/views/helpers','Catalog_View_Helper_');
       $view->addHelperPath(APPLICATION_PATH . '/modules/checkout/views/helpers','Checkout_View_Helper_');
       $view->addHelperPath(FRONT_APPLICATION_PATH . '/modules/'.$module.'/views/helpers',$moduleUcfirst.'_View_Helper_');              
   }
   
   protected function _initLayoutJsHelpers(Zend_View_Interface $view)
   {
       /* is unnecessary here ?
        * $view->layout()->box_Login = $view->box_Login();
        * $view->layout()->box_Language = $view->box_Language();
        * */
       $view->layout()->box_ShoppingCart = $view->box_ShoppingCart();
   }
    
}
<?php

class Shop_View_Helper_Box_Footer extends Zend_View_Helper_Abstract
{

    public function box_Footer()
    {
        $menu = Model_Service::factory('banner')->getAllByPlace('footer_menu');	
        $this->view->menu = $menu[0];
        
        return $this->view->render('box/footer.phtml');
    }

}
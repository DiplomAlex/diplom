<?php

class Catalog_Form_AdminItem_Abstract extends App_Form
{
    
    protected $_defaultInjections = array();
    
    protected $_injector = NULL;
    
    public function getInjector()
    {
        if ($this->_injector === NULL) {
            $this->_injector = new App_DIContainer;
            foreach ($this->_defaultInjections as $iface=>$class) {
                $this->_injector->inject($iface, $class);
            }
        }
        return $this->_injector;
    }

    protected function _getGridsConfig($section)
    {
        $class = get_class($this);
        if ( ! $conf = Model_Service::factory('config')->read('catalog/grids.xml', $class)->{$section}) {
            throw new Catalog_Form_AdminItem_Exception('grids.xml has no requested section "'.$class.'" (check for grids.xml format and form class name)');
        }        
        return $conf;
    }
    
    
}
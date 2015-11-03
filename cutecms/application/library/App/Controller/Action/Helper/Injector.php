<?php

/**
 * adds injector to controller action
 * 
 * using: in controller's init add $this->_invokeArgs['defaultInjections'] = $this->_defaultInjections
 * (protected $_defaultInjections = array('MyIface' => 'MyClass',) should be added before) 
 * after that use as usually - $this->_helper->Injector()->getObject('MyIface')
 */

class App_Controller_Action_Helper_Injector extends Zend_Controller_Action_Helper_Abstract
{
    
    const DEFAULT_INJECTIONS_INVOKE_ARG = 'defaultInjectons';
    
    protected $_injector = NULL;
    
    public function getInjector()
    {
        if ($this->_injector === NULL) {
            $this->_injector = new App_DIContainer;
            $injs = $this->getActionController()->getInvokeArg(self::DEFAULT_INJECTIONS_INVOKE_ARG);
            $this->injectArray($injs);
        }
        return $this->_injector;        
    }
    
    public function injectArray(array $injs = NULL)
    {
        if ($injs) {
            foreach ($injs as $interface => $class) {
                $this->getInjector()->inject($interface, $class);
            }
        }        
    }
    
    public function direct(array $injs = NULL)
    {
        $this->injectArray($injs);
        return $this->getInjector();
    }
    
}


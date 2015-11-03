<?php

/**
 * container of dependency injections
 */

require_once 'App/DIContainer/Exception.php';
require_once 'App/DIContainer/Interface.php';

class App_DIContainer implements App_DIContainer_Interface, Countable
{

	/**
	 * array of
	 */
	protected $_injections = array();

	/**
	 * @param string name of interface to be replaced
	 * @param string name of class that interface is replaced by
	 * @return App_DIContainer_Interface $this
	 */
	public function inject($interface, $class)
	{
	    if ( ! is_numeric($interface)) {
	       $interface = trim($interface);
	    }
	    $class = trim($class);
		if ( ! class_exists($class)) {
			$this->_throwException('injection of unknow stuff "'.$class.'"! ('.$interface.' => '.$class.') ');
		}
		$this->_injections[$interface] = $class;
		return $this;
	}

    /**
     * remove injection record
     * @param string
     * @return $this
     */
    public function uninject($interface)
    {
        if (isset($this->_injections[$interface])) {
            unset($this->_injections[$interface]);
        }
        else if (($searchKey = array_search($interface, $this->_injections)) AND (is_numeric($searchKey))) {
            unset($this->_injections[$searchKey]);
        }
        else {
            $this->_throwException('not injected yet '.$interface.' (from '.__FUNCTION__.')');
        }
        return $this;
    }

    /**
     * checks if injection for interface was inited
     * @param string
     * @return bool
     */
    public function hasInjection($interface)
    {
        if (array_key_exists($interface, $this->_injections)) {
            $result = TRUE;
        }
        else if (($searchKey = array_search($interface, $this->_injections)) AND (is_numeric($searchKey))) {
            $result = TRUE;
        }
        else {
            $result = FALSE;
        }
        return $result;
    }


    public function getInjectionKey($class)
    {
        if (is_object($class)) {
            $class = (string) $class;
        }
        if ($this->hasInjection($class)) {
            $result = $class;
        }
        else if ($interface = array_search($class, $this->_injections)) {
            $result = $interface;
        }
        else {
            $this->_throwException(' the class "'.$class.'" not injected yet by any interface');
        }
        return $result;
    }
    
    
    public function getInjection($interface)
    {
    	if (array_key_exists($interface, $this->_injections)) {
    		$class = $this->_injections[$interface];
    	}
    	else {
            $searchKey = array_search($interface, $this->_injections);
            if (($searchKey === FALSE) OR ( ! is_numeric($searchKey))) {
                $this->_throwException('not injected yet: '.$interface.' (detected in '.__FUNCTION__.')');
            }
            else {
                $class = $interface;
            }    		
    	}
        return $class;
    }
    


	/**
	 * @param requested interface of
	 * @param1,...@paramN - parameters to constructor
	 */
	public function getObject()
	{
		$args = func_get_args();
		if (empty($args)) {
			$this->_throwException('wrong call of method - interface should be as 1st parameter');
		}

		$interface = array_shift($args);
		
		$class = $this->getInjection($interface);

        /*
        App_Profiler::start('App_DIContainer__getObject::reflection');
        $reflectionObj = new ReflectionClass($class);
        $object = $reflectionObj->newInstanceArgs($args);
        App_Profiler::stop('App_DIContainer__getObject::reflection');
        */


        /**
         * using of ReflectionClass is still slow nowadays - so it was replaced with ugly, but fast code,
         * which is several times faster and on some pages allows to ecomon about 10% of execution time
         *
         */
        $argsCnt = count($args);
        if ($argsCnt == 0) {
            $object = new $class;
        }
        else if ($argsCnt == 1) {
            $object = new $class($args[0]);
        }
        else if ($argsCnt == 2) {
            $object = new $class($args[0], $args[1]);
        }
        else if ($argsCnt == 3) {
            $object = new $class($args[0], $args[1], $args[2]);
        }
        else if ($argsCnt == 4) {
            $object = new $class($args[0], $args[1], $args[2], $args[3]);
        }
        else if ($argsCnt == 5) {
            $object = new $class($args[0], $args[1], $args[2], $args[3], $args[4]);
        }
        else if ($argsCnt == 6) {
            $object = new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
        }
        else if ($argsCnt == 7) {
            $object = new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]);
        }
        else if ($argsCnt == 8) {
            $object = new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7]);
        }
        else if ($argsCnt == 9) {
            $object = new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8]);
        }
        else if ($argsCnt == 10) {
            $object = new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9]);
        }
        else if ($argsCnt == 11) {
            $object = new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9], $args[10]);
        }
        else if ($argsCnt == 12) {
            $object = new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9], $args[10], $args[11]);
        }
        else if ($argsCnt == 13) {
            $object = new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9], $args[10], $args[11], $args[12]);
        }
        else if ($argsCnt == 14) {
            $object = new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9], $args[10], $args[11], $args[12], $args[13]);
        }
        else if ($argsCnt == 15) {
            $object = new $class($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7], $args[8], $args[9], $args[10], $args[11], $args[12], $args[13], $args[14]);
        }
        else {
    	   $this->_throwException('it looks like you use more than 15 arguments when trying to instantiate an object of class '.$class.' - that\'s ugly');
        }

        return $object;
	}
	
	/**
	 * @see Countable::count()
	 */
	public function count()
	{
	    return count($this->_injections);
	}

    protected function _throwException($message)
    {
        $backtrace = debug_backtrace(FALSE);
        foreach ($backtrace as $n=>$arr) {
            if ($arr['file'] != realpath(__FILE__)) {
                break;
            }
        }
        $res = array();
        foreach ($backtrace as $n=>$arr) {
            $res[] = array($arr['file'], $arr['line']);
        }
        $n = 2;
        $filename = $backtrace[$n]['file'];
        $line = $backtrace[$n]['line'];
        App_Debug::dump($res, 'backtrace');
        $message .= ' Was occured in "'.$filename.'" at line '.$line .' (the "Dump" tab of ZFDebugPanel contains more backtrace info!)';
        throw new App_DIContainer_Exception(__CLASS__.' says: ' . $message);
    }

}
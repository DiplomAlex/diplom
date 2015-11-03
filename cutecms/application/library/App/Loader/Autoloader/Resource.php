<?php

class App_Loader_Autoloader_Resource extends Zend_Loader_Autoloader_Resource
{


    /**
     * cache of classes paths
     * @var array  (className => fullPath, ...)
     */
    protected static $_cache = NULL;

    /**
     * load previously saved cache of classes paths
     * @param string
     */
    public static function loadCache($filename)
    {
        @self::$_cache = unserialize(file_get_contents($filename));
    }

    /**
     * save cache
     * @param string
     */
    public static function saveCache($filename)
    {
        file_put_contents($filename, serialize(self::$_cache));
    }


    /**
     * Attempt to autoload a class
     *
     * @param  string $class
     * @return mixed False if not matched, otherwise result if include operation
     */
    public function autoload($class)
    {

        if (isset(self::$_cache[$class])) {
            /*echo $class.' | ';*/
            return include self::$_cache[$class];
        }

        $segments          = explode('_', $class);
        $namespaceTopLevel = $this->getNamespace();
        $namespace         = '';

        if (!empty($namespaceTopLevel)) {
            $namespace = array_shift($segments);
            if ($namespace != $this->getNamespace()) {
                // wrong prefix? we're done
                return false;
            }
        }

        if (count($segments) < 2) {
            // assumes all resources have a component and class name, minimum
            return false;
        }

        $final     = array_pop($segments);
        $component = $namespace;
        $lastMatch = false;
        do {
            $segment    = array_shift($segments);
            $component .= empty($component) ? $segment : '_' . $segment;
            if (isset($this->_components[$component])) {
                $lastMatch = $component;
            }
        } while (count($segments));

        if (!$lastMatch) {
            return false;
        }

        $final = substr($class, strlen($lastMatch));
        $path = $this->_components[$lastMatch];


        /**
         * added for retrieving an array $path
         */
        if (is_array($path)) {
        	$final = trim($final, '_');
        	$final = str_replace('_', '/', $final);
        	foreach ($path as $dir) {
        		$filename = $dir . '/' . $final . '.php';
        		if (file_exists($filename)) {
        			break;
        		}
        	}
        }
        else {
        	$filename = $path . '/' . str_replace('_', '/', $final) . '.php';
        }

        self::$_cache[$class] = realpath($filename);

        return include $filename;
    }



    /**
     * Add resource type
     *
     * @param  string $type identifier for the resource type being loaded
     * @param  string|array $path path relative to resource base path containing the resource types
     * @param  null|string $namespace sub-component namespace to append to base namespace that qualifies this resource type
     * @return Zend_Loader_Autoloader_Resource
     */
    public function addResourceType($type, $path, $namespace = null)
    {
        $type = strtolower($type);
        if (!isset($this->_resourceTypes[$type])) {
            if (null === $namespace) {
                require_once 'Zend/Loader/Exception.php';
                throw new Zend_Loader_Exception('Initial definition of a resource type must include a namespace');
            }
            $namespaceTopLevel = $this->getNamespace();
            $namespace = ucfirst(trim($namespace, '_'));
            $this->_resourceTypes[$type] = array(
                'namespace' => empty($namespaceTopLevel) ? $namespace : $namespaceTopLevel . '_' . $namespace,
            );
        }
        if ( ! is_string($path) AND ! is_array($path)) {
            require_once 'Zend/Loader/Exception.php';
            throw new Zend_Loader_Exception('Invalid path specification provided; must be string or array');
        }


        /**
         * added for retrieving an array $path
         */
        if (is_array($path)) {
        	$this->_resourceTypes[$type]['path'] = array();
        	foreach ($path as $p) {
        		$this->_resourceTypes[$type]['path'][] = $this->getBasePath() . '/' . $p;
        	}
        }
        else {
        		$this->_resourceTypes[$type]['path'] = $this->getBasePath() . '/' . $path;
        }


        $component = $this->_resourceTypes[$type]['namespace'];
        $this->_components[$component] = $this->_resourceTypes[$type]['path'];
        return $this;
    }


}
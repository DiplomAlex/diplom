<?php

class App_Controller_Router_Route_Regex extends Zend_Controller_Router_Route_Regex
{


    /**
     * Instantiates route based on passed Zend_Config structure
     *
     * @param Zend_Config $config Configuration object
     */
    public static function getInstance(Zend_Config $config)
    {
        $defs = ($config->defaults instanceof Zend_Config) ? $config->defaults->toArray() : array();
        $map = ($config->map instanceof Zend_Config) ? $config->map->toArray() : array();
        $reverse = (isset($config->reverse)) ? $config->reverse : null;
        return new self($config->route, $defs, $map, $reverse);
    }


    /**
     * Maps numerically indexed array values to it's associative mapped counterpart.
     *
     * Or vice versa. (exactly "vice versa" was fixed!!!)
     *
     * Uses user provided map array which consists of index => name
     * parameter mapping. If map is not found, it returns original array.
     *
     * Method strips destination type of keys form source array. Ie. if source array is
     * indexed numerically then every associative key will be stripped. Vice versa if reversed
     * is set to true.
     *
     * @param  array   $values Indexed or associative array of values to map
     * @param  boolean $reversed False means translation of index to association. True means reverse.
     * @param  boolean $preserve Should wrong type of keys be preserved or stripped.
     * @return array   An array of mapped values
     */

    protected function _getMappedValues($values, $reversed = false, $preserve = false)
    {
        if (count($this->_map) == 0) {
            return $values;
        }

        $return = array();
        foreach ($values as $key => $value) {
            if (is_int($key) && !$reversed) {
                if (array_key_exists($key, $this->_map)) {
                    $index = $this->_map[$key];
                } elseif (false === ($index = array_search($key, $this->_map))) {
                    $index = $key;
                }
                $return[$index] = $values[$key];
            } elseif ($reversed) {
                //$index = (!is_int($key)) ? array_search($key, $this->_map, true) : $key;


                // "vice versa" fix
                if ( ! is_int($key)) {
                    if ( ! $index = array_search($key, $this->_map, true)) {
                        if (isset($this->_map[$key])) {
                            $index = $this->_map[$key];
                        }
                        else {
                            $index = FALSE;
                        }
                    }
                }
                else {
                    $index = FALSE;
                }
                // vice versa fix end

                if (false !== $index) {
                    $return[$index] = $values[$key];
                }
            } elseif ($preserve) {
                $return[$key] = $value;
            }
        }

        return $return;
    }


    public function assemble($data = array(), $reset = false, $encode = false, $partial = false)
    {
        if ($this->_reverse === null) {
            require_once 'Zend/Controller/Router/Exception.php';
            throw new Zend_Controller_Router_Exception('Cannot assemble. Reversed route is not specified.');
        }

        $defaultValuesMapped  = $this->_getMappedValues($this->_defaults, true, false);
        $matchedValuesMapped  = $this->_getMappedValues($this->_values, true, false);
        $dataValuesMapped     = $this->_getMappedValues($data, true, false);

        // handle resets, if so requested (By null value) to do so
        if (($resetKeys = array_search(null, $dataValuesMapped, true)) !== false) {
            foreach ((array) $resetKeys as $resetKey) {
                if (isset($matchedValuesMapped[$resetKey])) {
                    unset($matchedValuesMapped[$resetKey]);
                    unset($dataValuesMapped[$resetKey]);
                }
            }
        }

        // merge all the data together, first defaults, then values matched, then supplied
        $mergedData = $defaultValuesMapped;
        $mergedData = $this->_arrayMergeNumericKeys($mergedData, $matchedValuesMapped);
        $mergedData = $this->_arrayMergeNumericKeys($mergedData, $dataValuesMapped);

        if ($encode) {
            foreach ($mergedData as $key => &$value) {
                $value = urlencode($value);
            }
        }

        ksort($mergedData);
        $return = @vsprintf($this->_reverse, $mergedData);

        if ($return === false) {
App_Debug::dump($mergedData, 'route-assemble-$mergedData');
App_Debug::dump($this->_defaults, 'route-assemble-defaults');
App_Debug::dump($this->_values, 'route-assemble-values');
App_Debug::dump($this->_map, 'route-assemble-map');
App_Debug::dump($data, 'route-assemble-$data');
App_Debug::dump($defaultValuesMapped, 'route-assemble-$defaultValuesMapped');
App_Debug::dump($matchedValuesMapped, 'route-assemble-$matchedValuesMapped');
/*var_dump($this->_reverse, $mergedData);
debug_print_backtrace();
exit;*/
            require_once 'Zend/Controller/Router/Exception.php';
            throw new Zend_Controller_Router_Exception('Cannot assemble. Too few arguments? (or maybe wrong routes.xml!???)');
        }

        return $return;

    }


}

<?php

class App_Controller_Plugin_Debug_Plugin_Dump extends ZFDebug_Controller_Plugin_Debug_Plugin implements ZFDebug_Controller_Plugin_Debug_Plugin_Interface
{
    /**
     * Contains plugin identifier name
     *
     * @var string
     */
    protected $_identifier = 'dump';

    /**
     * dumps container
     */
    protected $_dumps = array();

    /**
     * Create ZFDebug_Controller_Plugin_Debug_Plugin_Variables
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Gets identifier for this plugin
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }

    /**
     * Gets menu tab for the Debugbar
     *
     * @return string
     */
    public function getTab()
    {
        return ' Dump';
    }

    /**
     * add dump to container
     */
    public function add($var, $label = '', $type = NULL)
    {
        if (is_object($var) AND (method_exists($var, 'toArray'))) {
            $varStr = get_class($var).' Object() :'.$this->_cleanData($var->toArray());
        }
        else {
            $varStr = $this->_cleanData($var);
        }

        $varStr = $var;

        $this->_dumps []= array(
            'label' => $label,
            'var' => $varStr,
            'type' => $type,
        );
        return $this;
    }

    public function getDumps()
    {
        return $this->_dumps;
    }

    /**
     * Gets content panel for the Debugbar
     *
     * @return string
     */
    public function getPanel()
    {
        $dump = '';
        foreach ($this->_dumps as $row) {
            $dump .=  '<h4>'.$row['label'].'</h4>'
                    . '<div id="ZFDebug_dump">' . $row['var'] . '</div>';
        }
        return $dump;
    }


    /**
     * Transforms data into readable format
     *
     * @param mixed $values
     * @return string
     */
    protected function _cleanData($values)
    {
        if (is_array($values))
            ksort($values);

        $retVal = '<div class="pre">';

        if (is_array($values)) {
            foreach ($values as $key => $value)
            {
                $key = htmlspecialchars($key);
                if (is_numeric($value)) {
                    $retVal .= $key.' => '.$value.'<br>';
                }
                else if (is_string($value)) {
                    $retVal .= $key.' => \''.htmlspecialchars($value).'\'<br>';
                }
                else if (is_array($value))
                {
                    $retVal .= $key.' => '.self::_cleanData($value);
                }
                else if (is_object($value))
                {
                    $retVal .= $key.' => '.get_class($value).' Object()<br>';
                }
                else if (is_null($value))
                {
                    $retVal .= $key.' => NULL<br>';
                }
            }
        }
        else {
                if (is_numeric($values)) {
                    $retVal .= '  '.$values.'<br>';
                }
                else if (is_string($values)) {
                    $retVal .= '  \''.htmlspecialchars($values).'\'<br>';
                }
                else if (is_array($values))
                {
                    $retVal .= '  '.self::_cleanData($values);
                }
                else if (is_object($values))
                {
                    $retVal .= '  '.get_class($values).' Object()<br>';
                }
                else if (is_null($values))
                {
                    $retVal .= '  NULL<br>';
                }
        }
        return $retVal.'</div>';
    }
}
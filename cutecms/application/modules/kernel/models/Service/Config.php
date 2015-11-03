<?php

class Model_Service_Config extends Model_Service_Abstract
{
    const DEFAULT_TYPE = 'INI';

    protected $_defaultInjections = array();

    public function getConfigFilename($configName)
    {
        $info = pathinfo($configName);
        if (! isset($info['extension'])) {
            $configName .= '.'.strtolower(self::DEFAULT_TYPE);
        }

        $arr = explode('/', $configName);
        if (substr($configName, 0, 1) == '/') {
            $result = $configName;
        }
        else {
            $arr = explode('/', $configName);
            if ($arr[0]=='var') {
                $result = FRONT_APPLICATION_PATH . '/var/etc/'.$arr[1];
                if ( ! file_exists($result)) {
                    $result = APPLICATION_PATH . '/var/etc/'.$arr[1];
                }
            }
            else {
                if (count($arr) == 1) {
                    $module = 'kernel';
                    $config = $arr[0];
                } elseif (count($arr) == 2) {
                    $module = $arr[0];
                    $config = $arr[1];
                } else {
                    $module = $arr[2];
                    $config = $arr[4];
                }
                $result = FRONT_APPLICATION_PATH . '/modules/' . $module . '/configs/' . $config;
                if (!file_exists($result)) {
                    $result = APPLICATION_PATH . '/modules/' . $module . '/configs/' . $config;
                }
            }
        }

        return $result;
    }

    /**
     * @param string - filename of config
     * @return string - lowercase file extension
     */
    public function getConfigType($filename)
    {
        $info = pathinfo($filename);
        if (! isset($info['extension'])) {
            $type = self::DEFAULT_TYPE;
        }
        else {
            $type = $info['extension'];
        }
        $type = strtolower($type);
        return $type;
    }


    /**
     * @param string - name of config like "module/file.ext" for "modules/module/configs/file.ext"
     *                 or "var/file.ext" for var/etc/file.ext
     *                 or "/absolute/path/file.ext"
     * @param string
     * @param bool
     * @return Zend_Config_Abstract
     */
    public function read($configName, $section = NULL, $readonly = TRUE)
    {
        $className = 'Zend_Config_'.ucfirst($this->getConfigType($configName));

        return new $className($this->getConfigFilename($configName), $section, array('allowModifications'=>( ! $readonly)));
    }

    /**
     * @param Zend_Config
     * @param string - name of config like "module/file.ext" for "modules/module/configs/file.ext" or "var/file.ext" for var/etc/file.ext
     * @return $this
     */
    public function write(Zend_Config $config, $configName)
    {
        $className = 'Zend_Config_Writer_'.ucfirst($this->getConfigType($configName));
        $writer = new $className;
        $writer->write($this->getConfigFilename($configName), $config);
        return $this;
    }
}
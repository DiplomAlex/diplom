<?php

class Model_Service_Package extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface'     => 'Model_Object_Package',
        'Model_Collection_Interface' => 'Model_Collection_Package',
        'Model_Mapper_Interface'     => 'Model_Mapper_Config_Package',
    );

    /**
     * @return string - full path to package info file
     */
    public function getInfoFileName($packName)
    {
        return APPLICATION_PATH.'/var/packages/'.$packName.'.xml';
    }

    /**
     * @param string - "package-name" for kernel module package or "module-name/package-name" for package of module
     * @return Model_Object_Interface
     */
    public function get($packName)
    {
        $cache = Zend_Registry::get('Zend_Cache');
        $cacheKey = __CLASS__.__FUNCTION__.'__'.$packName;
        if ( ! $pack = $cache->load($cacheKey)) {
            $pack = $this->getMapper()->fetchFromFile($this->getInfoFileName($packName));
            $cache->save($pack, $cacheKey, array('package'));
        }
        return $pack;
    }


    /**
     * checks if file var/$packName.xml exists
     */
    public function isInstalled($packName)
    {
        return file_exists($this->getInfoFileName($packName));
    }

    /**
     * checks if enabled=TRUE for package
     */
    public function isEnabled($packName)
    {
    }

    /**
     * 1 - add package events and observers to module|kernel/events.xml
     * 2 - in var/packages/package.xml set enabled to TRUE
     */
    public function enable($packName)
    {
    }

    /**
     * 1 - in var/packages/package.xml set enabled to FALSE
     * 2 - delete package events and observers from module|kernel/events.xml
     */
    public function disable($packName)
    {
    }

    /**
     * package installation process:
     * 1 - create folder in var/tmp
     * 2 - unzip package to that folder
     * 3 - copy package.xml to var/packages
     * 4 - copy files to their places (controllers, forms, models, views)
     * 5 - copy configs to var/etc or module|kernel/configs
     * 6 - add acl resources, allows, denies to module|kernel/configs/acl.ini
     * 7 - delete folder in var/tmp
     * 8 - enable if autoenabled is on
     */
    public function install(Model_Object_Interface $pack)
    {
    }


    /**
     * package uninstallation process:
     * 1 - disable
     * 2 - delete acl resources, allows, denies from module|kernel/configs/acl.ini
     * 3 - delete configs from var/etc or module|kernel/configs
     * 4 - delete files from their places (controllers, forms, models, views)
     * 5 - delete package.xml from var/packages
     */
    public function uninstall(Model_Object_Interface $pack)
    {

    }


}
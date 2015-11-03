<?php

class Model_Service_Installer extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Model_Object_InstallerOptions'
    );

    public function getDefaultValues()
    {
        $config = $this->_getApplicationIniConfig();
        $def = array();

        $def['db_adapter'] = $config->production->resources->db->adapter;
        $def['db_host'] = $config->production->resources->db->params->host;
        $def['db_name'] = $config->production->resources->db->params->dbname;
        $def['db_user'] = $config->production->resources->db->params->username;
        $def['db_password'] = $config->production->resources->db->params->password;
        $def['db_import_dump'] = TRUE;

        $def['host'] = $_SERVER['HTTP_HOST'];
        $def['base_url'] = APPLICATION_BASE;
        $def['site_name'] = $def['host'];

        $def['support_email'] = 'support@'.$def['host'];
        $def['support_name'] = 'Support '.$def['site_name'];

        return $def;
    }

    public function install(array $options)
    {
        $opts = $this->getInjector()->getObject('Model_Object_Interface');
        foreach ($options as $key=>$val) {
            if ($opts->hasElement($key)) {
                $opts->{$key} = $val;
            }
        }
        $result = TRUE;
        try {
            $this->_chmods($opts);
            $this->_writeFrontApplicationIni($opts);
            $this->_writeFrontConfigIni($opts);
            $this->_writeDb($opts);
        }
        catch (Exception $e) {
            $result = $e->getMessage();
        }
        return $result;
    }

    public function finish()
    {
        $this->_writeInstallationXml();
        $this->_removeDangerousFiles();
    }

    private function _exec($command)
    {
        $res = NULL;
        exec($command, $res);
        return $res;
    }

    private function _chmods(Model_Object_Interface $opts)
    {
        $this->_exec('chmod a+rwx '.APPLICATION_PATH.'/var/cache');
        $this->_exec('chmod a+rwx '.APPLICATION_PATH.'/var/log');
        $this->_exec('chmod a+rwx '.APPLICATION_PATH.'/var/tmp');
        $this->_exec('chmod a+rwx '.APPLICATION_PATH.'/var/etc');
        $this->_exec('chmod a+rw  '.APPLICATION_PATH.'/var/etc/*');
        $this->_exec('chmod a+rwx '.APPLICATION_PATH.'/var/packages');
        $this->_exec('chmod a+rw  '.APPLICATION_PATH.'/var/packages/*');
        $this->_exec('chmod a+rwx '.APPLICATION_PUBLIC.'/tmp '.APPLICATION_PUBLIC.'/tmp/captcha');
        $this->_exec('chmod a+rwx '.APPLICATION_PUBLIC.'/uploads');
        $this->_exec('chmod a+rwx '.APPLICATION_PUBLIC.'/uploads/png');
        $this->_exec('chmod a+rwx '.APPLICATION_PUBLIC.'/uploads/ckfinder');
        $this->_exec('chmod a+rwx '.APPLICATION_PUBLIC.'/uploads/ckfinder/images');

        $this->_exec('chmod a+rw  '.APPLICATION_PATH.'/configs/application.ini');

        $modulesDir = APPLICATION_PATH.'/modules';
        $modulesEntries = scandir($modulesDir);
        foreach ($modulesEntries as $entry) {
            $dir = $modulesDir.'/'.$entry;
            if (($entry!='.') AND ($entry!='..') AND is_dir($dir)) {
                $this->_exec('chmod a+rwx  '.$dir.'/configs');
                $this->_exec('chmod a+rw   '.$dir.'/configs/config.ini');
                $this->_exec('chmod a+rw   '.$dir.'/configs/acl.ini');
                $this->_exec('chmod a+rw   '.$dir.'/configs/events.xml');
                $this->_exec('chmod a+rw   '.$dir.'/configs/packages.xml');
            }
        }

        /* TODO - next line should be uncommented for security reasons */
        //$this->_exec('chmod -R a-w  '.APPLICATION_PUBLIC.'/js');
    }

    private function _getApplicationIniConfig()
    {
    	$configService = Model_Service::factory('config');
        $appIni = Model_Service::factory('config')->read(APPLICATION_PATH.'/configs/application.ini', NULL, FALSE);
        $frontIniFilename = FRONT_APPLICATION_PATH.'/configs/application.ini';
        if (file_exists($frontIniFilename)) {
        	$frontIni = $configService->read($frontIniFilename);
        	$appIni->merge($frontIni);
        }
        return $appIni;
    }

    private function _writeApplicationIni(Model_Object_Interface $opts)
    {
        $configFilename = APPLICATION_PATH.'/configs/application.ini';
        $configService = Model_Service::factory('config');
        $config = $configService->read($configFilename, NULL, FALSE);
        $config->production->resources->db->adapter = $opts->db_adapter;
        $config->production->resources->db->params->host = $opts->db_host;
        $config->production->resources->db->params->dbname = $opts->db_name;
        $config->production->resources->db->params->username = $opts->db_user;
        $config->production->resources->db->params->password = $opts->db_password;
        $configService->write($config, $configFilename);
    }

    private function _writeFrontApplicationIni(Model_Object_Interface $opts)
    {
        $configService = Model_Service::factory('config');
        $configFilename = FRONT_APPLICATION_PATH.'/configs/application.ini';
        $config = $this->_getApplicationIniConfig();
        $config->production->resources->db->adapter = $opts->db_adapter;
        $config->production->resources->db->params->host = $opts->db_host;
        $config->production->resources->db->params->dbname = $opts->db_name;
        $config->production->resources->db->params->username = $opts->db_user;
        $config->production->resources->db->params->password = $opts->db_password;
        $configService->write($config, $configFilename);
    }

    private function _writeKernelConfigIni(Model_Object_Interface $opts)
    {
        $configFilename = 'config.ini';
        $configService = Model_Service::factory('config');
        $config = $configService->read($configFilename, NULL, FALSE);
        $config->email->support = $opts->support_email;
        $config->email->supportName = $opts->support_name;
        $config->www->domain = $opts->host;
        $config->www->siteName = $opts->site_name;
        $configService->write($config, $configFilename);
    }

    private function _writeFrontConfigIni(Model_Object_Interface $opts)
    {
        $configService = Model_Service::factory('config');
        $config = $configService->read($configService->getConfigFilename('kernel/config'), NULL, FALSE);
        $moduleName = $this->_getApplicationIniConfig()->production->frontModuleName;
        $configFilename = FRONT_APPLICATION_PATH.'/modules/'.$moduleName.'/configs/config.ini';
        if (file_exists($configFilename)) {
            $moduleConfig = $configService->read($configFilename);
            $config->merge($moduleConfig);
        }
        $config->email->support = $opts->support_email;
        $config->email->supportName = $opts->support_name;
        $config->www->domain = $opts->host;
        $config->www->siteName = $opts->site_name;
        $configService->write($config, $configFilename);
    }

    private function _writeDb(Model_Object_Interface $opts)
    {
        $config = $this->_getApplicationIniConfig()->production->resources->db->params;
        $db = Zend_Db::factory($opts->db_adapter, $config);
        $db->query("SET NAMES 'utf8'");
        $db->query("SET CHARACTER SET 'utf8'");
        if ( (int) $opts->db_import_dump) {
            $dumpFile = APPLICATION_PUBLIC.'/_sql/db.sql';
            if ( ! file_exists($dumpFile)) {
                throw new Exception('sql dump is absent');
            }
            $this->_exec('mysql '.$config->dbname.' --host='.$config->host.' --user='.$config->username.' --password='.$config->password.' < '.$dumpFile);
            $this->_exec('rm -rf '.APPLICATION_PUBLIC.'/_sql');
        }
        $baseUrl = trim($opts->base_url, '/');
        /*
         * besause of links from entitites to sites
         * only singlesite mechanizm is realized now for installer
         *
        $select = $db->select()
                     ->from('site', array('cnt'=>'COUNT(site_id)'))
                     ->where('site_host = ?', $opts->host)
                     ->where('site_base_url = ?', $baseUrl);
        $row = $select->query()->fetch();
        if ( ! $row['cnt']) {
            $db->insert('site', array(
                'site_host' => $opts->host,
                'site_base_url' => $baseUrl,
                'site_status' => 1,
                'site_vertical_id' => 1,
            ));
        }
        */
        $db->update('site', array(
	            'site_host' => $opts->host,
	            'site_base_url' => $baseUrl,
	            'site_status' => 1,
	            'site_vertical_id' => 1,
        	 ), 'site_id = 1');

    }

    private function _writeInstallationXml()
    {
    	$configService = Model_Service::factory('config');
        $config = new Zend_Config(array('installation'=>array('info'=>array('result'=>'ok'))), TRUE);
        $configService->write($config, APPLICATION_PATH.'/var/etc/installation.xml');
        $configService->write($config, FRONT_APPLICATION_PATH.'/var/etc/installation.xml');
    }

    private function _removeDangerousFiles()
    {
        unlink(__FILE__);
    }

}
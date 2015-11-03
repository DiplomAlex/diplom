<?php

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ((isset($_GET['notest'])) OR (ServerTester::factory()->run() AND ( ! isset($_GET['noinstall'])))) {
    ShellInstaller::factory()->install();
}

class ServerTester
{
    protected $_phpModulesRequired = array(
        'ctype', 'date', 'dom',
        'gd', 'gettext', 'hash', 'iconv', 'json',
        'libxml', 'mbstring', 'mcrypt',
        'mysqli', 'pcre', 'PDO', 'posix', 'pdo_mysql',
        'Reflection', 'session', 'SimpleXML', 'SPL',
        'standard', 'xml', 'zip', 'zlib', 
    );

    protected $_phpVersionMin = '5.2.1';
    protected $_mysqlVersionMin = '5.1';

    protected $_mysqlParamsTest = TRUE;
    protected $_mysqlParams = array(
        'host' => 'localhost',
        'username' => 'dbuser',
        'password' => 'dbpassword',
        'dbname' => 'dbname',
        'charset' => 'utf8',
        'engine' => 'InnoDb',
    );


    protected $_testFailed = FALSE;
    protected $_shellOutput = NULL;
    protected $_shellError = 0;

    public static function factory()
    {
        return new self();
    }

    public function run()
    {
        $this->_testPhpVersion();
        $this->_testPhpModules();
        $this->_testMysqlVersion();
        $this->_testMysqlParams();
        $this->_testShell();
        return  ! $this->_testFailed;
    }

    protected function _testPhpVersion()
    {
        $title = 'Php version >= '.$this->_phpVersionMin;
        $addonText = 'Current version is '.PHP_VERSION;
        if ($this->_isMoreThanMinVersion($this->_phpVersionMin, PHP_VERSION)) {
            echo $this->_resultOuputSuccess($title, $addonText);
        }
        else {
            echo $this->_resultOuputError($title, $addonText);
        }
    }

    protected function _testPhpModules()
    {
        $testTitle = 'Php modules';
        $modules = get_loaded_extensions();
        $intersect = array_intersect($modules, $this->_phpModulesRequired);
        if (count($intersect) == count($this->_phpModulesRequired)) {
            echo $this->_resultOuputSuccess($testTitle, 'Modules tested: '.implode($this->_phpModulesRequired, ', '));
        }
        else {
            $diff = array_diff($this->_phpModulesRequired, $intersect);
            echo $this->_resultOuputError($testTitle,  'Modules absent: '.implode($diff, ', '));
        }
    }

    protected function _testMysqlVersion()
    {
        $title = 'Mysql version >= '.$this->_mysqlVersionMin;
        $ver = mysql_get_client_info();
        $addonText = 'Current version is '.$ver;
        if ($this->_isMoreThanMinVersion($this->_mysqlVersionMin, $ver)) {
            echo $this->_resultOuputSuccess($title, $addonText);
        }
        else {
            echo $this->_resultOuputError($title, $addonText);
        }
    }

    protected function _testMysqlParams()
    {
    	if ( ! isset($_GET['test_mysql_params'])) {
    		return NULL;
    	}
        $title = 'Mysql parameters';
        if ($this->_mysqlParamsTest === TRUE) {
            if ( ! @mysql_connect($this->_mysqlParams['host'], $this->_mysqlParams['username'], $this->_mysqlParams['password'])) {
                echo $this->_resultOuputError($title, 'Wrong host('.$this->_mysqlParams['host'].'), username('.$this->_mysqlParams['username'].') or password('.$this->_mysqlParams['password'].') !');
            }
            else if ( ! @mysql_select_db($this->_mysqlParams['dbname'])) {
                echo $this->_resultOuputError($title, 'Wrong dbname -'.$this->_mysqlParams['dbname'].' !');
            }
            else {

                mysql_query('CREATE TABLE test_a (field_a INT NULL) ENGINE='.$this->_mysqlParams['engine'].' CHARSET='.$this->_mysqlParams['charset'].';');

                if ( ! $result = mysql_query('SELECT * FROM test_a')) {
                    echo $this->_resultOuputError($title, 'Wrong db grants - user "'.$this->_mysqlParams['username'].'" cannot create table in db "'.$this->_mysqlParams['dbname'].'" with engine="'.$this->_mysqlParams['engine'].'" and charset="'.$this->_mysqlParams['charset'].'"');
                }
                else {
                    $testIsOk = TRUE;
                    $result = mysql_query('SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA = \''.$this->_mysqlParams['dbname'].'\' AND TABLE_NAME = \'test_a\'');
                    $info = mysql_fetch_assoc($result);
                    if (strtoupper(@$info['ENGINE']) != strtoupper($this->_mysqlParams['engine'])) {
                        $testIsOk = FALSE;
                        echo $this->_resultOuputError($title, 'Wrong db settings - cannot create table with engine "'.$this->_mysqlParams['engine'].'"');
                    }
                    if (strtoupper(substr(@$info['TABLE_COLLATION'], 0, strlen($this->_mysqlParams['charset']))) != strtoupper($this->_mysqlParams['charset'])) {
                        $testIsOk = FALSE;
                        echo $this->_resultOuputError($title, 'Wrong db settings - cannot set for table charset "'.$this->_mysqlParams['charset'].'"');
                    }
                    if ( ! $result = mysql_query('DROP TABLE test_a')) {
                        $testIsOk = FALSE;
                        echo $this->_resultOuputError($title, 'Wrong db grants - user "'.$this->_mysqlParams['username'].'" cannot drop table in db "'.$this->_mysqlParams['dbname'].'"');
                    }

                    if ($testIsOk === TRUE) {
                        $cnt = count($this->_mysqlParams);
                        $i = 0;
                        $paramsText = '';
                        foreach($this->_mysqlParams as $param=>$value) {
                            $paramsText .= ($i++>0?', ':'').$param.': '.$value;
                        }
                        echo $this->_resultOuputSuccess($title, 'Parameters tested - '.$paramsText);
                    }
                }
            }
        }
    }

    protected function _testShell()
    {
        $testTitle = 'Shell commands';
        $this->_shellExec('mkdir -v test.dir');
        $this->_shellExec('chmod -R 0777 test.dir');
        $this->_shellExec('echo "text text" > test.dir/test.file');
        $this->_shellExec('chmod 0666 test.dir/test.file');
        $this->_shellExec('stat test.dir/test.file');
        $this->_shellExec('zip -r test.zip test.dir');
        $this->_shellExec('unzip -l test.zip');
        $this->_shellExec('mv -v test.dir/test.file ./');
        $this->_shellExec('rm -Rv test.dir');
        $this->_shellExec('rm -v test.file');
        $this->_shellExec('unzip test.zip');
        $this->_shellExec('rm -Rv test.dir');
        $this->_shellExec('rm -v test.zip');

        if ($this->_shellError) {
            echo $this->_resultOuputError($testTitle,  '<pre>'. $this->_shellOutput . '</pre>');
        }
        else {
            echo $this->_resultOuputSuccess($testTitle, 'Tested commands: mkdir, chmod, echo, stat, zip, unzip, mv, rm, rm -R');
        }

    }

    private function _shellExec($command, $beforeText = NULL, $afterText = NULL)
    {
        $res = NULL;
        try {
            exec($command, $res, $err);
            //$res = array(passthru($command));
        }
        catch (Exception $e) {
            $res = array('"'.$command.'": UNSUPPORTABLE');
        }
        $out =  "\r\n\"".$command.'": '.'(error - '.$err.') '.($beforeText?$beforeText.' : ':'')
                . implode("\r\n", $res)
                . ($afterText?"\r\n".$afterText."\r\n":'')."\r\n"."\r\n";
        $this->_shellOutput .= $out;
        $this->_shellError += $err;
        return $out;
    }

    private function _isMoreThanMinVersion($minV, $currV)
    {
        if ($currV[0] >= $minV[0]) {
            if (empty($minV[2]) || $minV[2] == '*') {
                return TRUE;
            } elseif ($currV[2] >= $minV[2]) {
                if (empty($minV[4]) || $minV[4] == '*') {
                    return TRUE;
                }
                else if ($currV[4] >= $minV[4]) {
                    if (empty($minV[5]) || $minV[5] == '*' || $currV[5] >= $minV[5]) {
                        return TRUE;
                    }
                }

            }
        }
        return FALSE;
    }

    private function _resultOuputSuccess($title, $addonText = '')
    {
        return '<p><strong style="color:green;">'.$title.' - OK</strong><br/>'.$addonText.'</p>';
    }

    private function _resultOuputError($title, $addonText = '')
    {
        $this->_testFailed = TRUE;
        return '<p><strong style="color:red;">Error occured during test "'.$title.'"</strong><br/>'.$addonText.'</p>';
    }

}



class ShellInstaller
{
    const GET_PARAM_ZIPFILE = 'zip';
    const GET_PARAM_INZIPDIR = 'dir';

    const DEFAULT_ZIPFILE = 'production.zip';
    const DEFAULT_INZIPDIR = 'production';

    protected $_zipfile = NULL;
    protected $_inzipdir = NULL;

    protected $_output = NULL;

    protected function __construct($zipfile = NULL, $inzipdir = NULL)
    {
        if ($zipfile) {
            $this->_zipfile = $zipfile;
        }
        else {
            $this->_zipfile = self::DEFAULT_ZIPFILE;
        }
        if ($inzipdir) {
            $this->_inzipdir = $inzipdir;
        }
        else {
            $this->_inzipdir = self::DEFAULT_INZIPDIR;
        }
    }

    public static function factory()
    {
        return new self(@$_GET[self::GET_PARAM_ZIPFILE], @$_GET[self::GET_PARAM_INZIPDIR]);
    }

    public function install()
    {
        $this->_exec('rm -f .htaccess');
        $this->_exec('rm -f index.html');

        $this->_createZipFile();

        $this->_exec('base64 -di '.$this->_zipfile.'.enc > '.$this->_zipfile);
        $this->_exec('rm '.$this->_zipfile.'.enc');

        $this->_exec('unzip -q '.$this->_zipfile, NULL, 'unzipped');

        $this->_exec('chmod -R a+rw '.$this->_inzipdir);

        $this->_exec('rm -Rf ./application');
        $this->_exec('mv '.$this->_inzipdir.'/application ./');

        $this->_exec('rm -Rf ./cutecms');
        $this->_exec('mv '.$this->_inzipdir.'/cutecms ./');

        $this->_exec('rm -Rf ./skins');
        $this->_exec('mv '.$this->_inzipdir.'/skins ./');

        $this->_exec('rm -f ./index.php');
        $this->_exec('mv '.$this->_inzipdir.'/index.php ./');

        $this->_exec('rm -f ./.htaccess');
        $this->_exec('mv '.$this->_inzipdir.'/.htaccess ./');

        $this->_exec('rm -Rf ./tmp');
        $this->_exec('mv '.$this->_inzipdir.'/tmp ./');

        $this->_exec('rm -Rf ./uploads');
        $this->_exec('mv '.$this->_inzipdir.'/uploads ./');

        $this->_exec('rm -Rf ./_sql');
        $this->_exec('mv '.$this->_inzipdir.'/_sql ./');

        $this->_exec('rm -Rf ./js');
        $this->_exec('mv '.$this->_inzipdir.'/js ./', NULL, 'mv finished');

        $this->_exec('rm -Rf '.$this->_inzipdir);
        $this->_exec('rm '.$this->_zipfile);

        $this->_exec('cp -f '.__FILE__.' '.__FILE__.'.bak', 'install 1st stage finished');

        header('Location: index.php');

    }

    private function _exec($command, $beforeText = NULL, $afterText = NULL)
    {
        $res = NULL;
        exec($command, $res);
        $out =  ($beforeText?$beforeText.' : ':'')
                . implode('<br/>'."\r\n", $res)
                . ($afterText?'<br/>'."\r\n".$afterText.'<br/>'."\r\n":'');
        $this->_output .= $out;
        return $out;
    }

    private function _createZipFile()
    {
    	$fThis = fopen(__FILE__, 'r');
    	$prev = '';
    	$char = '';
    	$finish = '?'.'>';
    	while( ( ! feof($fThis)) AND ($prev.$char != $finish)) {
    	   $prev = $char;
    	   $char = fgetc($fThis);
    	}
    	if ($prev.$char == $finish) {
	    	$fArc = fopen($this->_zipfile.'.enc', 'w');
	    	while( ! feof($fThis)) {
	    		$portion = fread($fThis, 1048576);
	    		fwrite($fArc, $portion);
	    	}
	    	fclose($fArc);
    	}
    	else {
    	   echo 'install.php is corrupted';
    	}
    	fclose($fThis);
    }

}

exit;
?>
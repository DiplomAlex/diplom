<?php

require dirname(__FILE__).'/functions.php';

class App_PreBoot {

    /**
     * /var/www/htdocs/sub/site/index.php
     * /sub/site/index/id/smth
     *
     * $path = array(var, www, htdocs, sub, site)
     * $uri = array(sub, site, index, id, smth)
     *
     * @param string $_SERVER['SCRIPT_FILENAME']
     * @param string $_SERVER['REQUEST_URI']
     * @param string PHP_OS
     * @return string current uri base (/sub/site)
     */

    public static function getApplicationBase($scriptFilename = NULL, $uri = NULL, $os = PHP_OS)
    {

        if (substr( $os, 0, 3 ) == 'WIN') {
            $dirSeparator = '\\';
        }
        else {
            $dirSeparator = '/';
        }

        if ($scriptFilename === NULL) {
        	$scriptFilename = $_SERVER['SCRIPT_FILENAME'];
        }
        $pathInfo = pathinfo(/*realpath(*/$scriptFilename/*)*/);

        $path = trim($pathInfo['dirname'], $dirSeparator);
        $path = explode($dirSeparator, $path);


        if ($uri === NULL) {
        	$uri = $_SERVER['REQUEST_URI'];
        }
        $uri = trim($uri, '/');
        $uri = explode('/', $uri);

        if (empty($uri)) {
            $result = '/';
        }
        else {
            $both = array();
            $maxLen = min(count($path), count($uri));
            $len = 1;
            $found = FALSE;
            $finished = FALSE;
            $result = '';
            while (($len <= $maxLen) AND ( ! $found OR  ! $finished)) {

                $arrPath = array_slice($path, - $len);
                $arrUri = array_slice($uri, 0, $len);

                $getPath = implode('/', $arrPath);
                $getUri = implode('/', $arrUri);

                if ($getPath == $getUri) {
                    $found = TRUE;
                    $result = $getPath;
                }
                else if ($found) {
                    $finished = TRUE;
                }

                ++ $len;
            }
            $result = '/' . $result;
        }

        return $result;
    }

    public static function processMagicQuotesGPC()
    {
        if (get_magic_quotes_gpc()) {
            $_GET = self::stripslashesArray($_GET);
            $_POST = self::stripslashesArray($_POST);
            $_COOKIE = self::stripslashesArray($_COOKIE);
         }
    }

    /**
     * Strip XSS injections in requests
     */
    public static function stripRequestXssArray(){
        $_GET = self::stripXssArray($_GET);
        $_POST = self::stripXssArray($_POST);
        $_COOKIE = self::stripXssArray($_COOKIE);
    }

    /**
     * @param array - data array to strip slashes in
     * @param string method to porcess array (stack or recursion)
     * @return array
     */
    public static function stripslashesArray(array $arr, $method = 'walk')
    {
        $function = '_stripslashesArray'.ucfirst($method);
        return self::$function($arr);
    }

    
    protected static function _stripslashesArrayWalk(array $arr)
    {
        array_walk_recursive($arr, 'App_PreBoot::stripslashesValue');
        return $arr;
    }
    
    public static function stripslashesValue(&$val, $key)
    {
        $val = stripslashes($val); 
    }

    /**
     * @param array - data array to strip xss in
     * @param string method to porcess array (stack or recursion)
     * @return array
     */
    public static function stripXssArray(array $arr, $method = 'walk')
    {
        $function = '_stripXssArray'.ucfirst($method);
        return self::$function($arr);
    }

    
    protected static function _stripXssArrayWalk(array $arr)
    {
        array_walk_recursive($arr, 'App_PreBoot::stripXssValue');
        return $arr;
    }
    
    public static function stripXssValue(&$val, $key)
    {
        if (strstr($val, '<script')) {
            $val = htmlspecialchars($val);
        } 
    }
    
    

    protected static function _stripslashesArrayRecursion(array $arr)
    {
        foreach ($arr as $key=>$val) {
            if (is_array($val)) {
                $val = self::_stripslashesArrayRecursion($val);
            }
            else {
                $val = stripslashes($val);
            }
            $arr[$key] = $val;
        }
        return $arr;
    }


    protected static function _stripslashesArrayStack(array $arr)
    {
        $stack = array();
        $currArr = $arr;
        $i = 0;
        $currCnt = count($currArr);
        while ($i<$currCnt) {
            $currVal = $currArr[$i];
            if (is_array($currVal)) {
                array_push($stack, array('i'=>$i, 'cnt'=>$currCnt, 'arr'=>$currArr));
                $currArr = $currVal;
                $currCnt = count($currArr);
                $i = 0;
                continue;
            }
            else {
                $currArr[$i] = stripslashes($currVal);
            }
            ++ $i;
            while (($i == $currCnt) AND ! empty($stack)) {
                $data = array_pop($stack);
                $data['arr'][$data['i']] = $currArr;
                $currArr = $data['arr'];
                $currCnt = $data['cnt'];
                $i = $data['i']+1;
            }
        }
        return $currArr;
    }

    public static function isInstalled()
    {
        $result =   file_exists(FRONT_APPLICATION_PATH.'/var/etc/installation.xml')
                    AND
                    file_exists(APPLICATION_PATH.'/var/etc/installation.xml');
        return $result;
    }

    public static function getApplicationIniConfig()
    {
        require_once 'Zend/Config/Ini.php';
        $baseIni  = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV, array('allowModifications'=>TRUE));
        $frontIniFile = FRONT_APPLICATION_PATH . '/configs/application.ini';
        if (file_exists($frontIniFile) AND (FRONT_APPLICATION_PATH != APPLICATION_PATH)) {
            $frontIni = new Zend_Config_Ini($frontIniFile);
            if ($frontIni->{APPLICATION_ENV}) {
                $baseIni->merge($frontIni->{APPLICATION_ENV});
            }
        }
        return $baseIni;
    }

    public static function initFrontModule()
    {
        if (FRONT_APPLICATION_PATH != APPLICATION_PATH) {
            Zend_Controller_Front::getInstance()->addModuleDirectory(FRONT_APPLICATION_PATH . '/modules');
        }
    }

}
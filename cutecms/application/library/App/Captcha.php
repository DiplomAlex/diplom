<?php

class App_Captcha
{

    protected static $_driver = 'Image';
    protected static $_options = NULL;

    public static function factory($driver = NULL, $options = NULL)
    {
        if ($driver !== NULL) {
            self::$_driver = $driver;
        }
        self::$_options = $options;
        if (self::$_options === NULL) {
            self::$_options = array();
        }
        $defOptions = self::_getDefaultOptions();
        self::$_options = array_merge(self::$_options, $defOptions);

        $className = 'App_Captcha_'.ucfirst(self::$_driver);
        if ( ! class_exists($className)) {
            $className = 'Zend_Captcha_'.ucfirst(self::$_driver);
        }
        $captcha = new $className(self::$_options);
        return $captcha;
    }

    protected static function _getDefaultOptions()
    {
        /*$url = rtrim(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), NULL, TRUE, TRUE), '/');*/
        $view = new Zend_View;
        $url = $view->serverUrl();
        $config = Zend_Registry::get('config')->captcha;
        return array(
            'captcha' => 'Image',
            // Length of the word...
            'wordLen' => 6,
            // Captcha timeout, 5 mins
            'timeout' => 300,
            // What font to use...
            'font' => APPLICATION_PATH .'/'. $config->font,
            // Where to put the image
            'imgDir' =>  APPLICATION_PUBLIC .'/'. $config->dir,
            // URL to the images
            // This was bogus, here's how it should be... Sorry again :S
            'imgUrl' =>  $url. '/' . $config->dir,
            'numbersOnly' => TRUE,
        );
    }

}



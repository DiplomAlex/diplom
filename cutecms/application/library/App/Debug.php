<?php

class App_Debug
{
    const LOG_FILENAME = 'var/log/debug.log';

    protected static $_enabled = FALSE;


    public function enable()
    {
        self::$_enabled = TRUE;
    }

    public function disable()
    {
        self::$_enabled = FALSE;
    }

    public static function dump($var, $label=NULL, $foldObjects=TRUE)
    {
        if (self::$_enabled) {
            $backtrace = debug_backtrace();
            $label = $label.' ('.'#'.$backtrace[0]['line'].' in '.$backtrace[0]['file'].') :';
            Zend_Controller_Front::getInstance()->getPlugin('ZFDebug_Controller_Plugin_Debug')->getPlugin('Dump')->add('<pre>'.self::getDumpText($var).'</pre>', $label);
        }
    }

    public static function log($var, $label = NULL)
    {
        $f = fopen(FRONT_APPLICATION_PATH .'/'. self::LOG_FILENAME, 'a');
        $txt = htmlspecialchars_decode(strip_tags(Zend_Debug::dump($var, $label, FALSE)));
        fwrite($f, date('Y-m-d H:i:s').' : '.$txt."\n\r\n\r");
        fclose($f);
    }

    public static function getDumpText($var, $level = 0)
    {
        if (is_object($var)) {
            $text = get_class($var);
        }
        else if (is_array($var)) {
            $text = "array(\r\n";
            foreach($var as $key=>$value) {
                $text .= str_repeat('    ', $level+1) . '"'.$key.'" => ' . self::getDumpText($value, $level + 1);
            }
            $text .= ")\r\n";
        }
        else if (is_int($var) OR (is_float($var)) OR is_bool($var)) {
            ob_start();
            var_dump($var);
            $text = ob_get_clean();
        }
        else if (is_string($var)) {
            $text = '"'.$var.'"';
        }
        else {
            ob_start();
            var_dump($var);
            $text = ob_get_clean();
        }
        $strings = explode("\r\n", $text);
        foreach ($strings as $key=>$string) {
            $strings[$key] = str_repeat('    ', $level) . $string;
        }
        $text = implode("\r\n", $strings);
        $text = trim($text);
        $text .= "\r\n";
        return $text;
    }

    public static function backtrace($backCount = 1, $echo = TRUE, $separator = '<br/>')
    {
        $trace = debug_backtrace();
        $trace = array_slice($trace, $backCount);
        foreach ($trace as $key=>$val) {
            $trace[$key] = $val['file'].': #'.$val['line'].' ('.$val['class'].'::'.$val['function'].')';
        }
        $txt = implode($separator, $trace);
        if ($echo === TRUE) {
            echo $txt;
        }
        return $txt;
    }

    public static function dumpToFirebug($var, $label=NULL, $foldObjects=TRUE)
    {
        Zend_Log_Writer_Firebug::factory(NULL)->write(array('firebugLabel'=>$label, 'message'=>self::getDumpText($var), 'priority'=>1));
    }

}
<?php

class App_View_Helper_FormatTime extends Zend_View_Helper_Abstract
{

    const FORMAT_TIME = 'H:i';

    public function formatTime($dt = NULL)
    {
        $format = self::FORMAT_TIME;

        if ($dt === NULL) {
            $dt = time();
        }
        else if ( ! is_numeric($dt)) {
            $dt = strtotime($dt);
        }

        $result = date($format, $dt);

        return $result;
    }

}



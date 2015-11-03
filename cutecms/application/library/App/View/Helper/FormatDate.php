<?php

class App_View_Helper_FormatDate extends Zend_View_Helper_Abstract
{

    const FORMAT_DATE = 'd.m.Y';
    const FORMAT_DATETIME = 'd.m.Y H:i';

    /**
     * @param string
     * @param bool|string
     * @return string
     */
    public function formatDate($dt, $datetime = FALSE, $resetNull = TRUE)
    {
        if ($datetime === TRUE) {
            $format = self::FORMAT_DATETIME;
        }
        else if (is_string($datetime)) {
            $format = $datetime;
        }
        else {
            $format = self::FORMAT_DATE;
        }
        if ( ! is_numeric($dt)) {
            $dt = strtotime($dt);
        }
        $result = date($format, $dt);


        if (($resetNull) AND ($result == '01.01.1970')) {
            $result = '';
        }

        return $result;
    }

}



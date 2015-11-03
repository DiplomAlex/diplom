<?php

class View_Helper_DateTranslated extends Zend_View_Helper_Abstract
{

    public function dateTranslated($dt = NULL, $withYear = FALSE)
    {
        if ($dt === NULL) {
            $time = time();
        }
        else if (is_int($dt)) {
            $time = $dt;
        }
        else {
            $time = strtotime($dt);
        }

        $day = date('d', $time);
        $month = $this->view->translate('of ' . date('F', $time));
        $year = date('Y', $time);
        $dayOfWeek = $this->view->translate(date('l', $time));

        $result = $day . ' ' . $month .($withYear?' '.$year:''). ', ' . $dayOfWeek;

        return $result;
    }

}



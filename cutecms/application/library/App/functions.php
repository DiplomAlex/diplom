<?php

if (function_exists('lcfirst') === FALSE) {
    function lcfirst( $str )
    {
        return (string)(strtolower(substr($str,0,1)).substr($str,1));
    }
}


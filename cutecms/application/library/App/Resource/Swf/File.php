<?php
class App_Resource_Swf_File {

    protected static $_handles = array();

    protected static function _open($fname, $mode = NULL)
    {
        if ( ! in_array($fname, array_keys(self::$_handles))) {
            if ($mode === NULL) {
                $mode = 'rb';
            }
            self::$_handles[$fname] = fopen($fname, $mode);
        }
        return self::$_handles[$fname];
    }

    protected static function _getH($fname, $mode = NULL)
    {
        return self::_open($fname, $mode);
    }

    public static function read($fname, $len)
    {
        $data = fread(self::_getH($fname), $len);
        return $data;
    }

    public static function write($fname, $data, $mode = NULL)
    {
        $res = fwrite(self::_getH($fname, $mode), $data);
        return $res;
    }

    public static function rewind($fname, $mode = NULL)
    {
        $res = rewind(self::_getH($fname, $mode));
        return $res;
    }


    public static function close($fname, $mode = NULL)
    {
        $res = fclose(self::_getH($fname, $mode));
        return $res;
    }
}

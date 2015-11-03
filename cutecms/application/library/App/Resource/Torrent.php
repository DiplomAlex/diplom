<?php

class App_Resource_Torrent extends App_Resource_Abstract
{

    const FILE_EXTENSION = 'torrent';

    public function onMoveUploaded($file)
    {
        $array = self::bdecode(App_Resource::getUploadsPath($file['filename']));
        $file['size'] = $array['info']['length'];
        $file['info_hash'] = sha1(self::bencode($array['info']), TRUE);
        return $file;
    }

    public function isProcessable($file)
    {
        if (is_array($file)) {
            $name = $file['filename'];
        }
        else {
            $name = $file;
        }
        $info = pathinfo($name);
        $result = (bool) (strtolower($info['extension']) == self::FILE_EXTENSION);
        return $result;
    }

    public static function bencode($array)
    {
        $string = '';
        $encoder = new App_Resource_Torrent_BEncode;
        $encoder->decideEncode($array, $string);
        return $string;
    }

    /**
     * bdecode("d8:announce44:http://www. ... e");
     */
    public static function bdecode($wholefile)
    {

        if (file_exists($wholefile)) {
            $h = fopen($wholefile, 'rb');
            $data = '';
            while ( ! feof($h)) {
                $data .= fread($h, 1024100);
            }
            fclose($h);
        }
        else {
            $data = $wholefile;
        }

        $decoder = new App_Resource_Torrent_BDecode;
        $return = $decoder->decodeEntry($data);
        return $return[0];
    }

}
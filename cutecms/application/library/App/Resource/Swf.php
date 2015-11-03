<?php

class App_Resource_Swf extends App_Resource_Abstract
{

    const FILE_EXTENSION = 'swf';

    public function onMoveUploaded($file)
    {
        $fullPath = App_Resource::getUploadsPath($file['filename']);
        if (file_exists($fullPath)) {
            list($file['width'], $file['height']) = self::getSizes($fullPath);
        }

        return $file;
    }

    public static function getSizes($filename)
    {
        $sizes = array();

        if (file_exists($filename) AND ( ! is_dir($filename))) {
            list($sizes['width'], $sizes['height']) = GetImageSize($filename);

            if (( ! $sizes['width']) OR ( ! $sizes['height'])) {
                $flash = new App_Resource_Swf_Driver($filename);
                $rect = $flash->getMovieSize();
                $sizes['width'] = $rect['width'];
                $sizes['height'] = $rect['height'];
            }
        }
        return $sizes;
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

}
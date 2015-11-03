<?php

class App_Resource
{

    protected static $_types = array('image', 'swf', 'torrent');

    /**
     * recieve preview filename from original filename
     */
    public static function getPreviewName($filename, $num = NULL)
    {
        $info = pathinfo($filename);
        if ( ! empty($info['dirname']) AND ($info['dirname'] !== '.')) {
            $info['dirname'] .= '/';
        }
        else {
            $info['dirname'] = '';
        }
        $preview = $info['dirname'] . 'prv'.self::getPreviewSuffix($num).'_' . $info['basename'];
        return $preview;
    }

    public static function getPreviewSuffix($num = NULL)
    {
        if ( (int) $num < 2) {
            $suffix = '';
        }
        else {
            $suffix = $num;
        }
        return $suffix;
    }

    /**
     * determine if the file is image
     */
    public static function isImage($filename)
    {
        $img = new App_Resource_Image;
        return $img->isProcessable($filename);
    }

    public static function getUploadsPath($fileName, $withFile = TRUE)
    {

        $info = pathinfo($fileName);
        $rawExt = $info['extension'];
        if (empty($rawExt)) {
            $rawExt = 'uploads';
        }
        $config = Zend_Registry::get('config');
        $appBase = rtrim(APPLICATION_PUBLIC, '/');
        $path = $appBase . '/uploads';
        $testPath = $path . '/' . $rawExt;
        if (file_exists($testPath)) {
            $path = $testPath;
        }
        if ($withFile) {
            $path .= '/'.$fileName;
        }
        return $path;
    }


    public static function getUploadsUrl($fileName, $withFile = TRUE)
    {
        if (empty($fileName)) return NULL;
        $info = pathinfo($fileName);
        $rawExt = $info['extension'];
        if (empty($rawExt)) {
            $rawExt = 'uploads';
        }
        $config = Zend_Registry::get('config');
        $appBase = rtrim(APPLICATION_BASE, '/');
        $path = $appBase . '/uploads';
        $testPath = $path . '/' . $rawExt;
        if (file_exists(APPLICATION_PUBLIC . '/uploads/' . $rawExt)) {
            $path = $testPath;
        }

        if ($withFile) {
            $path .= '/'.$fileName;
        }
        return $path;
    }



    /**
     * move uploaded file to uploads dir and rename it
     * @param array as $_FILES['somename']
     * @return array
     */
    public static function moveUploaded(array $file)
    {
        if (( ! array_key_exists('grabbed', $file)) OR ( ! $file['grabbed'])) {        
            $ext_arr = explode('.', $file['name']);
            if (count($ext_arr)>1) {
                $rawExt = array_pop($ext_arr);
                $ext = '.' . $rawExt;
            }
            else {
                $ext = '';
            }
            $file['filename'] = md5(uniqid()) . $ext;
    
            move_uploaded_file($file['tmp_name'], self::getUploadsPath($file['filename']));
        }

        $file = self::_triggerOnMoveUploaded($file);

        $file['size'] = filesize(self::getUploadsPath($file['filename']));

        return $file;
    }

    protected static function _triggerOnMoveUploaded(array $file)
    {
        foreach (self::$_types as $type) {
            $class = 'App_Resource_'.ucfirst($type);
            $obj = new $class;
            if ($obj->isProcessable($file)) {
                break;
            }
        }
        if (isset($obj)) {
            $file = $obj->onMoveUploaded($file);
        }
        return $file;
    }


    /**
     * manipulate original file to make preview
     * @param array $_FILES['resource']
     * @param int preview number
     * @return array
     */
    public function preparePreview(array $file, $num = NULL)
    {
        $found = FALSE;
        foreach (self::$_types as $type) {
            $class = 'App_Resource_'.ucfirst($type);
            $obj = new $class;
            if ($obj->isProcessable($file)) {
                $found = TRUE;
                break;
            }
        }
        if (isset($obj) AND ($found === TRUE)) {
            $preview = $obj->onPreparePreview($file, $num);
        }
        else {
            $preview = NULL;
        }
        return $preview;
    }

}
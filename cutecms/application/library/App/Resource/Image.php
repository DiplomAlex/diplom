<?php

class App_Resource_Image extends App_Resource_Abstract
{

    protected static $_imageExtensions = array('jpg', 'jpeg', 'gif', 'png', 'bmp');


    public function isProcessable($file)
    {
        if (is_array($file)) {
            $name = $file['filename'];
        }
        else {
            $name = $file;
        }
        $info = pathinfo($name);
        $result = (bool) (in_array(strtolower($info['extension']), self::$_imageExtensions));
        return $result;
    }

    public static function getExtensions()
    {
    	return self::$_imageExtensions;
    }


    public function onMoveUploaded($file)
    {
        list($file['width'], $file['height']) = GetImageSize(App_Resource::getUploadsPath($file['filename']));
        return $file;
    }



    /**
     * manipulate original image to make preview
     * @param array $_FILES['resource'] + previewDimensions as array(1=>array(width=>, height=>), ...)
     * @param int preview number
     * @return array
     */
    public function onPreparePreview(array $file, $num = 1)
    {
            $img_height = $file['height'];
            $img_width = $file['width'];

            //Scale by larger edge
            $_prv_width = $file['previewDimensions'][$num-1]['width'];
            $_prv_height = $file['previewDimensions'][$num-1]['height'];

            if ((int)$_prv_height && (int)$_prv_width){
                if ($img_width / $_prv_width > $img_height / $_prv_height) $file['previewDimensions'][$num-1]['height'] = NULL;
                else $file['previewDimensions'][$num-1]['width'] = NULL;
            }
            //end of scale

            $prv_width = $file['previewDimensions'][$num-1]['width'];

            if ($prv_width) {
                if ( ! (int) @$file['previewDimensions'][$num-1]['height'])
                    $prv_height = $prv_width * $img_height / $img_width;
                else {
                    $prv_height = $file['previewDimensions'][$num-1]['height'];
                }
            }
            else {
                $prv_height = $file['previewDimensions'][$num-1]['height'];                
                if ( ! (int) $prv_height) {
                    throw new App_Exception(__CLASS__.'::'.__FUNCTION__.' says that preview width and height are zero in config.ini (kernel + backend modules + frontend module) for preview number '.$num);
                }
                else {
                    $prv_width = $prv_height * $img_width / $img_height;
                }                
            }

            if ($img_height<$img_width) $mult_koef = $img_height/$prv_height;
            else  $mult_koef = $img_width/$prv_width;

            $wm_koef = $prv_width*$mult_koef/$img_width;
            $hm_koef = $prv_height*$mult_koef/$img_height;

            if (round($wm_koef,2)==1 && $hm_koef>1) {
                $wm_koef = 1 / $hm_koef;
                $hm_koef = 1;
            }
            elseif (round($hm_koef,2)==1 && $wm_koef>1) {
                $hm_koef = 1 / $wm_koef;
                $wm_koef = 1;
            }

            $src_width = $img_width * $wm_koef;
            $src_height = $img_height * $hm_koef;


            /********************************************/
            $fd = fopen(App_Resource::getUploadsPath($file['filename']), "rb");
            $img_data = fread($fd, filesize(App_Resource::getUploadsPath($file['filename'])));
            fclose($fd);
            $original_img = ImageCreateFromString($img_data);
            //        $image = ImageCreateFromJPEG($img_tmp);
            $preview_img = ImageCreateTrueColor($prv_width, $prv_height);
            if (function_exists("ImageCopyResampled")) {
                ImageCopyResampled($preview_img, $original_img, 0, 0, 0, 0,
                                    $prv_width, $prv_height, $src_width, $src_height);
            }
            else {
                ImageCopyResized($preview_img, $original_img, 0, 0, 0, 0,
                                    $prv_width, $prv_height, $src_width, $src_height);
            }

            $preview = array(
                'filename' => App_Resource::getPreviewName($file['filename'], $num),
                //'name' => basename(self::getPreviewName($file['filename'])),
                'width' => $prv_width,
                'height' => $prv_height,
            );
            
            ImageJPEG($preview_img, App_Resource::getUploadsPath($preview['filename']), 100);

                // Clean up
            ImageDestroy($preview_img);
            /********************************************/

            return $preview;
    }



    /**
     * just add watermark
     */
    protected function _addWatermark($filename, $watermark)
    {
        $wmPathInfo = pathinfo($watermark);
        $mark_func_name = 'imagecreatefrom'.strtolower($wmPathInfo['extension']);
        $mark = $mark_func_name($watermark);
        //$mark = imagecreatefromgif($watermark);
        list($mwidth, $mheight) = getimagesize($watermark);
        //$img = imagecreatefrompng($filename);
        $img = imagecreatefromstring(file_get_contents($filename));
        //echo '{error:"'.'passed2'.'"}';exit;
        list($iwidth, $iheight) = getimagesize($filename);
        imagecopy($img, $mark, ($iwidth-$mwidth)/2, ($iheight-$mheight)/2+$mheight,
            0, 0, $mwidth, $mheight);
        imagejpeg($img, $filename);
        //imagepng($img, $filename);
    }

}



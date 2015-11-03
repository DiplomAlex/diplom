<?php

class View_Helper_Html_Img extends Zend_View_Helper_Abstract
{

    /**
     * draw html img tag only if image file exists
     *
     * @param string filename in full path format (if starts from / or http://) otherwise - in format of the resources table - relative path to http://domain/root/uploads
     * @param string tag attributes
     * @return string html
     */
    public function html_Img($filename, $attribs = '', $fullImageFilename = NULL)
    {
        $html = '';
        if ( ! empty($filename)) {
            if ((substr($filename, 0, 1) != '/') AND (substr($filename, 0, 7) != 'http://')) {
                $filename = App_Resource::getUploadsUrl($filename);
            }
            if ($fullImageFilename !== NULL) {
                if ((substr($fullImageFilename, 0, 1) != '/') AND (substr($fullImageFilename, 0, 7) != 'http://')) {
                    $fullImageFilename = App_Resource::getUploadsUrl($fullImageFilename);
                }

                $html .= '<a target="_blank" href="'.$fullImageFilename.'">';
            }
            $html .= '<img src="'.$filename.'" '.$attribs.'/>';
            if ($fullImageFilename !== NULL) {
                $html .= '</a>';
            }
        }
        return $html;
    }

}
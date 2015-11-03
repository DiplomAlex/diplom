<?php

class View_Helper_AdminListSorting extends Zend_View_Helper_Abstract
{

    /**
     * @param array parameter to add to url
     */
    public function adminListSorting(array $params = array())
    {
        $htmlAttrs = '';
        if (array_key_exists('class', $params)) {
            $htmlAttrs = 'class="'.$params['class'].'"';
            unset($params['class']);
        }
        if (array_key_exists('htmlAttribs', $params)) {
            $htmlAttrs .= ' '. $params['htmlAttribs'];
            unset($params['htmlAttribs']);
        }
        
        if (array_key_exists('hrefFirst', $params)) {
            $hrefFirst = $params['hrefFirst'];
        }
        else {
            $arrFirst = array('position'=>'first') + $params;
            $hrefFirst = $this->view->url($arrFirst);
        }

        if (array_key_exists('hrefPrev', $params)) {
            $hrefPrev = $params['hrefPrev'];
        }
        else {
            $arrPrev = array('position'=>'prev') + $params;
            $hrefPrev = $this->view->url($arrPrev);
        }
        
        if (array_key_exists('hrefNext', $params)) {
            $hrefNext = $params['hrefNext'];
        }
        else {
            $arrNext = array('position'=>'next') + $params;
            $hrefNext = $this->view->url($arrNext);
        }
        
        if (array_key_exists('hrefLast', $params)) {
            $hrefLast = $params['hrefLast'];
        }
        else {
            $arrLast = array('position'=>'last') + $params;
            $hrefLast = $this->view->url($arrLast);
        }
        
        $html = '
<span style="white-space:nowrap">
<a '.$htmlAttrs.' href="'.$hrefFirst.'"><i class="icon-circle-arrow-up"></i></a>&nbsp;
<a '.$htmlAttrs.' href="'.$hrefPrev.'"><i class="icon-arrow-up"></i></a>&nbsp;
<a '.$htmlAttrs.' href="'.$hrefNext.'"><i class="icon-arrow-down"></i></a>&nbsp;
<a '.$htmlAttrs.' href="'.$hrefLast.'"><i class="icon-circle-arrow-down"></i></a>
</span>
                ';
        return $html;
    }

}
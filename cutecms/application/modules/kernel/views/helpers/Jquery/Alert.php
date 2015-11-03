<?php

/**
 * Helper adds to headScript js for alert box or just returns it
 * and also returns js for showing this box
 */

class View_Helper_Jquery_Alert extends Zend_View_Helper_Abstract
{
    
    /**
     * @var bool
     */
    protected static $_inited = FALSE;

    /**
     * @param string $boxId
     * @param string $text
     * @param bool $addToHeadScript
     * @param bool $addJqueryUI
     * @param string $height
     * @return mixed string|View_Helper_Jquery_Alert
     */
    public function jquery_Alert($boxId = NULL, $text = NULL, $addToHeadScript = TRUE, $addJqueryUI = TRUE, $height = '50px')
    {
        if ($boxId === NULL) {
            return $this;
        }
        $js = '
            $(function(){
                $("body").append("<div style=\"display:none;\" id=\"'.$boxId.'\"><p>'.$text.'</p></div>");
                $("#'.$boxId.'").dialog({autoOpen: false, height: "'.$height.'"});
            });
        ';
        if ($addToHeadScript) {
            $this->view->headScript('SCRIPT', $js);
            if ( ! self::$_inited) {
                self::$_inited = TRUE;
                $this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/jquery-ui.js');
                if ($addJqueryUI === TRUE) {
                    $this->view->headLink(array('type' => 'text/css','rel' => 'stylesheet','href' => $this->view->stdUrl(array('reset'=>TRUE)) . 'js/jquery/theme/ui.core.css'));
                    $this->view->headLink(array('type' => 'text/css','rel' => 'stylesheet','href' => $this->view->stdUrl(array('reset'=>TRUE)) . 'js/jquery/theme/ui.theme.css'));
                    $this->view->headLink(array('type' => 'text/css','rel' => 'stylesheet','href' => $this->view->stdUrl(array('reset'=>TRUE)) . 'js/jquery/theme/ui.dialog.css'));
                }
            }
        }
        return $js;
    }
    
    /**
     * @param string $boxId
     * @param bool $autoHide
     * @return string
     */
    public function getShowJs($boxId, $autoHide = TRUE, $timeout = 1000)
    {
        $js = '$("#'.$boxId.'").dialog("open");';
        if ($autoHide === TRUE) {
            $js .= 'setTimeout(function(){$("#'.$boxId.'").dialog("close");}, '.$timeout.');';
        }
        return $js;
    }
    
}
<?php

class View_Helper_Jquery_Bouncebox extends Zend_View_Helper_Abstract
{
    
    public function jquery_Bouncebox($boxId, $text, $addToHeadScript = TRUE)
    {
        $this->view->headScript('FILE', $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/bouncebox/bouncebox-plugin/jquery.easing.1.3.js');
        $this->view->headScript('FILE', $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/bouncebox/bouncebox-plugin/jquery.bouncebox.1.0.js');
        $this->view->headStyle('
            #'.$boxId.'{
                background:url("'.$this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/bouncebox/img/box_bg.jpg") repeat-x center top #fcfcfc;
                height:70px;
                padding:20px;
                margin-top:-10px;
                padding-top:30px;
                width:400px;
                border:1px solid #fcfcfc;
                color:#494848;
                text-shadow:1px 1px 0 white;
                font-family:"Myriad Pro",Arial,Helvetica,sans-serif;
            }
            
            #'.$boxId.' p{
                line-height: 50px;
                font-size:25px;
                background:url("'.$this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/bouncebox/img/warning.png") no-repeat 10px center;
                padding-left:90px;
            }
            
            #'.$boxId.' p b{
                font-size:52px;
                display:block;
            }
            
            #'.$boxId.'{
                -moz-border-radius:10px;
                -webkit-border-radius:10px;
                border-radius:10px;
            }        
        ');
        $js = '
            $(function(){
                $("body").append("<div id=\"'.$boxId.'\"><p>'.$text.'</p></div>");
                $("#'.$boxId.'").bounceBox();
            });
        ';
        if ($addToHeadScript) {
            $this->view->headScript('SCRIPT', $js);
        }
        return $js;
    }
    
}
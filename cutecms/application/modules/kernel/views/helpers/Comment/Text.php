<?php

class View_Helper_Comment_Text extends Zend_View_Helper_Abstract
{
    
    const DEFAULT_SHORT_TEXT_LENGTH = 100;
    
    protected $_shortTextLength = NULL;
    
    protected static $_headJsInited = FALSE;
    
    public function comment_Text(Model_Object_Interface $comment = NULL, $shortenned = TRUE) 
    {
        if ($comment === NULL) {
            return $this; 
        }
        
        $this->_headJsInit();
        
        $uid = uniqid('comment');
        $html = '';
        if ($comment->subject) {
            $html .= '<strong>'.$comment->subject.'</strong>';
        }
        if ($shortenned === TRUE) {
            $short = App_Utf8::substr($comment->text, 0, $this->_getShortTextLength());
            if (strlen($short) == strlen($comment->text)) {
                $html .=  $comment->text;
            }
            else {
                $rest = App_Utf8::substr($comment->text, $this->_getShortTextLength());
                $html .=  $short . '<a href="#" class="comment-extender" uid="'.$comment->id.'">...</a><div class="comment-extender-text" uid="'.$comment->id.'" style="display:none;visibility:hidden;">'.$rest.'</div>' ;
            }
        }
        else {
            $html .= $comment->text;
        }
        return $html;
    }
    
    protected function _headJsInit()
    {
        if ( ! self::$_headJsInited) {
            self::$_headJsInited = TRUE;
            $this->view->headScript('SCRIPT', '
                $(function(){
                    $(".comment-extender").click(function(e){
                        e.preventDefault();
                        var $this = $(this);
                        var uid = $this.attr("uid");
                        var div = $(".comment-extender-text[uid="+uid+"]");
                        div.show();
                        $this.replaceWith(div.html());
                        div.replaceWith("");
                    });
                });
            ');
        }
    }
    
    protected function _getShortTextLength()
    {
        if ($this->_shortTextLength === NULL) {
            $this->_shortTextLength = self::DEFAULT_SHORT_TEXT_LENGTH;
        }
        return $this->_shortTextLength;
    }
        
    public function setShortTextLength($length)
    {
        $this->_shortTextLength = $length;
        return $this;
    }
    
}
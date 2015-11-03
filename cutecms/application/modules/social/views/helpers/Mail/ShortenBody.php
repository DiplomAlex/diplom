<?php

class Social_View_Helper_Mail_ShortenBody extends Zend_View_Helper_Abstract
{

    public function mail_ShortenBody($text)
    {
        $text = strip_tags($text);
        $len = App_Utf8::strlen($text);
        $maxLen = Zend_Registry::get('social_config')->mail->previewBodyLength;
        if ($len <= $maxLen) {
            $result = $text;
        }
        else {
            $result = App_Utf8::substr($text, 0, $maxLen) . ' <b>...</b>';
        }
        return $result;
    }

}
<?php

class App_Mail extends Zend_Mail
{

    public static function factory($charset = 'UTF-8')
    {
        $obj = new self($charset);
        return $obj;
    }

    public function __construct($charset = 'UTF-8')
    {
        $this->_charset = $charset;
        self::setDefaultTransport(new Zend_Mail_Transport_Smtp('smtp.yandex.ru', array(
            'auth'      =>  'login',
            'username'  =>  Zend_Registry::get('config')->emailSMTP->username,
            'password'  =>  Zend_Registry::get('config')->emailSMTP->password,
            'port'      =>  465,
            'ssl'       =>  'ssl'
        )));
    }


    /**
     * Encode a given string with the QUOTED_PRINTABLE mechanism
     *
     * @param string $str
     * @param int $lineLength Defaults to {@link LINELENGTH}
     * @param int $lineEnd Defaults to {@link LINEEND}
     * @return string
     */
    protected function encodeQuotedPrintable($str,
    $lineLength = Zend_Mime::LINELENGTH)
    {
        $out = '';
        $str = str_replace('=', '=3D', $str);
        $str = str_replace(Zend_Mime::$qpKeys, Zend_Mime::$qpReplaceValues, $str);
        $str = rtrim($str);
        // Split encoded text into separate lines
        while ($str) {
            $ptr = strlen($str);
            if ($ptr > $lineLength) {
                $ptr = $lineLength;
            }
            // Ensure we are not splitting across an encoded character
            $pos = strrpos(substr($str, 0, $ptr), '=');
            if ($pos !== false && $pos >= $ptr - 2) {
                $ptr = $pos;
            }
            // Check if there is a space at the end of the line and rewind
            if ($ptr > 0 && $str[$ptr - 1] == ' ') {
                --$ptr;
            }
            // Add string and continue
            $out .= substr($str, 0, $ptr);
            $str = substr($str, $ptr);
        }
        return $out;
    }

    /**
     * Encode header fields
     *
     * Encodes header content according to RFC1522 if it contains non-printable
     * characters.
     *
     * @param  string $value
     * @return string
     */
    protected function _encodeHeader($value)
    {
        if (Zend_Mime::isPrintable($value)) {
            return $value;
        } else {
            /**
             * Next strings fixes the problems
             * According to RFC 1522 (http://www.faqs.org/rfcs/rfc1522.html)
             */
            $quotedValue = '';
            $count = 1;
            for ($i=0; strlen($value)>$i;$i++) {
                if ($value[$i] == '?' or $value[$i] == '_' or $value[$i] == ' ') {
                    $quotedValue .= str_replace(array('?', ' ', '_'), array('=3F', '=20', '=5F'), $value[$i]);
                } else {
                    $quotedValue .= $this->encodeQuotedPrintable($value[$i]);
                }
                if (strlen($quotedValue)>$count*Zend_Mime::LINELENGTH) {
                    $count++;
                    $quotedValue .= "?=\n =?". $this->_charset . '?Q?';
                }
            }
            return '=?' . $this->_charset . '?Q?' . $quotedValue . '?=';
        }
    }

}
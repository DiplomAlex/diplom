<?php

class App_Mail_Transport_Log extends Zend_Mail_Transport_Abstract
{

    public function _sendMail()
    {
        $result = mail(
                $this->recipients,
                $this->_mail->getSubject(),
                $this->body,
                $this->header);

        $logText =   'Recipients: '.$this->recipients."\r\n\r\n"
                   . 'Subject: '.$this->_mail->getSubject()."\r\n\r\n"
                   . 'Header: '.$this->header."\r\n\r\n"
                   . 'Body: '.$this->body."\r\n"
                   ;

        App_Debug::log($logText, 'email sent to logfile');
    }


    /**
     * thieved from Zend_Mail_Transport_Sendmail
     */
    protected function _prepareHeaders($headers)
    {
        if (!$this->_mail) {
            /**
             * @see Zend_Mail_Transport_Exception
             */
            require_once 'Zend/Mail/Transport/Exception.php';
            throw new Zend_Mail_Transport_Exception('_prepareHeaders requires a registered Zend_Mail object');
        }

        // mail() uses its $to parameter to set the To: header, and the $subject
        // parameter to set the Subject: header. We need to strip them out.
        if (0 === strpos(PHP_OS, 'WIN')) {
            // If the current recipients list is empty, throw an error
            if (empty($this->recipients)) {
                /**
                 * @see Zend_Mail_Transport_Exception
                 */
                require_once 'Zend/Mail/Transport/Exception.php';
                throw new Zend_Mail_Transport_Exception('Missing To addresses');
            }
        } else {
            // All others, simply grab the recipients and unset the To: header
            if (!isset($headers['To'])) {
                /**
                 * @see Zend_Mail_Transport_Exception
                 */
                require_once 'Zend/Mail/Transport/Exception.php';
                throw new Zend_Mail_Transport_Exception('Missing To header');
            }

            unset($headers['To']['append']);
            $this->recipients = implode(',', $headers['To']);
        }

        // Remove recipient header
        unset($headers['To']);

        // Remove subject header, if present
        if (isset($headers['Subject'])) {
            unset($headers['Subject']);
        }

        // Prepare headers
        parent::_prepareHeaders($headers);

        // Fix issue with empty blank line ontop when using Sendmail Trnasport
        $this->header = rtrim($this->header);
    }


}
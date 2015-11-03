<?php

class CommentController extends Zend_Controller_Action
{
    
    public function init()
    {
        App_Event::factory('Controller__init', array($this))->dispatch();
    }
    
    public function addAction()
    {
        $name = $this->_getParam('name');
        $email = $this->_getParam('email');
        $text = $this->_getParam('text');
        $contentType = $this->_getParam('contentType');
        $contentId = $this->_getParam('contentId');
        Model_Service::factory('comment')->addFromValues(array(
            'adder_name' => $name,
            'adder_email' => $email,
            'text' => $text,
            'content_type' => $contentType,
            'content_id' => $contentId,
        ));        
        $url = $this->_decodeReturnUrl($this->_getParam('goto')).'?'.http_build_query(array(
            'name' => $name,
            'email' => $email,
            'text' => $text,
        ));
        $this->getHelper('Redirector')->gotoUrlAndExit($url);
    }
    
    
    protected function _decodeReturnUrl($url)
    {
        $len = strlen($url);
        $pieceLeftLen = 5;
        $pieceRightLen = 5;
        $pieceLastLen = 2;
        $pieceLeft = substr($url, 0, $pieceLeftLen);
        $pieceRight = substr($url, -1 * $pieceLastLen - $pieceRightLen, $pieceRightLen);
        $middle = substr($url, $pieceLeftLen, $len - $pieceLeftLen - $pieceLastLen - $pieceRightLen);
        $pieceLast = substr($url, -1 * $pieceLastLen);
        $url = $pieceRight . $middle . $pieceLeft . $pieceLast;
        $decoded = base64_decode($url);
        return $decoded;
    }
    
    
}
<?php

class View_Helper_Box_Comments extends Zend_View_Helper_Abstract
{
    
    public function box_Comments($contentType, $contentId, $contentTitle, $afterPostRedirectUrl = '')
    {
        $contentService = $this->_getServiceByType($contentType);
        $this->view->contentType = $contentType;
        $this->view->contentId = $contentId;
        $this->view->comments = $contentService->getHelper('Comment')->getLinkedToContent($contentId);
        $this->view->contentTitle = $contentTitle;
        $this->view->afterPostRedirectUrl = $this->_encodeReturnUrl($afterPostRedirectUrl);
        $this->view->postUrl = $this->view->url(array(), 'comment-post');
        $html = $this->view->render('box/comments.phtml');
        return $html;
    }

    protected function _getServiceByType($contentType)
    {
        $service = Model_Service::factory($contentType);        
        return $service;
    }
    
    protected function _encodeReturnUrl($url)
    {
        $url = base64_encode($url);
        $len = strlen($url);
        $pieceLeftLen = 5;
        $pieceRightLen = 5;
        $pieceLastLen = 2;
        $pieceLeft = substr($url, 0, $pieceLeftLen);
        $pieceRight = substr($url, -1 * $pieceLastLen - $pieceRightLen, $pieceRightLen);
        $middle = substr($url, $pieceLeftLen, $len - $pieceLeftLen - $pieceLastLen - $pieceRightLen);
        $pieceLast = substr($url, -1 * $pieceLastLen);
        $url = $pieceRight . $middle . $pieceLeft . $pieceLast;
        return $url;
    }
    
    
}

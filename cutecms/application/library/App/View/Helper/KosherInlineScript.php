<?php

class App_View_Helper_KosherInlineScript extends Zend_View_Helper_Abstract
{

    protected $_useCdata = TRUE;

    public function kosherInlineScript($js)
    {
        $escapeStart = ($this->_useCdata) ? '//<![CDATA[' : '//<!--';
        $escapeEnd   = ($this->_useCdata) ? '//]]>'       : '//-->';
        $xhtml = $this->view->inlineScript()->itemToString(
                        $this->view->inlineScript()->createData('text/javascript', array(), $js),
                        '', $escapeStart, $escapeEnd);
        return $xhtml;
    }

}
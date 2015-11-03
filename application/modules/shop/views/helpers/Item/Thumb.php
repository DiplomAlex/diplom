<?php

class Shop_View_Helper_Item_Thumb extends Zend_View_Helper_Abstract
{
    
    const STYLE_SMALL      = 'small';
    const STYLE_MIDDLE     = 'middle';
    const STYLE_BIG        = 'big';
    
    const CLASS_BUY_BUTTON = 'item-thumb-buy-button';
    const ATTR_HREF        = 'href';
    const ATTR_ITEM_ID     = 'itemId';
    
    protected $_scripts = array(
        self::STYLE_BIG    => 'catalog/item/thumb/big.phtml',
        self::STYLE_MIDDLE => 'catalog/item/thumb/middle.phtml',
        self::STYLE_SMALL  => 'catalog/item/thumb/small.phtml',
    );
    
    protected static $_headScriptAdded = FALSE;
    
    /**
     * 
     * @param Model_Object_Interface $item
     * @param string $style
     * @return string
     */
    public function item_Thumb(Model_Object_Interface $item, $style = self::STYLE_SMALL, $scripts = NULL)
    {
        if ( ! $this->_isValidStyle($style)) {
            throw new Zend_View_Exception('Wrong style for item_Thumb helper - "'.$style.'"');
        }
        if (is_array($scripts)) {
            $this->_setScripts($scripts);
        }
        if ( ! self::$_headScriptAdded) {
            $this->_addHeadScript(self::CLASS_BUY_BUTTON, self::ATTR_HREF, self::ATTR_ITEM_ID);
            self::$_headScriptAdded = TRUE;
        }
        $this->view->item = $item;
        $this->view->classBuyButton = self::CLASS_BUY_BUTTON;
        $this->view->attrHref = self::ATTR_HREF;
        $this->view->attrItemId = self::ATTR_ITEM_ID;
        $html = $this->view->render($this->_scripts[$style]);
        return $html;
    }

    protected function _addHeadScript($classBuyButton, $attrHref, $attrItemId)
    {
        $this->view->headScript(Zend_View_Helper_HeadScript::SCRIPT, '
        	$(function(){
        		$(".'.$classBuyButton.'").click(function(e){
        			var $this = $(this);
        			e.preventDefault();
        			$.post($this.attr("'.$attrHref.'"), {qty: 1, id: $this.attr("'.$attrItemId.'")}, function(resp){
        				triggerGlobalEvent(EVENT_SHOPPING_CART_UPDATED);
    				});
    			});
    		});
        ');
    }
    
    
    protected function _setScripts(array $scripts)
    {
        if ( ! $this->_isValidScripts($scripts)) {
            throw new Zend_View_Exception('Wrong scripts array, it should contain '.count($this->_scripts).' string elements '
                                         .'with keys - '.implode(',', array_keys($this->_scripts)));
        }
        $this->_scripts = $scripts;
        return $this;
    }
    
    /**
     * checks if $style is one of STYLE_ consts
     * @param string $style
     * @return bool
     */    
    protected function _isValidStyle($style)
    {
        $result = in_array($style, array(self::STYLE_BIG, self::STYLE_MIDDLE, self::STYLE_SMALL));
        return $result;
    } 
    
    /**
     * checks if $scripts array is contains the same keys as current $this->_scripts
     * @param array $scripts
     * @return bool
     */
    protected function _isValidScripts(array $scripts)
    {
        $result = TRUE;
        if (count($scripts) != count($this->_scripts)) {
            $result = FALSE;
        }
        else {
            foreach ($this->_scripts as $key=>$val) {
                if ( ! array_key_exists($key, $scripts)) {
                    $result = FALSE;
                    break;
                }
            }
        }
        return $result;
    }
    
    
}
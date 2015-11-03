<?php

class Catalog_View_Helper_Item_StarRating extends Zend_View_Helper_Abstract
{
    
    protected $_starCount = 5; 
    
    protected static $_headerInited = FALSE;
    
    public function item_StarRating(Model_Object_Interface $item = NULL, $readOnly = FALSE)
    {
        if ($item === NULL) {
            return $this;
        }
        else {
            return $this->render($item, $readOnly);
        }
    }
    
    public function setStarCount($count)
    {
        $this->_starCount = $count;
        return $this;
    }
    
    public function render(Model_Object_Interface $item, $readOnly = FALSE)
    {
        if ( ! self::$_headerInited) {
            $this->view->headLink(array('type'=>'text/css',
            							'rel'=>'stylesheet',
            							'href'=>$this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/star-rating/jquery.rating.css'));
            $this->view->headScript(Zend_View_Helper_HeadScript::FILE, 
                                    $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/star-rating/jquery.Metadata.js');
            $this->view->headScript(Zend_View_Helper_HeadScript::FILE, 
                                    $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/star-rating/jquery.rating.js');
            self::$_headerInited = TRUE;
        }        
        $value = $item->votes?ceil($item->rate / $item->votes):0;
        $uid = uniqid('starRating_item_');
        if ($readOnly === TRUE) {
            $disabledStr = 'disabled="disabled"';
        }
        else {
            $disabledStr = '';
        }
        $html = '';        
        for ($i = 1; $i<=$this->_starCount; $i++) {
            if ($value >= $i) {
                $checkedStr = 'checked="checked"';
            }
            else {
                $checkedStr = '';
            }
            $html .= '<input value="'.$i.'" type="radio" name="'.$uid.'" itemId="'.$item->id.'" class="star" '.$checkedStr.' '.$disabledStr.'>';
        }
            $html .= $this->view->kosherInlineScript('
        			$(".star").rating({
        				starWidth: 15,
        				required: true,
        				callback:function(value, link){
        					var $radio = $(this);
            				var itemId = $radio.attr("itemId");
            				$.post("'.$this->view->stdUrl(array('reset'=>TRUE), 'vote', 'item', 'catalog').'", {id: itemId, value: value}, function(resp){
            					$(".star[itemId="+itemId+"]").rating("disable");
    						}, "json");
        				}
        			});
            ');
        return $html;
    }
    
}
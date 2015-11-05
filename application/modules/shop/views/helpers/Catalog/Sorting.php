<?php

class Shop_View_Helper_Catalog_Sorting extends Zend_View_Helper_Abstract
{
    
    public function catalog_Sorting($modeName, $currentDirection = 'ASC', $isCurrent = FALSE, $text = NULL)
    {
        $currentDirection = strtoupper($currentDirection);
        if (($currentDirection === 'ASC') OR (empty($currentDirection))) {
            $alt = $this->view->translate('sorted up');
            $imgFile = $this->view->skin()->url().'images/blue_arrow_up.png';
            $newDirection = 'DESC';
        }
        else {
            $alt = $this->view->translate('sotred down');
            $imgFile = $this->view->skin()->url().'images/blue_arrow_down.png';
            $newDirection = 'ASC';
        }
        $class = 'catalog-sorting-'.$modeName;
        $href = $this->view->stdUrl(array('mode'=>$modeName, 'direction'=>$newDirection), 'set-sorting-mode', 'catalog', 'lab');
        if ($text) {            
            if ($isCurrent) {
                $aClass = $class.' checked';
                $img = '&nbsp; <a class="'.$class.'" href="'.$href.'"><img alt="'.$alt.'" src="'.$imgFile.'"/></a>';
            }
            else { 
                $aClass = $class;
                $img = '';
            }
            $html = '<a href="'.$href.'" class="'.$aClass.'">'.$text.'</a>'.$img;
        }
        else {
            $html = '<a class="'.$class.'" href="'.$href.'"><img alt="'.$alt.'" src="'.$imgFile.'" /></a>';
        }
        $this->view->headScript(Zend_View_Helper_HeadScript::SCRIPT, '
            $(function(){
                $("a.'.$class.'").click(function(e){
                    e.preventDefault();
                    $.post($(this).attr("href"), function(resp){
                        window.location.reload();
                    });
                });
            });
        ');
        return $html;
    }
    
}
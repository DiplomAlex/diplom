<?php

class View_Helper_AdminBox_SiteSelect extends Zend_View_Helper_Abstract
{
    
    public function adminBox_SiteSelect($script = 'admin-box/site-select.phtml')
    {        
        $currentId = $this->view->currentSiteId;
        $list = $this->view->site_List(TRUE);
        if ($this->view->isAllowedSiteSelect) {
            $select = $this->view->formSelect('site_id', $currentId, array('class'=>'global-current-site'), $list);
            $this->view->siteSelect = $select;
                $this->view->headScript('SCRIPT', '                
                    $(function(){
                        $("select.global-current-site").change(function(e){
                            $.get("'.$this->view->stdUrl(NULL, 'set-site-id', 'admin-multisite', 'kernel').'", {site_id: $(this).val()}, function(resp){
                                window.location.reload();
                            });
                        });
                    });
                ');            
        }
        else {
            $this->view->siteSelect = $list[$currentId];
        }
        $xhtml = $this->view->render($script);
        return $xhtml;
    }
    
}
<?php

class Catalog_View_Helper_FormAdminItem_Attribute extends Zend_View_Helper_Abstract
{
    
    public function formAdminItem_Attribute($name, $value = NULL, $attribs = NULL)
    {
        if ( ( ! array_key_exists('id', $attribs)) OR ( ! $id = $attribs['id'])) {
            $id = $name;
        }
        if ( ( ! array_key_exists('href', $attribs)) OR ( ! $href = $attribs['href'])) {
            throw new Zend_View_Exception('href is a required option for "'.__FUNCTION__.'"()');
        }
        if ( ( ! array_key_exists('classWrap', $attribs)) OR ( ! $classWrap = $attribs['classWrap'])) {
            throw new Zend_View_Exception('classWrap is a required option for "'.__FUNCTION__.'"()');
        }
        if ( ( ! array_key_exists('classWrapEdit', $attribs)) OR ( ! $classWrapEdit = $attribs['classWrapEdit'])) {
            throw new Zend_View_Exception('classWrapEdit is a required option for "'.__FUNCTION__.'"()');
        }
        if ( ( ! array_key_exists('classList', $attribs)) OR ( ! $classList = $attribs['classList'])) {
            throw new Zend_View_Exception('classList is a required option for "'.__FUNCTION__.'"()');
        }
        if ( ( ! array_key_exists('classLinkCreate', $attribs)) OR ( ! $classLinkCreate = $attribs['classLinkCreate'])) {
            throw new Zend_View_Exception('classLinkCreate is a required option for "'.__FUNCTION__.'"()');
        }
        if ( ( ! array_key_exists('classLinkAddFromGroup', $attribs)) OR ( ! $classLinkAddFromGroup = $attribs['classLinkAddFromGroup'])) {
            throw new Zend_View_Exception('classLinkAddFromGroup is a required option for "'.__FUNCTION__.'"()');
        }        
        if ( ( ! array_key_exists('classLinkDelete', $attribs)) OR ( ! $classLinkDelete = $attribs['classLinkDelete'])) {
            throw new Zend_View_Exception('classLinkDelete is a required option for "'.__FUNCTION__.'"()');
        }
        if ( ( ! array_key_exists('classLinkSorting', $attribs)) OR ( ! $classLinkSorting = $attribs['classLinkSorting'])) {
            throw new Zend_View_Exception('classLinkSorting is a required option for "'.__FUNCTION__.'"()');
        }        
        if ( ( ! array_key_exists('classBtnSubmit', $attribs)) OR ( ! $classBtnSubmit = $attribs['classBtnSubmit'])) {
            throw new Zend_View_Exception('classBtnSubmit is a required option for "'.__FUNCTION__.'"()');
        }
        if ( ( ! array_key_exists('classBtnCancel', $attribs)) OR ( ! $classBtnCancel = $attribs['classBtnCancel'])) {
            throw new Zend_View_Exception('classBtnCancel is a required option for "'.__FUNCTION__.'"()');
        }
        if ( ! array_key_exists('contentDialogEdit', $attribs)) {
            throw new Zend_View_Exception('contentDialogEdit is a required option for "'.__FUNCTION__.'"()');
        }
        else {
            $contentDialogEdit = $attribs['contentDialogEdit'];
        }
        $titleEdit = (string) @$attribs['titleEdit'];
        
        $this->view->headLink(array('type' => 'text/css','rel' => 'stylesheet','href' => $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/theme/ui.dialog.css'));
        $xhtml = '<div class="'.$classWrap.'" name="'.$name.'" id="'.$id.'" href="'.$href.'"></div>';
        $bodyEndXhtml =  '<div class="'.$classWrapEdit.'" style="display:none">'.$contentDialogEdit.'</div>'
                        ;
        $xhtml .= $this->view->kosherInlineScript('
            if ( ! contentDialogEdit_'.$id.'_created) {
                contentDialogEdit_'.$id.'_created = true;
                $("body").append('.Zend_Json::encode($bodyEndXhtml).');
            }
        ');
        
        $this->view->headScript('SCRIPT', '
            $(function(){            
                function reloadWrapper_'.$id.'() {
                    var wrapper = $("#'.$id.'"); 
                    $.get(wrapper.attr("href"), function(resp){
                        wrapper.html(resp);
                    });
                }
                                
                $(".'.$classWrapEdit.'").dialog({autoOpen: false,
                    title: "'.$titleEdit.'", 
                    position:"top",
                    width: "600px"/*, height: "500px"*/, buttons: {
                    "'.$this->view->translate('OK').'": function() {
                        var href = $(".'.$classWrapEdit.'").data("href");
                        $.post(href, $(".'.$classWrapEdit.' form").serialize(), function(resp){
                            $(".'.$classWrapEdit.'").dialog("close");
                            reloadWrapper_'.$id.'();
                        });                    
                    },
                    "'.$this->view->translate('Cancel').'": function() {
                        $(".'.$classWrapEdit.'").data("href", null);
                        $(".'.$classWrapEdit.'").dialog("close");                    
                    }
                }});
                
                /* link "create" clicked */
                $(".'.$classWrap.' .'.$classLinkCreate.'").live("click", function(e){
                    e.preventDefault();
                    var $link = $(this);
                    /* get default values to populate */
                    $.getJSON($link.attr("hrefGet"), {hash: $link.attr("hash")}, function(resp){
                        var key;
                        for (key in resp) {
                            $(".'.$classWrapEdit.' #"+key).val(resp[key]);
                        }
                        $(".'.$classWrapEdit.' #variants").trigger("reloadGrid");                       
                        $(".'.$classWrapEdit.'").data("href", $link.attr("href"));
                        $(".'.$classWrapEdit.'").dialog("open");
                    });
                });

                /* link "add from group" clicked */
                $(".'.$classWrap.' .'.$classLinkAddFromGroup.'").live("change", function(e){
                    e.preventDefault();
                    var $this = $(this);  
                    $.post($this.attr("href"), {group: $this.val()}, function(resp){
                        reloadWrapper_'.$id.'();
                    });                    
                });
                
                /* click "delete" link */
                $(".'.$classList.' .'.$classLinkDelete.'").live("click", function(e){
                    e.preventDefault();
                    $.post($(this).attr("href"), function(resp){
                        reloadWrapper_'.$id.'();
                    });
                });
                                
                /* click sorting link */
                $(".'.$classList.' .'.$classLinkSorting.'").live("click", function(e){
                    e.preventDefault();
                    $.post($(this).attr("href"), function(resp){
                        reloadWrapper_'.$id.'();
                    });                    
                });
                
                reloadWrapper_'.$id.'();
            });
        ');
        
        return $xhtml;
    }
    
}
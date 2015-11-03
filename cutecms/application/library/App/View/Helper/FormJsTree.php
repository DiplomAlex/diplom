<?php

class App_View_Helper_FormJsTree extends Zend_View_Helper_FormElement {

    protected $_fields = array(
        'id' => 'id',
        'level' => 'tree_level',
        'text' => 'name',
        'rel' => 'rel',
    );

	public function formJsTree($name, $value = NULL, $attribs = NULL, $options = NULL) {
		//$this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/jsTree/jquery.tree.min.js');
        $this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/jsTree-1.0/jquery.jstree.js');
		/*$this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/jquery.metadata.js');
		$this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/jquery.cookie.js');
		$this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->stdUrl(array('reset'=>TRUE)).'js/sarissa.js');
		$this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/loadMask/jquery.loadmask.min.js');*/
		$this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/jsTree/plugins/jquery.tree.checkbox.js');
		/*$this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/jsTree/plugins/jquery.tree.cookie.js');
		$this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/jsTree/plugins/jquery.tree.metadata.js');
		$this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/jsTree/plugins/jquery.tree.themeroller.js');*/
		/*$this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->stdUrl(array('reset'=>TRUE)).'js/loadXMLTree.js');*/

        $treeNodes = $options;
        if (isset($attribs['fields'])) {
            $fields = $attribs['fields'];
        }
        else {
            $fields = $this->_fields;
        }

        if ( ! is_array($value)) {
            $value = array($value);
        }

        $xhtml = '<div class="xmlTreeCat" id="'.$name.'">';
        $level = -1;
        $parents = array();
        $prevNode = NULL;
        $cnt = 0;
        $opened = array();
        foreach ($treeNodes as $node) {
            $node[$fields['level']] = (int) $node[$fields['level']];
            if ($node[$fields['level']] > $level) {
                array_push($parents, $prevNode);
                $xhtml .= '<ul>';
            }
            else if ($node[$fields['level']] < $level) {
                $diff = $level - $node[$fields['level']];
                $xhtml .= str_repeat('</ul></li>', $diff);
                for ($i = 0; $i < $diff; $i++) {
                    array_pop($parents);
                }
            }
            else {
                $xhtml .= '</li>';
            }
            if (isset($node[$fields['rel']])) {
                $rel = 'rel="'.$node[$fields['rel']].'"';
            }
            else {
                $rel = '';
            }
            if (   ( ! isset($attribs['types']))
                OR ( ! isset($attribs['types'][$node[$fields['rel']]]))
                OR ( ! isset($attribs['types'][$node[$fields['rel']]]['clickable']))) {
                $clickable = TRUE;
            }
            else {
                $clickable = $attribs['types'][$node[$fields['rel']]]['clickable'];
            }
            $xhtml .=    '<li id="'.$name.'_'.$node[$fields['id']].'" '.$rel.'>'
                       . ($clickable?'<a href="#" '.(in_array($node[$fields['id']], $value)?' class="checked"':'').' rowId="'.$node[$fields['id']].'" rowName="'.$name.'[]">':'')
                       . ($clickable?'<ins>&nbsp;</ins>':'')
                       . $node[$fields['text']]
                       . ($clickable?'</a>':'')
                       . ($clickable?'<input type="checkbox" name="'.$name.'[]" id="cb_'.$name.'_'.$node[$fields['id']].'" value="'
                       . $node[$fields['id']].'" style="display: none;"'.(in_array($node[$fields['id']], $value)?' checked="checked"':'').'/>':'')
                       ;

            if (isset($attribs['nodeScript'])) {
                $xhtml .= $this->view->partial($attribs['nodeScript'], array(
                                                                             'name' => $name,
                                                                             'value' => $value,
                                                                             'node' => $node,
                                                                             'fields' => $fields,
                                                                             'attribs' => $attribs,
                                                                             ));
            }

            $level = $node[$fields['level']];
            if (in_array($node[$fields['id']], $value) AND ! empty($parents)) {
                $parent = end($parents);
                $opened []= $name.'_'.$parent[$fields['id']];
            }
            $prevNode = $node;
        }

        $xhtml .= str_repeat('</li></ul>', $level+1).'</div>';

        if (isset($attribs['types'])) {
            $typesJs = 'types: '.Zend_Json::encode($attribs['types']).',';
        }
        else {
            $typesJs = '';
        }
        if (isset($attribs['rules'])) {
            $rulesJs = 'rules: '.Zend_Json::encode($attribs['rules']).',';
        }
        else {
            $rulesJs = '';
        }

        $stdCallbackJs = 'function (n, t) {
                            var t = $.jstree.reference(n);
                            n = t.get_node(n);
                            $("#cb_"+n.attr("id")).attr("checked", n.children("a").hasClass("checked"));
                          }';
        if (isset($attribs['callback'])) {
            $callbackJs = '';
            $comma = '';
            foreach ($attribs['callback'] as $key=>$val) {
                $callbackJs .= $comma . $key .':'. $val;
            }
            if ( ! isset($attribs['callback']['onchange'])) {
                $callbackJs .= $comma . 'onchange:'.$stdCallbackJs;
            }
        }
        else {
            $callbackJs = 'onchange:'.$stdCallbackJs;
        }

        if (isset($attribs['ui'])) {
            if ( ! isset($attribs['ui']['theme_name'])) {
                $attribs['ui']['theme_name'] = 'checkbox';
            }
            $uiJs = 'ui:'.Zend_Json::encode($attribs['ui']);
        }
        else {
            $uiJs = 'ui : {
                             theme_name : "checkbox"
                     }';
        }
        if (isset($attribs['plugins'])) {
            $pluginsJs = Zend_Json::encode($attribs['plugins']);
        }
        else {
            $pluginsJs = Zend_Json::encode(array('checkbox'=>array('three_state'=>FALSE)));
        }

        
        /*
        $xhtml .= $this->view->inlineScript()->itemToString($this->view->inlineScript()->createData('text/javascript', array(), '
            $(function(){
                $("#'.$name.'").tree({
                                '.$uiJs.',
                                plugins : '.$pluginsJs.',
                                callback: {
                                    '.$callbackJs.'
                                },
                                '.$typesJs . $rulesJs.'
                                opened: '.Zend_Json::encode($opened).'
                });
            });
        '), '', '//<!--', '//-->');
        */
        
                
        /**
         * when tree is included into jquery dialog box it should be inited only after box first showing,
         * that's why setTimeout was added
         */

        $this->view->headScript('SCRIPT', '
            $(function(){            
                var loaded = false;
            
                function initTree(){ 
                    $("#'.$name.'").jstree({
                                    '.$uiJs.',
                                   // plugins : '.$pluginsJs.',
                                    plugins : [ "themes", "html_data", "checkbox", "ui" ],
                                    callback: {
                                        '.$callbackJs.'
                                    },
                                    '.$typesJs . $rulesJs.'
                                   // opened: '.Zend_Json::encode($opened).',
                                    "checkbox": {"real_checkboxes": true,
                                                "checked_parent_open" : true
                                    },
                                    "themes" : {
                                    	            "theme" : "default",
                                    	            "icons" : false
                                    	        },                                    
                    });
                }
                
                function tryToLoad() {
                    if ( ! loaded) {
                        if ($("#'.$name.'").size() >0 ) {
                            loaded = true;
                            initTree();
                        }
                        else {
                            setTimeout(tryToLoad, 1000);
                        }
                    }
                }
                
                tryToLoad();
                
                
                
            });        
        ');
        
        

		return $xhtml;
	}

}

<?php

class App_View_Helper_FormJqGrid extends Zend_View_Helper_Abstract
{

    protected static $_inited = FALSE;

    /**
     * @param string - name and id are the same here
     * @param string - not used now
     * @param array - array('params' => array(options for jqGrid), ...)
     */
    public function formJqGrid($name = NULL, $value = null, $attribs = null, $options = NULL)
    {
        /**
         * enable helper's api
         */
        if ($name === NULL) {
            return $this;
        }

        if ( ! self::$_inited) {
            $lang = Model_Service::factory('language')->getCurrent()->code2;
            $this->view->headScript('FILE', $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/jqGrid/js/i18n/grid.locale-'.$lang.'.js');
            $this->view->headScript('FILE', $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/jqGrid/js/jquery.jqGrid.min.js');
            $this->view->headLink(array('rel' => 'stylesheet','type' => 'text/css',
                                        'href' => $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/theme/ui.theme.css'));

            $this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/jquery-ui.js');
            $this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/ui/i18n/ui.datepicker-'.Model_Service::factory('language')->getCurrent()->code2.'.js');
            $this->view->headLink(array('type' => 'text/css','rel' => 'stylesheet','href' => $this->view->stdUrl(array('reset'=>TRUE)) . 'js/jquery/theme/ui.core.css'));
            //$this->view->headLink(array('type' => 'text/css','rel' => 'stylesheet','href' => $this->view->stdUrl(array('reset'=>TRUE)) . 'js/jquery/theme/ui.theme.css'));
            $this->view->headLink(array('type' => 'text/css','rel' => 'stylesheet','href' => $this->view->stdUrl(array('reset'=>TRUE)) . 'js/jquery/theme/ui.datepicker.css'));

            $this->view->headLink(array('rel' => 'stylesheet','type' => 'text/css',
                                        'href' => $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/jqGrid/css/ui.jqgrid.css'));

            self::$_inited = TRUE;
        }

        if (isset($attribs['id'])) {
            $id = $attribs['id'];
        }
        else {
            $id = $name;
        }
        $pagerId = $id.'_pager';

        $xhtml = '<table id="'.$id.'" class="scroll"></table>
                  <div id="'.$pagerId.'" class="scroll" style="text-align:center;"></div>';
        $params = array_merge($this->getDefaultParams($id, $pagerId), $attribs);
        $params = $this->_prepareParams($params);
        $params['nopager'] = NULL;
        $subgridJs = $this->_prepareSubGridJs($id, $params);
        $this->view->headScript('SCRIPT', '
            jQuery(function(){
                var gridId = "'.$id.'";
                var pagerId = "'.$pagerId.'";
                var gridSelector = "#"+gridId;
                var pagerSelector = "#"+pagerId;
                jQuery(gridSelector).jqGrid({'
                                        .'multiselect:'.$params['multiselect'].','
                                        .'width:"'.$params['width'].'",'
                                        .'height:"'.$params['height'].'",'
                                        .'url:"'.$params['url'].'",'
                                        .'datatype:"'.$params['datatype'].'",'
                                        .'mtype:"'.$params['mtype'].'",'
                                        .'rowList:'.$params['rowList'].','
                                        .'rowNum:'.$params['rowNum'].','
                                        .'pgbuttons:'.$params['pgbuttons'].','
                                        .'pginput:'.$params['pginput'].','
                                        .'multiboxonly:'.$params['multiboxonly'].','
                                        .'colNames:'.$params['colNames'].','
                                        .'colModel:'.$params['colModel']
                                        .(empty($params['onSelectRow'])?'':','.'ondblClickRow:'.$params['onSelectRow'])
                                        .(empty($params['afterInsertRow'])?'':','.'afterInsertRow:'.$params['afterInsertRow'])
                                        .(empty($params['gridComplete'])?'':','.'gridComplete:'.$params['gridComplete'])
                                        .(isset($params['nopager'])?'':','.'pager:$(pagerSelector/*"'.$params['pager'].'"*/)')
                                        .(isset($params['editurl'])?','.'editurl:"'.$params['editurl'].'"':'')
                                        .(isset($params['loadComplete'])?','.'loadComplete:'.$params['loadComplete'].'':'')
                                        .($subgridJs?','.$subgridJs:'')
                                   .'});    '

                .' var gridNav = jQuery(gridSelector).navGrid(pagerSelector, {edit:false,add:false,del:false,search:false});'
                                   
                                   
                .(!@$params['noStandartButtons']?$this->_prepareButtonsJs($id, $pagerId, $params):'')
                .(array_key_exists('addonJs', $params)?$params['addonJs']:'')
            .'});
        ');
        return $xhtml;
    }

    public function getDefaultParams($id, $pagerId)
    {
        $_defaultParams = array(
            'pager' => '#'.$pagerId,
            'rowNum' => (int) Zend_Controller_Action_HelperBroker::getStaticHelper('RowsPerPage')->getValue(),
            'rowList' => array(/*10, 20, 30, 40, 50*/),
            'datatype' => 'json',
            'mtype' => 'GET',
            'multiselect' => 'true',
            'multiboxonly' => 'false',
            'pgbuttons' => 'false',
            'pginput' => 'false',
            'afterInsertRow' => NULL,
            'onSelectRow' => '
                function(id){
                      var $this = jQuery(this);                      
                      if(id){
                        $this.saveRow($this.data("lastsel2"));
                        $this.collapseSubGridRow(id);
                        $this.editRow(id, true, null, null, null, function(){
                            $this.data("lastsel2", null);
                            $this.trigger("reloadGrid");
                        });
                        $this.data("lastsel2", id);
                      }
                }
            ',
            'gridComplete' => '
                function(){
                    var $this = jQuery(this);
                    var ids = $this.getDataIDs();
                    var rowData;
                    var props;
                    var value;
                    var inited;
                    for (idx in ids) {
                        rowData = $this.getRowData(ids[idx]);
                        inited = jQuery(gridSelector+" #"+ids[idx]).data("inited");
                        if (inited != true) {
                            for (colname in rowData) {
                                props = $this.getColProp(colname);
                                if (props.edittype=="select") {
                                    value = props.editoptions.value[rowData[colname]];
                                    $this.setCell(ids[idx], colname, value);
                                }
                            }
                        }
                        jQuery(gridSelector+" #"+ids[idx]).data("inited", true);
                    }
                }
            ',
            'subGridRowNum' => 100,
        );
        return $_defaultParams;
    }

    private function _prepareButtonsJs($id, $pagerId, array $params)
    {
        $js = '                
                    gridNav
                    .navButtonAdd(pagerSelector, {
                        caption: "",
                        title: "'.$this->view->translate('Добавить').'",
                        buttonicon: "ui-icon-document",
                        onClickButton: function(){
                            var grd = jQuery(gridSelector);
                        	var id="new_row";
                            if (grd.data("lastsel2")!=null) {
                            	alert("'.$this->view->translate('Сначала сохраните текущую строку').'");
                            	return false;
                            }                            
                            var addResult = grd.addRowData(id, {}, "last");
                            grd.data("lastsel2", id);
                            grd.editRow(
                                id, true, null, null, null,
                                null,
                                function(row_id, data){
                                    grd.data("lastsel2", null);
                                    grd.trigger("reloadGrid");
                                }
                            );
                        }
                    })
                    .navButtonAdd(pagerSelector, {
                        caption: "",
                        title: "'.$this->view->translate('Сохранить').'",
                        buttonicon: "ui-icon-disk",
                        onClickButton: function(){
                            var grd = jQuery(gridSelector);
                            grd.saveRow(grd.data("lastsel2"), null, null, null,
                                function(id, data){
                                	grd.data("lastsel2", null);
                                    grd.trigger("reloadGrid");
                                }
                            );
                        }
                    })
                    .navButtonAdd(pagerSelector, {
                        caption: "",
                        title: "'.$this->view->translate('Удалить').'",
                        buttonicon: "ui-icon-trash",
                        onClickButton: function(){
                            if ( ! confirm("'.$this->view->translate('Удалить отмеченные строки?').'")) {
                                return false;
                            }
                            jQuery(gridSelector).data("lastsel2", null);
                            var sels = jQuery(gridSelector).getGridParam("selarrrow");
                            var postData = "";
                            var row, i = 0;
                            for (row in sels) {
                                postData += "&rows["+i+"]="+sels[row];
                                i++;
                            }
                            jQuery.post("'.$params['deleteurl'].'?"+postData, function(data){
                                jQuery(gridSelector).trigger("reloadGrid");
                            });
                        }
                    })
                    .navSeparatorAdd(pagerSelector)
                    .navButtonAdd(pagerSelector, {
                        caption: "",
                        title: "'.$this->view->translate('Настроить').'",
                        buttonicon: "ui-icon-wrench",
                        onClickButton: function(){
                            jQuery(gridSelector).setColumns({width: 250, colnameview: false});
                            return false;
                        }
                    })
                    ;';
        return $js;
    }

    private function _prepareSubGridJs($id, array $params)
    {
        if (isset($params['subGrid']) AND ($params['subGrid'] == TRUE)) {
            $subGrid = 'subGrid: true, subGridUrl: "'.$params['subGridUrl'].'",
                        subGridRowExpanded: function(subgrid_id, row_id) {
                        		if (row_id == "new_row") {
                        			alert("'.$this->view->translate('Сначала сохраните строку').'");
                        			return false;
                        		}
                        		jQuery("#'.$id.'").saveRow(row_id);
	                            var subgrid_table_id = subgrid_id+"_t";
	                            var subgrid_pager_id = subgrid_id+"_p";
	                            jQuery("#"+subgrid_id).html("<table id=\""+subgrid_table_id+"\" class=\"scroll\"></table><div id=\""+subgrid_pager_id+"\" class=\"scroll\"></div>");
	                            jQuery("#"+subgrid_table_id).jqGrid({
	                                url:"'.$params['subGridUrl'].'?row_id="+row_id,
	                                editurl:"'.$params['subGridEditUrl'].'",
	                                datatype: "json",
	                                colNames: '.$params['subGridColNames'].',
	                                colModel: '.$params['subGridColModel'].',
	                                height: "100%",
	                                multiselect: true,
	                                rowNum: '.$params['subGridRowNum'].',
	                                sortname: "value",
	                                sortorder: "asc",
	                                pager: jQuery("#"+subgrid_pager_id),
	                                pgbuttons: false,
	                                pginput: false,
	                                gridComplete:
	                                    function(){
	                                        var $this = jQuery("#"+subgrid_table_id);
	                                        var ids = $this.getDataIDs();
	                                        var rowData;
	                                        var props;
	                                        var value;
	                                        for (idx in ids) {
	                                            rowData = $this.getRowData(ids[idx]);
	                                            for (colname in rowData) {
	                                                inited = jQuery("#"+subgrid_table_id+" #"+ids[idx]).data("inited");
	                                                if (inited != true) {
	                                                    for (colname in rowData) {
	                                                        props = $this.getColProp(colname);
	                                                        if (props.edittype=="select") {
	                                                            value = props.editoptions.value[rowData[colname]];
	                                                            $this.setCell(ids[idx], colname, value);
	                                                        }
	                                                    }
	                                                }
	                                                jQuery(gridSelector+" #"+ids[idx]).data("inited", true);
	                                            }
	                                        }
	                                    },
	                                ondblClickRow:
	                                   '.(@$params['subGridOnSelectRow']?$params['subGridOnSelectRow']:'
	                                    function(id){
	                                          var $this = jQuery(this);
	                                          if(id){
	                                            $this.saveRow($this.data("lastselSub"), null, null,
	                                                {row_id: row_id},
	                                                function(var_id, data){
	                                                    var text = data.responseText;
	                                                    var rowData;
	                                                    var comm = "rowData = " + text;
	                                                    eval(comm);
	                                                    jQuery("#'.$id.'").setRowData(row_id, rowData);
	                                                }
	                                            );
	                                            $this.editRow(
	                                                id, true, null, null, null,
	                                                {row_id: row_id},
	                                                function(var_id, data){
	                                                    var text = data.responseText;
	                                                    var rowData;
	                                                    var comm = "rowData = " + text;
	                                                    eval(comm);
	                                                    jQuery("#'.$id.'").setRowData(row_id, rowData);
	                                                }
	                                            );
	                                            $this.data("lastselSub", id);
	                                          }
	                                    }
	                                    ').'
	
	                            });
	                            jQuery("#"+subgrid_table_id)
	                                .navGrid("#"+subgrid_pager_id, {edit:false,add:false,del:false,search:false})
	                                .navButtonAdd("#"+subgrid_pager_id, {
	                                    caption: "",
	                                    title: "'.$this->view->translate('Добавить строку').'",
	                                    buttonicon: "ui-icon-document",
	                                    onClickButton: function(){
	                                        var grd = jQuery("#"+subgrid_table_id);
	                                        if (grd.data("lastselSub")=="new_subrow") {
	                                        	alert("'.$this->view->translate('Сначала сохраните текущую строку').'");
	                                        	return false;
	                                        }
	                                        var mainGrid = jQuery("#'.$id.'");
                                            var id = "new_subrow";
                                            var addResult = jQuery("#"+subgrid_table_id).addRowData(id, {}, "last");
                                            grd.data("lastselSub", id);
                                            jQuery("#"+subgrid_table_id).editRow(
                                                id, true, null, null, null,
                                                {row_id: row_id},
                                                function(var_id, data){
                                                    grd.trigger("reloadGrid");                                                  
                                                    jQuery.getJSON(mainGrid.getGridParam("url"), {id: row_id}, function(rowData){
                                                        mainGrid.setRowData(row_id, rowData);
                                                    });
                                                }
                                            );
	                                    }
	                                })
	                                .navButtonAdd("#"+subgrid_pager_id, {
	                                    caption: "",
	                                    id: jQuery("#"+subgrid_pager_id+"_button_save"),
	                                    title: "'.$this->view->translate('Сохранить строку').'",
	                                    buttonicon: "ui-icon-disk",
	                                    onClickButton: function(){
	                                        var grd = jQuery("#"+subgrid_table_id);
	                                        grd.saveRow(grd.data("lastselSub"), null, null,
	                                            {row_id: row_id},
	                                            function(var_id, data){
	                                                var text = data.responseText;
	                                                var rowData;
	                                                var comm = "rowData = " + text;
	                                                eval(comm);
	                                                jQuery("#'.$id.'").setRowData(row_id, rowData);
	                                                grd.data("lastselSub", null);
	                                                grd.trigger("reloadGrid");
	                                            }
	                                        );
	                                    }
	                                })
	                                .navButtonAdd("#"+subgrid_pager_id, {
	                                    caption: "",
	                                    title: "'.$this->view->translate('Удалить выбранные строки').'",
	                                    buttonicon: "ui-icon-trash",
	                                    onClickButton: function(){
	                                    	jQuery("#"+subgrid_table_id).data("lastselSub", null);
	                                        var sels = jQuery("#"+subgrid_table_id).getGridParam("selarrrow");
	                                        var postData = "row_id="+row_id;
	                                        var subrow, i = 0;
	                                        for (subrow in sels) {
	                                            postData += "&subrows["+i+"]="+sels[subrow];
	                                            i++;
	                                        }
	                                        jQuery.post("'.$params['subGridDeleteUrl'].'?"+postData, function(data){
	                                            jQuery("#"+subgrid_table_id).trigger("reloadGrid");
	                                            var mainGrid = jQuery("#'.$id.'");
	                                            jQuery.getJSON(mainGrid.getGridParam("url"), {id: row_id}, function(rowData){
	                                                mainGrid.setRowData(row_id, rowData);
	                                            });
	                                        });
	                                    }
	                                })
	                                .navSeparatorAdd("#"+subgrid_pager_id)
	                                .navButtonAdd("#"+subgrid_pager_id, {
	                                    caption: "",
	                                    title: "'.$this->view->translate('Свернуть').'",
	                                    buttonicon: "ui-icon-closethick",
	                                    onClickButton: function(){
	                                    	jQuery("#"+subgrid_table_id).data("lastselSub", null);
	                                        jQuery("#'.$id.'").collapseSubGridRow(row_id);
	                                    }
	                                })
	                                '.(array_key_exists('addonSubGridButtonsJs', $params)?$params['addonSubGridButtonsJs']:'').'
	                                ;
	                                '.(array_key_exists('addonSubGridJs', $params)?$params['addonSubGridJs']:'').'

                       }
                       ';
        }
        else {
            $subGrid = '';
        }

        return $subGrid;
    }

    private function _prepareParams(array $params)
    {
        if (array_key_exists('colModel', $params) AND $this->_isCountable($params['colModel'])) {
            foreach ($params['colModel'] as $idx=>$row) {
                foreach ($row as $field=>$val) {
                    if (is_string($val)) {
                        if (strtolower($val) == 'true') {
                            $params['colModel'][$idx][$field] = TRUE;
                        }
                        else if (strtolower($val) == 'false') {
                            $params['colModel'][$idx][$field] = FALSE;
                        }
                    }
                }
            }
            $params['colModel'] = Zend_Json::encode($params['colModel']);
        }
        if (array_key_exists('colNames', $params) AND $this->_isCountable($params['colNames'])) {
            $params['colNames'] = Zend_Json::encode($params['colNames']);
        }
        if (array_key_exists('subGridColModel', $params) AND $this->_isCountable($params['subGridColModel'])) {
            foreach ($params['subGridColModel'] as $idx=>$row) {
                foreach ($row as $field=>$val) {
                    if (is_string($val)) {
                        if (strtolower($val) == 'true') {
                            $params['subGridColModel'][$idx][$field] = TRUE;
                        }
                        else if (strtolower($val) == 'false') {
                            $params['subGridColModel'][$idx][$field] = FALSE;
                        }                    
                    }
                }
            }
            $params['subGridColModel'] = Zend_Json::encode($params['subGridColModel']);
        }
        if (array_key_exists('subGridColNames', $params) AND $this->_isCountable($params['subGridColNames'])) {
            $params['subGridColNames'] = Zend_Json::encode($params['subGridColNames']);
        }
        if (array_key_exists('rowList', $params) AND $this->_isCountable($params['rowList'])) {
            $params['rowList'] = Zend_Json::encode($params['rowList']);
        }
        return $params;
    }

    private function _isCountable($var)
    {
        return (is_array($var) OR (is_object($var) AND ($var instanceof Countable)));
    }

}

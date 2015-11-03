<?php

class App_View_Helper_FormFlexiGrid extends Zend_View_Helper_FormElement {

    const DEFAULT_DATATYPE = 'json';
    const DEFAULT_WIDTH = 452;
    const DEFAULT_HEIGHT = 100;
    const DEFAULT_SORTORDER = 'asc';
    const DEFAULT_USEPAGER = FALSE;

    protected static $_inited = FALSE;

	public function formFlexiGrid($name = NULL, $value = null, $attribs = null, $options = NULL) {

        /**
         * enable helper's api
         */
        if ($name === NULL) {
            return $this;
        }

        if (self::$_inited !== TRUE) {
            $this->view->headScript('FILE', $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/flexigrid/flexigrid.pack.js');
            $this->view->headLink(array('rel' => 'stylesheet','type' => 'text/css',
                                        'href' => $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/flexigrid/css/flexigrid/flexigrid.css'));
            /*$this->view->headLink(array('rel' => 'stylesheet','type' => 'text/css',
                                        'href' => $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/flexigrid/style.css'));*/
            $this->view->headScript('FILE', $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/jquery-ui.js');
            $this->view->headLink(array('rel' => 'stylesheet','type' => 'text/css',
                                        'href' => $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/theme/ui.dialog.css'));
            $this->view->headLink(array('rel' => 'stylesheet','type' => 'text/css',
                                        'href' => $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/theme/jquery-ui-lightness.css'));
            $this->view->headStyle('
                .flexigrid div.fbutton .add
                    {
                        background: url('.$this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/flexigrid/css/images/add.png) no-repeat center left;
                    }

                .flexigrid div.fbutton .edit
                    {
                        background: url('.$this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/flexigrid/css/images/edit.png) no-repeat center left;
                    }

                .flexigrid div.fbutton .delete
                    {
                        background: url('.$this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/flexigrid/css/images/close.png) no-repeat center left;
                    }
                .flexigrid div.bDiv td div
                    {
                        padding-left: 0px;
                        padding-right: 0px;
                    }
            ');
            self::$_inited = TRUE;
        }

        if (isset($attribs['id'])) {
            $id = $attribs['id'];
        }
        else {
            $id = $name;
        }
        if (isset($attribs['dataType'])) {
            $dataType = $attribs['dataType'];
        }
        else {
            $dataType = self::DEFAULT_DATATYPE;
        }
        if (isset($attribs['width'])) {
            $width = $attribs['width'];
        }
        else {
            $width = self::DEFAULT_WIDTH;
        }
        if (isset($attribs['height'])) {
            $height = $attribs['height'];
        }
        else {
            $height = self::DEFAULT_HEIGHT;
        }
        if (isset($attribs['sortorder'])) {
            $sortorder = $attribs['sortorder'];
        }
        else {
            $sortorder = self::DEFAULT_SORTORDER;
        }
        if (isset($attribs['sortname'])) {
            $sortname = $attribs['sortname'];
        }
        else {
            $sortname = $attribs['colModel'][0]['name'];
        }
        if (array_key_exists('usepager', $attribs)) {
            $usepager = (bool) $attribs['usepager'];
        }
        else {
            $usepager = self::DEFAULT_USEPAGER;
        }
        if ($usepager) {
            $usepager = 'true';
        }
        else {
            $usepager = 'false';
        }

        $uid = md5(microtime().''.$id);

        if (isset($attribs['buttons'])) {
            $buttons = Zend_Json::encode($attribs['buttons']);
            $buttons = preg_replace('/\"onpress\"\s*\:\s*(\"([a-zA-Z0-9_,\(\)\s]+?)\")/', 'onpress: $2', $buttons);
            if (isset($attribs['buttonsJs'])) {
                $buttonsJs = $attribs['buttonsJs'];
            }
            else {
                $buttonsJs = '';
            }
        }
        else {
            $addDialog  = 'addDialog_' .$id.'_'.$uid;
            $editDialog = 'editDialog_'.$id.'_'.$uid;
            $buttons = '[
                        {name: "'.$this->view->translate('Edit').'", bclass: "edit", onpress: flexiGridEdit_'.$id.'_'.$uid.'},
                        {name: "'.$this->view->translate('New').'", bclass: "add", onpress: flexiGridAdd_'.$id.'_'.$uid.'},
                        {name: "'.$this->view->translate('Delete').'", bclass: "delete", onpress: flexiGridDelete_'.$id.'_'.$uid.'},
                        {separator: true}
                        ]';
            $dialogNewWidth = '';
            $dialogNewHeight = '';
            $dialogNewTitle = '';
            if (isset($attribs['dialogNew'])) {
                if (isset($attribs['dialogNew']['width'])) {
                    $dialogNewWidth = 'width: '.$attribs['dialogNew']['width'].',';
                }
                if (isset($attribs['dialogNew']['height'])) {
                    $dialogNewHeight = 'height: '.$attribs['dialogNew']['height'].',';
                }
                if (isset($attribs['dialogNew']['title'])) {
                    $dialogNewTitle = 'title: "'.$attribs['dialogNew']['title'].'",';
                }
            }
            $dialogEditWidth = '';
            $dialogEditHeight = '';
            $dialogEditTitle = '';
            if (isset($attribs['dialogEdit'])) {
                if (isset($attribs['dialogEdit']['width'])) {
                    $dialogEditWidth = 'width: '.$attribs['dialogEdit']['width'].',';
                }
                if (isset($attribs['dialogEdit']['height'])) {
                    $dialogEditHeight = 'height: '.$attribs['dialogEdit']['height'].',';
                }
                if (isset($attribs['dialogEdit']['title'])) {
                    $dialogEditTitle = 'title: "'.$attribs['dialogEdit']['title'].'",';
                }
            }
            $buttonsJs = '
                $(function(){
                    $("body").append('.Zend_Json::encode('<div id="'.$addDialog.'">'.$this->view->renderForm($attribs['editForm']).'</div>').');
                    $("body").append('.Zend_Json::encode('<div id="'.$editDialog.'">'.$this->view->renderForm($attribs['editForm']).'</div>').');


                    $("#'.$addDialog.'").dialog({
                        autoOpen: false,
                        closeOnEscape: true,
                        '.$dialogNewWidth.$dialogNewHeight.$dialogNewTitle.'
                        buttons: {
                            "'.$this->view->translate('Ok').'":
                                    function(){
                                        var $this = $(this);
                                        var grid = $this.data("grid");
                                        var data = $(":input", this).serializeArray();
                                        var postData = {};
                                        for (el in data) {
                                            postData[data[el].name] = data[el].value;
                                        }
                                        $.post("'.$attribs['urlAdd'].'", postData, function(res){
                                            $this.dialog("close");
                                            $("div.bDiv>table", grid).flexReload();
                                        });
                                    },
                            "'.$this->view->translate('Cancel').'":
                                    function(){
                                        $(this).dialog("close");
                                    }
                        }
                    });

                    $("#'.$editDialog.'").dialog({
                        autoOpen: false,
                        closeOnEscape: true,
                        '.$dialogEditWidth.$dialogEditHeight.$dialogEditTitle.'
                        buttons: {
                            "'.$this->view->translate('Ok').'":
                                    function(){
                                        var $this = $(this);
                                        var grid = $this.data("grid");
                                        var data = $(":input", this).serializeArray();
                                        var postData = {};
                                        for (el in data) {
                                            postData[data[el].name] = data[el].value;
                                        }
                                        postData.rowId = $this.data("rowId");
                                        $.post("'.$attribs['urlEdit'].'", postData, function(res){
                                            $this.dialog("close");
                                            $("div.bDiv>table", grid).flexReload();
                                        });
                                    },
                            "'.$this->view->translate('Cancel').'":
                                    function(){
                                        $(this).dialog("close");
                                    }
                        }
                    });

                });

                function flexiGridAdd_'.$id.'_'.$uid.'(command, grid)
                {
                    /*$("#'.$addDialog.' :input").val("");*/
                    $("#'.$addDialog.' form")[0].reset();
                    $("#'.$addDialog.'").data("grid", grid);
                    $("#'.$addDialog.'").dialog("open");
                }

                function flexiGridEdit_'.$id.'_'.$uid.'(command, grid)
                {

                    if ($(".trSelected", grid).length<1) {
                        alert("'.$this->view->translate('Не выбрана строка для редактирования').'");
                        return;
                    }
                    var rowId = $(".trSelected", grid).eq(0).attr("id");
                    $("#'.$editDialog.'").data("rowId", rowId);
                    $("#'.$editDialog.'").data("grid", grid);
                    $.getJSON("'.$attribs['url'].'", {"rowId": rowId}, function(rowData){
                        var el;
                        $("#'.$editDialog.' form")[0].reset();
                        for (el in rowData) {
                            /*$("#'.$editDialog.' #"+el).val(rowData[el]);*/
                            $("#'.$editDialog.' [name="+el+"]").not(":radio")
                                .val(rowData[el]);
                        }
                        for (el in rowData) {
                            /*$("#'.$editDialog.' #"+el).val(rowData[el]);*/
                            $("#'.$editDialog.' [name="+el+"][value="+rowData[el]+"]:radio")
                                .attr("checked", true);
                        }
                        $("#'.$editDialog.' div.bDiv>table").flexReload();
                        $("#'.$editDialog.'").dialog("open");
                    });

                }

                function flexiGridDelete_'.$id.'_'.$uid.'(command, grid)
                {
                    if ($(".trSelected", grid).length<1) {
                        alert("'.$this->view->translate('Не выбраны строки для удаления').'");
                        return;
                    }
                    var postData = "";
                    $(".trSelected", grid).each(function(i){
                        postData += "&rows["+i+"]="+$(this).attr("id");
                    });
                    $.post("'.$attribs['urlDelete'].'", postData, function(res){
                        $("div.bDiv>table", grid).flexReload();
                    });
                }

            ';
        }

        if (isset($attribs['searchItems'])) {
            $searchItemsJs = 'searchitems : '.Zend_Json::encode($attribs['searchItems']).',';
        }
        else {
            $searchItemsJs = '';
        }

        $this->view->headScript('SCRIPT', 'var colModel_'.$id.' = '.Zend_Json::encode($attribs['colModel']).';');

        if (isset($attribs['rp'])) {
            $rp = $attribs['rp'];
        }
        else {
            $rp = (int) Zend_Controller_Action_HelperBroker::getStaticHelper('RowsPerPage')->getValue();
        }

        if (isset($attribs['useRp'])) {
            $useRp = $attribs['useRp'];
        }
        else {
            $useRp = 'false';
        }
        
        if (isset($attribs['showTableToggleBtn'])) {
            $showTableToggleBtn = $attribs['showTableToggleBtn'];
        }
        else {
            $showTableToggleBtn = 'false';
        }
        
        if (isset($attribs['autoload'])) {
            $autoload = $attribs['autoload'];
        }
        else {
            $autoload = 'false';
        }
        
        $js = '
            $(function(){
                $("#'.$id.'_'.$uid.'").flexigrid({
                    url: "'.$attribs['url'].'",
                    autoload: '.$autoload.',
                    dataType: "'.$dataType.'",
                    colModel : colModel_'.$id.',
                    '.$searchItemsJs.'
                    sortname: "'.$sortname.'",
                    sortorder: "'.$sortorder.'",
                    usepager: '.$usepager.',
                    useRp: '.$useRp.',
                    rp: '. $rp.',
                    pagestat: "'.$this->view->translate('с {from} пo {to} из {total} строк').'",
                    pagetext: "'.$this->view->translate('Стр.').'",
                    outof: "'.$this->view->translate('из').'",
                    findtext: "'.$this->view->translate('Поиск').'",
                    procmsg: "'.$this->view->translate('Загрузка ...').'",
                    title: "'.(isset($attribs['title'])?$attribs['title']:'&nbsp;').'",
                    showTableToggleBtn: '.$showTableToggleBtn.',
                    width: '.$width.' ,
                    height: '.$height.',
                    buttons : '.$buttons.',                  
                });
                $("#'.$id.'_'.$uid.'").flexReload();
            });
        ';

        if ( ! empty($buttonsJs)) {
            $this->view->headScript('SCRIPT', $buttonsJs);
        }

        $xhtml = '<table id="'.$id.'_'.$uid.'"></table>';
        $this->view->inlineScript('SCRIPT', $js);

		return $xhtml;
	}

}


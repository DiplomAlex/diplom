<?php

class View_Helper_Jquery_Combobox extends Zend_View_Helper_Abstract
{

    /**
     * @param string - jquery selector for input field
     * @param mixed array|object - data will be json-encoded
     * @return string
     */
    public function jquery_Combobox($inputName, $data)
    {
        $this->view->headLink(array('type' => 'text/css','rel' => 'stylesheet','href' => $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/combobox/ui.combobox.css'));

        $this->view->headStyle('
            img.input {
                width: auto;
                border: 0;
            }
        ');
        
        $this->view->headScript('FILE', $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/combobox/ui.combobox.js');
        $this->view->headScript('SCRIPT', '

            $(function(){
                $("#'.$inputName.'").css("width", "432px");
                $("#'.$inputName.'").combobox(
                    {
                        "arrowURL":"'.$this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/combobox/drop_down.png",
                        "arrowHTML":function() {
                                        return $("<img class = \"ui-combobox-arrow\" border = \"0\" src = \"" + this.options.arrowURL + "\" />");
                                    },
                        "data": '.Zend_Json::encode($data).',
                        "autoShow": false,
                        "listHTML": function (data, i) {
                            var cls = i % 2 ? "odd" : "even";
                            return "<span class = \"ui-combobox-item " + cls + "\">" + data + "</span>";                        
                        }
                    }
                );
            });

        ');
    }

}
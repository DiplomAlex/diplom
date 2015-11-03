<?php

class View_Helper_Jquery_Datepicker extends Zend_View_Helper_Abstract
{

    const EMPTY_TEXT = 'выберите дату';

    public function jquery_Datepicker($fieldId, $addToHeadScript = TRUE, $renderInput = FALSE, $inputAttribs = array())
    {

        $this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/jquery-ui.js');
        $this->view->headScript(Zend_View_Helper_HeadScript::FILE, $this->view->stdUrl(array('reset'=>TRUE)).'js/jquery/ui/i18n/ui.datepicker-'.Model_Service::factory('language')->getCurrent()->code2.'.js');

        $this->view->headLink(array('type' => 'text/css','rel' => 'stylesheet','href' => $this->view->stdUrl(array('reset'=>TRUE)) . 'js/jquery/theme/ui.core.css'));
        $this->view->headLink(array('type' => 'text/css','rel' => 'stylesheet','href' => $this->view->stdUrl(array('reset'=>TRUE)) . 'js/jquery/theme/ui.theme.css'));

        $this->view->headLink(array('type' => 'text/css','rel' => 'stylesheet','href' => $this->view->stdUrl(array('reset'=>TRUE)) . 'js/jquery/theme/ui.datepicker.css'));

        $script = '
            $(document).ready(function(){
                var field = $("#'.$fieldId.'");


               var cssEmpty = {
                    /*"font-style": "italic",
                    "color":"#6b6b76",
                    "text-align":"left"*/
                };

                var cssNotEmpty = {
                    "font-style": field.css("font-style"),
                    "color": field.css("color"),
                    "text-align": field.css("text-align")
                };


                field.datepicker({
                    showStatus: true,
                    showOn: "both",
                    buttonImage: "'.$this->view->skin()->url().'images/calendar.png",
                    buttonImageOnly: true,
                    format: "yyyy-mm-dd",
                    changeMonth: true,
                    changeYear: true,
                    yearRange : "c-65:c+10",
                    beforeShow: function() {$(".ui-datepicker").css("z-index", "10000"); },
                });


                if (field.val() == "") {
                    field.val("'.$this->view->translate(self::EMPTY_TEXT).'");
                    field.css(cssEmpty);
                }



                field.blur(function(){
                    if (field.val() == "") {
                        field.val("'.$this->view->translate(self::EMPTY_TEXT).'");
                        field.css(cssEmpty);
                    }
                });

                field.focus(function(){
                    if (field.val() == "'.$this->view->translate(self::EMPTY_TEXT).'") {
                        field.val("");
                        field.css(cssNotEmpty);
                    }
                });


            });
        ';

        $xhtml = '';
        if ($renderInput === TRUE) {
            $attribs = '';
            foreach ($inputAttribs as $attr=>$value) {
                $attribs .= $attr.' = "'.$value.'" ';
            }
            $xhtml = '<input type="text" name="'.$fieldId.'" id="'.$fieldId.'" '.$attribs.'/>';
        }
        if ($addToHeadScript === TRUE) {
            $this->view->headScript('SCRIPT', $script);
        }
        else {
            $xhtml .= ' <script type="text/javascript">'.$script.'</script>';
        }
        return $xhtml;
    }

}
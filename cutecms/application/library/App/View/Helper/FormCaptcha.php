<?php

class App_View_Helper_FormCaptcha extends Zend_View_Helper_FormElement {

	public function formCaptcha($name, $value = null, $attribs = null) {

        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, id, value, attribs, options, listsep, disable

        // is it disabled?
        $disabled = '';
        if ($disable) {
            $disabled = ' disabled="disabled"';
        }

        // XHTML or HTML end tag?
        $endTag = ' />';
        if (($this->view instanceof Zend_View_Abstract) && !$this->view->doctype()->isXhtml()) {
            $endTag= '>';
        }

        @$captcha = App_Captcha::factory($attribs['captchaDriver'], $attribs['captchaOptions']);
        $captcha->generate();

        $unique = md5(rand(0, 1000000));

        $uniqId = $id . '_' . $unique;

        $reloadText = $this->view->translate('Reload');
        $xhtml =  '<span id="'.$uniqId.'_captcha_view">'.$captcha->render($this->view).'</span>'
                . '<input type="text" '
                . (isset($attribs['size'])?'size="'.$attribs['size'].'"':'').' '
                . (isset($attribs['maxlen'])?'maxlen="'.$attribs['maxlen'].'"':'').' '
                . (isset($attribs['class'])?'class="'.$attribs['class'].'"':'').' '
                . (isset($attribs['style'])?'style="'.$attribs['style'].'"':'').' '
                . $disabled . ' '
                . 'name="'.$name.'[input]" id="' . $uniqId . '" value="">'
                . '<input type="hidden" id="'.$uniqId.'_captcha_id" name="'.$name.'[id]"
                        value="'.$captcha->getId().'">'
        	. '&nbsp;&nbsp;&nbsp;'
                . '<a class="reload_captcha" href="#" ajax-href="'.$this->view->stdUrl(NULL, 'captcha-reload', 'auth', 'kernel').'" id="'.$uniqId.'_captcha_reload">'.$reloadText.'</a>'
                . '
                    <script type="text/javascript">
                        $(document).ready(function(){
                            $("#'.$uniqId.'_captcha_reload").click(function(e){
                                e.preventDefault();
                                $.get($(this).attr("ajax-href"), function(data){
                                    eval("data = "+data);
                                    $("#'.$uniqId.'_captcha_view").html(data.view);
                                    $("#'.$uniqId.'_captcha_id").val(data.id);
                                });
                            });
                        });
                    </script>
                  '
                ;

        return $xhtml;
	}

}
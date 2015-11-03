<?php

class App_View_Helper_FormSubmitLink extends Zend_View_Helper_FormElement
{

    /**
     * Generates a 'submit' button in form of link with onsubmit="this.form.submit()" javascript action.
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     *
     * @param mixed $value The element value.
     *
     * @param array $attribs Attributes for the element tag.
     *
     * @return string The element XHTML.
     */
    public function formSubmitLink($name, $value = null, $attribs = null)
    {

        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable

        // check if disabled
        $disabled = '';
        if ($disable) {
            $disabled = ' disabled="disabled"';
        }

        // XHTML or HTML end tag?
        $endTag = ' />';
        if (($this->view instanceof Zend_View_Abstract) && !$this->view->doctype()->isXhtml()) {
            $endTag= '>';
        }

        // Render the button.
        if ( ! isset($class)) {
            if (isset($attribs['class'])) {
                $class = $attribs['class'];
            }
            else {
                $class = 'btn2';
            }
        }

        $uid = md5(microtime());

        $xhtml  = '<input smLink="1" type="hidden" name="'.$name.'" id="'.$id.'" value="" uid="'.$uid.'">';

        if (( ! isset($attribs['noAjaxSubmit']))
            OR
            ( ! $attribs['noAjaxSubmit'])
            OR
            ( ! Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest())) {
                $onClick = 'onclick="return submitFormBySubmitLink(\''.$uid.'\')"';

/*
            $xhtml .= ' <script type="text/javascript">
                            $(document).ready(function(){
                                var form = $("#'.$id.'").get(0).form;
                                $("#'.$id.'_link", form).click(function(e){
                                    e.preventDefault();';
            if ( ! $disable) {
                if (isset($attribs['onclick'])) {
                    $xhtml .= $attribs['onclick'];
                }
                else {
                    $xhtml .= ''
                                  .'$("#'.$id.'", form).removeAttr("smLink").val("1");'
                                  .'$("[smLink=1]", form).remove();'
                                  .'form.submit();';
                }
            }
            $xhtml .= '         });
                            });
                        </script>';
*/

        }

        if (isset($attribs['no_ajax_wj_replace'])) {
            $noAj = 'no_ajax_wj_replace="1"';
        }
        else {
            $noAj = '';
        }

        $class .= ' btn';
        
        $xhtml .= '<a smLink="1" uid="'.$uid.'" href="#" class="'.$class.' submitLink" id="'.$id.'_link" '.$noAj.' >'.$value.'</a>';


        $this->view->headScript('SCRIPT', '
            $(function(){
                $("a[uid='.$uid.']").click(function(e){
                    e.preventDefault();
                    '.(isset($attribs['onclick'])?$attribs['onclick']:'').'
                    submitFormBySubmitLink("'.$uid.'");
                });
            });
        ');

        return $xhtml;
    }

}
<?php


class App_View_Helper_FormTextareaCounter extends Zend_View_Helper_FormTextarea
{
    public function formTextareaCounter($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable

        // is it disabled?
        $disabled = '';
        if ($disable) {
            // disabled.
            $disabled = ' disabled="disabled"';
        }

        // Make sure that there are 'rows' and 'cols' values
        // as required by the spec.  noted by Orjan Persson.
        if (empty($attribs['rows'])) {
            $attribs['rows'] = (int) $this->rows;
        }
        if (empty($attribs['cols'])) {
            $attribs['cols'] = (int) $this->cols;
        }
        
        $xhtml = 
        		App::$reg->lang->_('Total symbols left').' : '
        		. '<span id="'.$id.'_textareacounter">'.$attribs['maxlength'].'</span><br/>';

        // build the textarea element
        $xhtml .= '<textarea name="' . $this->view->escape($name) . '"'
                . ' id="' . $this->view->escape($id) . '"'
                . $disabled
                . $this->_htmlAttribs($attribs) . '>'
                . $this->view->escape($value) . '</textarea>';
		
		$this->view->headScript(
					'SCRIPT',
					'
						$(document).ready(function(){
							$("#'.$id.'").keyup(function(e){
								var val = String($(this).val()).length;
								var num = '.$attribs['maxlength'].' - val;
								if (num <= 0 ) {
									$(this).val($(this).val().substr(0, '.$attribs['maxlength'].'));
									val = String($(this).val()).length;
									num = '.$attribs['maxlength'].' - val
								}
								$("#'.$id.'_textareacounter").html(num);
    						});
    						$("#'.$id.'").keyup();
    					});
					' 
				  );

        return $xhtml;
    }
}

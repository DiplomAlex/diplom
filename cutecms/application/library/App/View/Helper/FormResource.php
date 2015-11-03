<?php

class App_View_Helper_FormResource extends Zend_View_Helper_FormElement
{
    /**
     * Generates a 'file' element.
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     *
     * @param mixed $value The element value.  array('preview'=>, 'filename'=>,)
     *
     * @param array $attribs Attributes for the element tag.
     *
     * @return string The element XHTML.
     */
    public function formResource($name, $value = null, $attribs = null)
    {
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

        // build the element
        $xhtml = '';
        if (!empty($value)) {
        	$xhtml .= '<div class="upload_file"><a href="' . App_Resource::getUploadsUrl($value)  . '">';
        	$previewname = App_Resource::getPreviewName($value);

        	if (file_exists(App_Resource::getUploadsPath($previewname))) {
                $xhtml .= '<img style="border: 0;" src="' . App_Resource::getUploadsUrl($previewname)  . '">';
            }
            else {
                if (App_Resource::isImage($value)) {
                    $xhtml .= '<img style="border: 0;" width="100" src="' . App_Resource::getUploadsUrl($value) . '">';
                }
            	else {
                    $xhtml .= '<b>Download</b>';
                }
            }
        	$xhtml .= '</a><div class="upload_delete"><input name="'.$name.'_del" type="checkbox" value="1" '. $disabled.' />'.$this->view->translate('Delete').'</div></div>';
        }
        $xhtml .= '<input type="file"'
                . ' name="' . $this->view->escape($name) . '"'
                . ' id="' . $this->view->escape($id) . '"'
                . ' value=""'
                . $disabled
                . $this->_htmlAttribs($attribs)
                . $endTag;

        if (Zend_Registry::get('config')->resources->allowGrabFromUrl) {
            $xhtml .= '<div class="upload_from">'.$this->view->translate('Grab from url').': <input type="text"'
                    . ' name="' . $this->view->escape($name) . '_grab"'
                    . ' id="' . $this->view->escape($id) . '_grab"'
                    . ' value=""'
                    . $disabled
                    . $this->_htmlAttribs($attribs)
                    . $endTag
                    . '</div>';
        }

        return $xhtml;
    }
}

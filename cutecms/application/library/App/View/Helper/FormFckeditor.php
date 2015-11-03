<?php

class App_View_Helper_FormFckeditor extends Zend_View_Helper_FormElement
{


    protected static $_addedToHeadScript = FALSE;

    const DEFAULT_SKIN = 'kama';

    protected static $_toolbar = array(
                array('Source', '-', 'Copy', 'Cut', 'Paste', 'Undo', 'Redo', '-', 'Find', 'Replace', '-', 'SelectAll', 'RemoveFormat', 'Image', 'Flash', 'Table', 'Rool', 'SpecialChar'),
                array('Link', 'Unlink', 'Anchor'),
                array('/'),
                array('Bold', 'Italic', 'Underline', 'StrikeThrough', '-', 'Subscript', 'Superscript', 'TextColor', 'BGColor'),
                array('OrderedList', 'UnorderedList', '-', 'Outdent', 'Indent', 'Blockquote', 'CreateDiv'),
                array('JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyFull'),
    );

    public function formFckeditor($name, $value = null, $attribs = null)
    {
        if (isset($attribs['newJsStyle'])) {
            return $this->_newJsStyle($name, $value, $attribs);
        }
        else if (isset($attribs['oldStyle'])) {
            return $this->_oldStyle($name, $value, $attribs);
        }
        require_once  APPLICATION_PUBLIC.'/js/ckeditor/ckeditor.php';


        /**
         * ATTENTION !!!
         * TODO : remove necessity of editing /ckfinder/config.php when installing
         */
        require_once  APPLICATION_PUBLIC.'/js/ckfinder/ckfinder.php';

        $ckeditorPath = $this->view->stdUrl(array('reset'=>TRUE)).'js/ckeditor/';
        $editor = new CKEditor($ckeditorPath);
        if ( ! self::$_addedToHeadScript) {
            if (!empty($editor->timestamp) && $editor->timestamp != "%"."TIMESTAMP%") {
                $args = '?t=' . $editor->timestamp;
            }
            $this->view->headScript('SCRIPT', 'window.CKEDITOR_BASEPATH="'. $ckeditorPath .'";');
            $this->view->headScript('FILE', $ckeditorPath.'ckeditor.js'.$args);
            if ($editor->timestamp != CKEditor::timestamp) {
                $this->view->headScript('SCRIPT', 'CKEDITOR.timestamp = "'. $editor->timestamp .'";');
            }
        }
        $base = rtrim(APPLICATION_BASE, '/');
        CKFinder::SetupCKEditor( $editor, $base.'/js/ckfinder/' ) ;
        $editor->initialized = TRUE;
        $xhtml = $editor->editor($name, $value, array(
            'returnOutput' => TRUE,
            'textareaAttributes' => $attribs,
            'skin' => (isset($attribs['skin'])?$attribs['skin']:self::DEFAULT_SKIN),
            'toolbar' => (isset($attribs['toolbar'])?$attribs['toolbar']:self::$_toolbar),
            'width' => (isset($attribs['width'])?$attribs['width']:''),
            'height' => (isset($attribs['height'])?$attribs['height']:''),
        ));
        return $xhtml;
    }


    /**
     * TODO : integrate CKFinder
     */
    protected function _newJsStyle($name, $value = null, $attribs = null)
    {

        if ( ! self::$_addedToHeadScript) {
            $this->view->headScript('FILE', $this->view->stdUrl(array('reset'=>TRUE)).'js/ckeditor/ckeditor.js');
        }

        $info = $this->_getInfo($name, $value, $attribs);
        extract($info);

        $disabled = '';
        if ($disable) {
            $disabled = ' disabled="disabled"';
        }

        if ($width) {
            $width = 'width: '.$width.',';
        }
        else {
            $width = '';
        }
        if ($height) {
            $height = 'height: '.$height.',';
        }
        else {
            $height = '';
        }

        $xhtml = '<textarea
                   id="'.$name.'" name="'.$name.'" '.$disabled.'
                   '.$this->_htmlAttribs($attribs).'>'.htmlspecialchars($value).'</textarea>'
        .'<script type="text/javascript">'."\n".'//<![CDATA['."\n".'
                CKEDITOR.replace( "'.$name.'",
                    {
                        '. $height . $width .'
                        skin : "'.(isset($attribs['skin'])?$attribs['skin']:self::DEFAULT_SKIN).'"
                    });
        '."\n".'//]]>'."\n".'</script>';

        return $xhtml;
    }


    protected function _oldStyle($name, $value = null, $attribs = null) {

        require (APPLICATION_PUBLIC . '/js/fckeditor/fckeditor.php');

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

        $oFCKeditor = new FCKeditor($id) ;
        $oFCKeditor->BasePath = $this->view->stdUrl(array('reset'=>TRUE)) . 'js/fckeditor/' ;
        $oFCKeditor->Value = $value ;
        $oFCKeditor->Height = (int)($attribs['height_koef'] * $oFCKeditor->Height);
        if (isset($attribs['width'])) $oFCKeditor->Width = $attribs['width'];
        if (isset($attribs['toolbar'])) {
            $oFCKeditor->ToolbarSet = $attribs['toolbar'];
        }
        ob_start();
        $oFCKeditor->Create() ;
        $xhtml = ob_get_clean();

        return $xhtml;
    }



}
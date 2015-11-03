<?php

class Catalog_Model_Service_Helper_Importer_Abstract extends Model_Service_Helper_Abstract
{
    
    /**
     * read xml file and add section after root if needed
     * @param string $filename
     * @param string|false $addSectionAfterRoot
     * @return Zend_Config_Xml
     */
    protected function _getXml($filename, $addSectionAfterRoot = FALSE, $wrapCData = NULL)
    {
        $xmlStr = file_get_contents($filename);
        if ($wrapCData !== NULL) {
            if ( ! is_array($wrapCData)) {
                $wrapCData = array($wrapCData);
            }
            foreach ($wrapCData as $tag) {
                $xmlStr = preg_replace('/\<'.$tag.'(\s.*)?\>(.*)?<\/'.$tag.'>/', '<'.$tag.' $1><![CDATA[$2]]></'.$tag.'>', $xmlStr);
            }
        }
        
        if ($addSectionAfterRoot) {
            $xmlStr = preg_replace('/\<ObjectXML(\s*NAME=\".*\")?\s*\>/', '<ObjectXML $1><'.$addSectionAfterRoot.'>', $xmlStr); 
            $xmlStr = str_replace('</ObjectXML>', '</'.$addSectionAfterRoot.'></ObjectXML>', $xmlStr);
            $xml = new Zend_Config_Xml($xmlStr, $addSectionAfterRoot);
        }
        else {
            $xml = new Zend_Config_Xml($xmlStr);
        }
        return $xml;
    }
    
}
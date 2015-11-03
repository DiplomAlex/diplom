<?php

class Catalog_Model_Service_Helper_Importer_Category extends Catalog_Model_Service_Helper_Importer_Abstract
{

    protected $_filename = NULL;
    protected $_backupFilename = NULL;
    
    /**
     * 1) читаем xml из файла
     * 2) маппим xml в Catalog_Model_Collection_Category
     * 3) обновляем у всей коллекции id по guid
     * 4) сохраняем коллекцию через db-маппер 
     */
    public function process()
    {
        $xml = $this->_getXml($this->getFilename(), 'categories');
        $xmlMapper = $this->getService()->getInjector()->getObject('Model_Mapper_Importer');
        $dbMapper = $this->getService()->getMapper();
        $coll = $xmlMapper->makeComplexCollection($xml->category);
        $guids = array();
        foreach ($coll as $obj) {
            $guids []= $obj->guid;
        }
        $ids = $dbMapper->fetchIdsByGuids($guids); // from db-mapper        
        foreach ($coll as $obj) {
            if (array_key_exists($obj->guid, $ids)) {
                $obj->id = $ids[$obj->guid];
            }
        }
        $dbMapper->saveImportedCollection($coll);
        $this->_sanitize();
    }
    
    /**
     * @return string
     */
    public function getFilename()
    {
        if ($this->_filename === NULL) {
            $this->_filename = APPLICATION_PATH.'/'.Zend_Registry::get('catalog_config')->importCategory->file;
        }
        return $this->_filename;
    }
    
    /**
     * set name of xml file for import
     * @param string $fname
     */
    public function setFilename($fname)
    {
        if (empty($fname)) {
            $this->_throwException('illegal or empty $fname (file name for import): "'.$fname.'"');
        }
        $this->_filename = $fname;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getBackupFilename()
    {
        if ($this->_backupFilename === NULL) {
            $info = pathinfo($this->getFilename());
            $this->_backupFilename = $info['dirname'] . '/backup/' . $info['filename']. '_' . date('Y-m-d H_i_s').'.'.$info['extension'];
        }
        return $this->_backupFilename;
    }    
    
    /**
     * backup imported file and remove original
     */
    protected function _sanitize()
    {
        $filename = $this->getFilename();
        copy($filename, $this->getBackupFilename());
        unlink($filename);
    }
    
}
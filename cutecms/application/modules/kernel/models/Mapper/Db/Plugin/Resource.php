<?php


/**
 * Using example: 
 *      in mapper's init() add
 *              $this->addPlugin('Resource',
 *                               $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Resource', 
 *                                                               array('rc_id'), // all resource fields of object should be listed here 
 *                                                               Zend_Registry::get('config')->images->previewMaxCount));
 *      and if you need special previews add also - 
 *        if ($config = Zend_Registry::get('config')->images->previewDimensions->{$this->getInjector()->getInjection('Model_Object_Interface')}) {
 *            $this->getPlugin('Resource')->setPreviewDimensions($config->toArray());
 *        }      
 *        
 *     in config.ini previewDimensions array is described as:
 *          previewDimensions.Model_Object_MyObject.previewPlace.zIndex =
 *          previewDimensions.Model_Object_MyObject.previewPlace.width  = 
 *          previewDimensions.Model_Object_MyObject.previewPlace.height =
 *                      
 */

class Model_Mapper_Db_Plugin_Resource extends Model_Mapper_Db_Plugin_Abstract
{


    /**
     * @var Model_Db_Table_Abstract
     */
    protected $_table = NULL;

    protected $_hasTable = TRUE;


    /**
     * resource_id fields of object
     * @var array
     */
    protected $_fields = NULL;


    protected $_previewCount = NULL;
    
    protected $_previewDimensions = NULL;
    
    protected $_mimeTypes = array(
        'bmp'  => 'image/bmp',
        'gif'  => 'image/gif',
        'jpeg' => 'image/jpeg',
        'jpg'  => 'image/jpeg',
        'jpe'  => 'image/jpeg',
        'png'  => 'image/png',
        'tif'  => 'image/tif',
    );


    /**
     * @param array of object fields
     */
    public function __construct(array $fields, $previewCount = NULL, array $previewDimensions = NULL)
    {
        $this->_fields = $fields;
        $this->_previewCount = $previewCount;
        if ($previewDimensions !== NULL) {
            $this->_previewDimensions = $this->setPreviewDimensions($previewDimensions);
        }
    }

    /**
     * setter for _mapper
     * @param Model_Mapper_Db_Interface
     * @return $this
     */
    public function setMapper(Model_Mapper_Db_Interface $mapper)
    {
        $this->_mapper = $mapper;
        $maxPrvCount = Zend_Registry::get('config')->images->previewMaxCount;
        if ( (int) $this->_previewCount > $maxPrvCount) {
            $this->_throwException('2nd parameter passed to constructor for plugin Resource (previewsCount) should be less or equal "'.$maxPrvCount.'" in init() of mapper '.get_class($this->getMapper()));
        }
        return $this;
    }


    /**
     * @return array
     */
    public function getFields()
    {
        return $this->_fields;
    }
    
    /**
     * lazy init table
     * @return Zend_Db_Table_Abstract
     */
    public function getTable()
    {
        if ($this->_table === NULL) {
            $this->_table = $this->getMapper()->getInjector()->getObject('Model_Db_Table_Resources');
        }
        return $this->_table;
    }


    /**
     * join description row for current language (for _onFetchComplexAddons)
     * @param Zend_Db_Select
     * @return Zend_Db_Select
     */
    public function onFetchComplex(Zend_Db_Select $select)
    {
        $mapperTable = $this->getMapper()->getTable();
        foreach ($this->_fields as $field) {
            $joinSelectFields = array(
                    'resource_'.$field.'_'.'id' => 'resource_'.$field.'.rc_id',
                    'resource_'.$field.'_'.'filename' => 'resource_'.$field.'.rc_filename',
                    'resource_'.$field.'_'.'source_filename' => 'resource_'.$field.'.rc_source_filename',
                    'resource_'.$field.'_'.'size' => 'resource_'.$field.'.rc_size',
                    'resource_'.$field.'_'.'mime' => 'resource_'.$field.'.rc_mime',
                    'resource_'.$field.'_'.'width' => 'resource_'.$field.'.rc_width',
                    'resource_'.$field.'_'.'height' => 'resource_'.$field.'.rc_height',
                    'resource_'.$field.'_'.'preview' => 'resource_'.$field.'.rc_preview',
                    'resource_'.$field.'_'.'prv_width' => 'resource_'.$field.'.rc_prv_width',
                    'resource_'.$field.'_'.'prv_height' => 'resource_'.$field.'.rc_prv_height',
            );
            for ($i = 2; $i<= (int) $this->_previewCount; $i++) {
                $suffix = $this->_getPreviewSuffix($i);
                $joinSelectFields['resource_'.$field.'_'.'preview'.$suffix] = 'resource_'.$field.'.rc_preview'.$suffix;
                $joinSelectFields['resource_'.$field.'_'.'prv'.$suffix.'_width'] = 'resource_'.$field.'.rc_prv'.$suffix.'_width';
                $joinSelectFields['resource_'.$field.'_'.'prv'.$suffix.'_height'] = 'resource_'.$field.'.rc_prv'.$suffix.'_height';
            }
            $select -> joinLeft(
                array('resource_'.$field => $this->getTable()->getTableName()),
                $mapperTable->getTableName().'.'.$mapperTable->getColumnPrefix().'_'.$field.'= resource_'.$field.'.rc_id',
                $joinSelectFields
            );
        }

        return $select;
    }

    /**
     * compose key for flat array when fetching or saving descriptions
     * @param string
     * @return string
     */
    protected function _getFlatArrayKey($field)
    {
        return 'resource_'.$field;
    }


    /**
     * get all resources of one object
     * @param Model_Object_Interface
     * @return array()
     */
    public function fetchResources(Model_Object_Interface $obj, $returnFlatArray = TRUE)
    {

        $table = $this->getTable();

        $ids = array();
        $keys = array();
        foreach ($this->_fields as $field) {
            $ids[$field]=$obj->{$field};
            $keys[$obj->{$field}] = $field;
        }

        $select = $table->select()
                       ->from(
                            $table->getTableName(),
                            '*'
                       )
                       ->where($table->getColumnPrefix().'_id IN (?)', $ids)
                       ;
        $rcs = $select->query()->fetchAll();

        if ($returnFlatArray === TRUE) {
            $result = array();
            foreach ($rcs as $rc) {
                $field = $keys[$rc['rc_id']];
                $key = $this->_getFlatArrayKey($field);
                $result[$key] = $rc['rc_filename'];
                $result[$key.'_id'] = $rc['rc_id'];
                $result[$key.'_filename'] = $rc['rc_filename'];
                $result[$key.'_height'] = $rc['rc_height'];
                $result[$key.'_width'] = $rc['rc_width'];
                $result[$key.'_size'] = $rc['rc_size'];
                $result[$key.'_mime'] = $rc['rc_mime'];
                $result[$key.'_source_filename'] = $rc['rc_source_filename'];
                $result[$key.'_preview'] = $rc['rc_preview'];
                $result[$key.'_prv_height'] = $rc['rc_prv_height'];
                $result[$key.'_prv_width'] = $rc['rc_prv_width'];
                for ($i = 2; $i<= (int) $this->_previewCount; $i++) {
                    $suffix = $this->_getPreviewSuffix($i);
                    $result[$key.'_preview'.$suffix] = $rc['rc_preview'.$suffix];
                    $result[$key.'_prv'.$suffix.'_height'] = $rc['rc_prv'.$suffix.'_height'];
                    $result[$key.'_prv'.$suffix.'_width'] = $rc['rc_prv'.$suffix.'_width'];
                }
            }
        }
        else {
            foreach ($rcs as $rc) {
                $key = $this->_getFlatArrayKey($field);
                $result[$key] = $rc;
            }
        }
        return $result;
    }


    /**
     * map fields to object
     * @param Model_Object_Interface object itself
     * @param array values to map
     * @return Model_Object_Interface
     */
    public function onBuildComplex(Model_Object_Interface $object, array $values)
    {
        foreach ($this->_fields as $field) {
            $this->buildOneFieldFromValues($field, $object, $values);
        }
        return $object;
    }
    
    
    public function buildOneFieldFromValues($field, Model_Object_Interface $object, array $values, $prefix = NULL)
    {
        if ($prefix === NULL) {
            $prefix = $this->_getFlatArrayKey($field);
        }
        if (isset($values[$prefix.'_id']) AND $object->hasElement($field)) {
            $object->{$field} = $values[$prefix.'_id'];
        }
        if (isset($values[$prefix.'_filename']) AND $object->hasElement($field.'_filename')) {
            $object->{$field.'_filename'} = $values[$prefix.'_filename'];
        }
        if (isset($values[$prefix.'_size']) AND $object->hasElement($field.'_size')) {
            $object->{$field.'_size'} = $values[$prefix.'_size'];
        }
        if (isset($values[$prefix.'_width']) AND $object->hasElement($field.'_width')) {
            $object->{$field.'_width'} = $values[$prefix.'_width'];
        }
        if (isset($values[$prefix.'_height']) AND $object->hasElement($field.'_height')) {
            $object->{$field.'_height'} = $values[$prefix.'_height'];
        }
        if (isset($values[$prefix.'_source_filename']) AND $object->hasElement($field.'_source_filename')) {
            $object->{$field.'_source_filename'} = $values[$prefix.'_source_filename'];
        }
        if (isset($values[$prefix.'_mime']) AND $object->hasElement($field.'_mime')) {
            $object->{$field.'_mime'} = $values[$prefix.'_mime'];
        }

        if (isset($values[$prefix.'_preview']) AND $object->hasElement($field.'_preview')) {
            $object->{$field.'_preview'} = $values[$prefix.'_preview'];
        }
        for ($i = 2; $i<= (int) $this->_previewCount; $i++) {
            $suffix = $this->_getPreviewSuffix($i);
            if (isset($values[$prefix.'_preview'.$suffix]) AND $object->hasElement($field.'_preview'.$suffix)) {
                $object->{$field.'_preview'.$suffix} = $values[$prefix.'_preview'.$suffix];
            }
        }
        return $object;
    }


    /**
     * @param Model_Object_Interface
     */
    public function onBeforeDelete(Model_Object_Interface $object)
    {
        $ids = array();
        foreach ($this->_fields as $field) {
            $ids[] = $object->{$field};
        }
        $selectFields = array('rc_filename', 'rc_preview');
        for ($i = 2; $i<= (int) $this->_previewCount; $i++) {
            $selectFields []= 'rc_preview'.$this->_getPreviewSuffix($i);
        }
        $rows = $this->getTable()->select()
                         ->from($this->getTable()->getTableName(), $selectFields)
                         ->where('rc_id IN (?)', $ids)
                         ->query()->fetchAll();
        foreach ($rows as $rc) {
            @unlink($this->getUploadsPath($rc['rc_filename']));
            @unlink($this->getUploadsPath($rc['rc_preview']));
            for ($i = 2; $i<= (int) $this->_previewCount; $i++) {
                @unlink($this->getUploadsPath($rc['rc_preview'.$this->_getPreviewSuffix($i)]));
            }
        }
        $this->getTable()->delete(array('rc_id IN (?)'=>$ids));
        return $object;
    }


    /**
     * @param Model_Object_Interface
     * @param array
     * @param bool
     */
    public function onAfterSaveComplex(Model_Object_Interface $obj, array $values, $isNew = FALSE)
    {
        $this->saveUploadedResource($obj, $values, $isNew);
    }
    
    public function saveUploadedResource(Model_Object_Interface $obj, array $values, $isNew = FALSE)
    {
        if (is_array($this->_fields)) {
            $prefix = $this->getTable()->getColumnPrefix() . $this->getTable()->getPrefixSeparator();
            $toSave = FALSE;
            foreach ($this->_fields as $field) {
               if (isset($_FILES['resource_'.$field]) OR isset($values['resource_'.$field.'_del']) 
                   OR (isset($values['resource_'.$field.'_grab']) AND ( ! empty($values['resource_'.$field.'_grab'])))) {
                   /**
                    * prepare image
                    */
                    
                    /**
                     * thnx, Kest, for grabbing
                     */
                    $ok = TRUE;
                    if (isset($values['resource_'.$field.'_grab']) AND ( ! empty($values['resource_'.$field.'_grab']))) {                        
                        $content = file_get_contents($fn = $values['resource_'.$field.'_grab']);
                        if ($content) {
                            $fna = explode('.', $fn);
                            $fn = $fna[count($fna)-1];
                            $fn = md5(uniqid()).'.'.$fn;
                            file_put_contents($fn = $this->getUploadsPath($fn), $content);

                            $fileArr = array(
                                'name' => basename($values['resource_'.$field.'_grab']),
                                'filename' => basename($fn),
                                'size' => filesize($fn),
                                'type' => $this->_getMimeType($fn),
                                'source_url' => $values['resource_'.$field.'_grab'],
                                'grabbed' => TRUE,
                            );

                            $file = $fileArr;
                        }
                        else {
                            $ok = FALSE;
                        }
                    }
                    else {
                        $fileArr = $_FILES['resource_'.$field];
                    }                   
                    
                    $fileArr['previewDimensions'] = $this->_getPreviewDimensions();
                    if ($ok AND $row = $this->_create(
                                            $fileArr,
                                            NULL,
                                            $values['resource_'.$field.'_del'],
                                            $obj[$field]
                                        )) {
                        $obj = $this->buildOneFieldFromValues($field, $obj, $row->toArray(), 'rc');
                        $toSave = TRUE;
                    }
               }

            }
            if ($toSave) {
                try {
                    $this->getMapper()->save($obj, TRUE);
                }
                catch (Model_Mapper_Exception $e) {
                }
            }
        }        
    }

    /**
     * create resource
     * @param array $_FILES['resource']
     * @param array $_FILES['resource']
     * @param bool set TRUE to delete file current resource
     * @param int current resource id
     * @return int id value of new resource
     */
    protected function _create( array $file = NULL, array $previews = NULL,
                                    $fileDel = FALSE, $oldRcId = NULL )
    {

        if (($oldRcId !== NULL) AND (( (bool) $fileDel===TRUE) OR ( ! empty($file['name'])))) {
            $this->getTable()->find($oldRcId)->current()->delete();
        }

        return $this->_prepare($file, $previews, $oldRcId);
    }

    /**
     * retrieve resource (move/rename uploaded file and preview, manipulate images, insert into db)
     * @param array $_FILES['resource']
     * @param array array($_FILES['resource'], ...)
     * @return App_Model_Row_Resource
     */
    protected function _prepare(array $file = NULL, array $previews = NULL, $oldRcId = NULL)
    {
        if ( ! empty($file['name'])) {
            $row = $this->getTable()->createRow();
            $file = App_Resource::moveUploaded($file);
            if ( ! empty($previews)) {
                foreach ($previews as $key=>$preview) {
                    $previews[$key] = App_Resource::moveUploaded($preview);
                }
            }
            else {
                for ($i = 1; $i<=$this->_previewCount; $i++) {
                    $previews[] = App_Resource::preparePreview($file, $i);
                }
            }
            $this->prepareRow($row, $file, $previews);
            $row->save();
        }
        else {
            $row = /*$this->getTable()->find($oldRcId)->current()*/ FALSE;
        }
        return $row;
    }


    /**
     * set correct row values
     */
    public function prepareRow(Zend_Db_Table_Row_Abstract $row, array $file = NULL, array $previews = NULL)
    {
        $mapArray = array(
            $this->getTable()->getColumnPrefix().'_filename' => $file['filename'],
            $this->getTable()->getColumnPrefix().'_source_filename' => $file['name'],
            $this->getTable()->getColumnPrefix().'_source_url' => $file['source_url'],
            $this->getTable()->getColumnPrefix().'_mime' => $file['type'],
            $this->getTable()->getColumnPrefix().'_width' => $file['width'],
            $this->getTable()->getColumnPrefix().'_height' => $file['height'],
            $this->getTable()->getColumnPrefix().'_size' => $file['size'],
        );
        foreach ($previews as $key=>$preview) {
            $suffix = $this->_getPreviewSuffix($key+1);
            $mapArray[$this->getTable()->getColumnPrefix().'_preview'.$suffix] = $preview['filename'];
            $mapArray[$this->getTable()->getColumnPrefix().'_prv'.$suffix.'_width'] = $preview['width'];
            $mapArray[$this->getTable()->getColumnPrefix().'_prv'.$suffix.'_height'] = $preview['height'];
        }
        $row->setFromArray($mapArray);
    }


    public function getUploadsPath($fileName, $withFile = TRUE)
    {
        return App_Resource::getUploadsPath($fileName, $withFile = TRUE);
    }

    protected function _getPreviewSuffix($num = NULL)
    {
        return App_Resource::getPreviewSuffix($num);
    }
    
    protected function _getPreviewDimensions()
    {
        if ($this->_previewDimensions === NULL) {
            $config = Zend_Registry::get('config')->images;
            $this->_previewDimensions = array(array('width' => $config->previewWidth, 'height' => $config->previewHeight));
            for ($i = 1; $i<=$this->_previewCount-1; $i++) {
                $this->_previewDimensions[$i] = array('width' => $config->{'previewWidth'.($i+1)}, 'height' => $config->{'previewHeight'.($i+1)});
            }
        }
        return $this->_previewDimensions;
    }
    
    public function setPreviewDimensions(array $dimensions = NULL)
    {        
        $this->_previewDimensions = $this->orderDimensions($dimensions);
        $this->_previewCount = count($this->_previewDimensions);
        return $this;
    }
    
    /**
     * example: array(
     *                  array('width'=>, 'height'=>), 
     *                  array('zIndex'=>1, 'width'=>, 'height'=>), 
     *                  array('zIndex'=>3, 'width'=>, 'height'=>)
     *               ) 
     *          will be translated into 
     *          array(
     *                  0=>array('zIndex'=>1, 'width'=>, 'height'=>), 
     *                  1=>NULL, 
     *                  2=>array('zIndex'=>3, 'width'=>, 'height'=>), 
     *                  3=>array('width'=>, 'height'=>)
     *               )
     * 
     * 
     */    
    public function orderDimensions(array $dimensions)
    {
        $result = array();
        $maxZ = 0;
        $byZ = array();
        $woZ = array();
        foreach ($dimensions as $arr) {
            if (array_key_exists('zIndex', $arr) AND ( (int) $arr['zIndex'] > 0)) {
                $byZ[ (int) $arr['zIndex']] = $arr;
                if ($arr['zIndex'] > $maxZ) {
                    $maxZ = $arr['zIndex'];
                }
            }
            else {
                $woZ []= $arr;
            }
        }
        for ($i = 0; $i<$maxZ; $i++) {
            if (array_key_exists($i+1, $byZ)) {
                $result[$i] = $byZ[$i+1]; 
            }
            else {
                $result[$i] = NULL;
            }
        }
        if ( ! empty($woZ)) {
            $result = array_merge($result, $woZ);
        }
        return $result;
    }
    
    
    protected function _getMimeType($file)
    {
        if (class_exists('finfo')) {
            $finfo = new finfo(FILEINFO_MIME);
            $mime = $finfo->buffer(file_get_contents($file));
        }
        else if (function_exists('mime_content_type')) {
            $mime = mime_content_type($file);
        }
        else {
            $info = pathinfo($file);
            $key = strtolower($info['extension']);
            if (array_key_exists($key, $this->_mimeTypes)) {
                $mime = $this->_mimeTypes[$key];
            }
            else {
                $mime = NULL;
            }
        }
        return $mime;        
    }
    
    /**
     * prepare previews for object resource fields if needed 
     * @param Model_Object_Interface $obj
     * @return $this
     */
    public function reprocessPreviews(Model_Object_Interface $obj)
    {
        $table = $this->getTable();
        $prefix = $table->getColumnPrefix().$table->getPrefixSeparator();
        $dimensions = $this->_getPreviewDimensions();
        foreach ($this->_fields as $field) {
            if ( (int) $obj->{$field}) {
                $needToReprocess = array();
                foreach ($dimensions as $num => $dimension) {
                    $elNamePreview = $field.'_preview'.(($num>0)?($num+1):'');                    
                    /* may be checking width and height is better here ?? */
                    if ($obj->hasElement($elNamePreview) AND empty($obj->{$elNamePreview}) AND ( ! empty($dimension))) { 
                        $needToReprocess []= $num+1;
                    }
                }
                if (count($needToReprocess)) {
                    $row = $table->find($obj->{$field})->current();
                    $file = array(
                        'filename' => $row->{$prefix.'filename'},
                        'type' => $row->{$prefix.'mime'},
                        'width' => $row->{$prefix.'width'},
                        'height' => $row->{$prefix.'height'},
                        'previewDimensions' => $dimensions,
                    );
                    for ($i = 1; $i<=$this->_previewCount; $i++) {
                        if (in_array($i, $needToReprocess)) {
                            $preview = App_Resource::preparePreview($file, $i);
                            $strI = $i >1 ? $i : '';
                            $row->{$prefix.'preview'.$strI} = $preview['filename'];
                            $row->{$prefix.'prv'.$strI.'_width'} = $preview['width'];
                            $row->{$prefix.'prv'.$strI.'_height'} = $preview['height'];
                            $elNamePreview = $field.'_preview'.$strI;
                            $elNameWidth = $field.'_prv'.$strI.'_width';
                            $elNameHeight = $field.'_prv'.$strI.'_height';
                            $obj->{$elNamePreview} = $preview['filename'];
                            if ($obj->hasElement($elNameWidth)) {
                                $obj->{$elNameWidth} = $preview['width'];
                            }
                            if ($obj->hasElement($elNameHeight)) {
                                $obj->{$elNameHeight} = $preview['height'];
                            }
                        }
                    }
                    $row->save();
                }
            }            
        }
        return $this;
    }
    
                
}
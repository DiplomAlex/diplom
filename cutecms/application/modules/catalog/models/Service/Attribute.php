<?php

class Catalog_Model_Service_Attribute extends Model_Service_Abstract
{


    protected $_defaultInjections = array(
        'Model_Object_Interface'   => 'Catalog_Model_Object_Attribute',
        'Model_Collection_Interface'  => 'Catalog_Model_Collection_Attribute',
        'Model_Mapper_Interface'   => 'Catalog_Model_Mapper_Db_Attribute',
        'Model_Service_Language',
        'Model_Mapper_XML_Variant' => 'Catalog_Model_Mapper_XML_AttributeVariant',
        'Model_Mapper_Db_Variant'  => 'Catalog_Model_Mapper_Db_AttributeVariant',
        'Model_Collection_Variant' => 'Catalog_Model_Collection_AttributeVariant',
    );


    protected $_validTypes = array(
        'int', 'decimal', 'datetime', 'string', 'text', 'variant',
    );

    protected $_validTypesTranslated = NULL;



    /**
     * initializes object
     * @see Model_Service_Abstract::init()
     */
    public function init()
    {
        $lang = $this->getInjector()->getObject('Model_Service_Language');
        $this->getMapper()->getPlugin('Description')->setLanguages($lang->getAllActive())->setCurrentLanguage($lang->getCurrent());
    }


    /**
     * creates new object
     * @return Model_Object_Interface
     */
    public function create()
    {
        $attr = $this->getInjector()->getObject('Model_Object_Interface');
        $attr->status = 1;
        $attr->type = 'variant';
        return $attr;
    }
    

    /**
     * get objects fields values for edit form
     * @return array
     */
    public function getEditFormValues($id)
    {
        $obj = $this->getComplex($id);
        $values = $obj->toArray();
        $descs = $this->getMapper()->getPlugin('Description')->fetchDescriptions($id);
        $values['attribute_groups'] = $this->getBindedGroupsIds($obj);
        $values = array_merge($values, $descs);
        return $values;
    }

    public function getBindedGroupsIds(Model_Object_Interface $obj)
    {
        $groups = Model_Service::factory('catalog/attribute-group')->getAllByAttribute($obj);
        $ids = array();
        foreach ($groups as $group) {
            $ids[$group->id] = $group->id;
        }
        return $ids;
    }



    /**
     * get all rows of group as page of Zend_Paginator object
     * @param int
     * @param int
     * @param int
     * @return Zend_Paginator
     */
    public function paginatorGetAllByGroup($group, $rowsPerPage = NULL, $page = NULL)
    {
        if ($rowsPerPage === NULL) {
            $rowsPerPage = Zend_Registry::get('config')->default->paginator->rowsPerPage;
        }
        if ($page === NULL) {
            $page = Zend_Controller_Front::getInstance()->getRequest()->getParam('page');
        }
        $paginator = $this->getMapper()->paginatorFetchComplexByGroup($group, $rowsPerPage, $page);
        return $paginator;
    }


    /**
     * @param bool
     * @return array
     */
    public function getAllTypes($translated = FALSE)
    {
        $result = array_combine($this->_validTypes, $this->_validTypes);
        if ($translated === TRUE) {
            if ($this->_validTypesTranslated !== NULL) {
                $result = $this->_validTypesTranslated;
            }
            else {
                $arr = $result;
                $result = array();
                foreach ($arr as $key=>$val) {
                    $result[$val] = $this->getTranslator()->_($val);
                }
            }
        }
        return $result;
    }

    /**
     * @param bool
     * @return array
     */
    public function getAllStatuses($translated = FALSE)
    {
        $result = array();
        $statuses = $this->getStatusesList();
        foreach ($statuses as $key=>$id) {
            if ($translated) {
                $result[$id] = $this->getTranslator()->_('attribute_status.'.$key);
            }
            else {
                $result[$id] = $key;
            }
        }
        return $result;
    }
    
    /**
     * @param int $groupId
     * @return Model_Collection_Interface
     */
    public function getAllByGroup($groupId)
    {
        return $this->getMapper()->fetchComplexByGroup($groupId);
    }
   
    

    /**
     * parse variants of attribute from xml to collection
     *
     * @param string xml
     * @return Model_Collection_Interface
     */
    public function parseVariantsFromXML($xml)
    {
        $mapper = $this->getInjector()->getObject('Model_Mapper_XML_Variant');
        $xml = trim($xml);
        if ( ! empty($xml)) {
            try {
                $variants = new SimpleXMLElement($xml);
            }
            catch (Exception $e) {
                echo __FILE__.' '.__LINE__;                
                var_dump($xml);exit;
            }
        }
        else {
            $variants = NULL;
        }
        $result = $mapper->makeSimpleCollection($variants);
        return $result;
    }

    public function parseVariantsToXML(Catalog_Model_Collection_AttributeVariant $coll)
    {
        $mapper = $this->getInjector()->getObject('Model_Mapper_XML_Variant');
        return $mapper->unmapCollectionToXML($coll);
    }


    public function parseVariantsFromText($text)
    {
        $coll = $this->getInjector()->getObject('Model_Collection_Variant');
        $text = trim($text);
        $text = trim($text, '| ');
        $variants = explode('|', $text);
        foreach ($variants as $key=>$var) {
            if (empty($var)) {
                unset($variants[$key]);
            }
            else {
                $values = explode(';', $var);
                if ( ! empty($values[0])) {
                    $coll->add($this->createVariantFromValues(array(
                        'value' => $values[0],
                        'text' => $values[0],
                        'param1' => $values[1],
                        'param2' => $values[2],
                    )));
                }
            }
        }
        return $coll;
    }


    public function parseVariantsToText(Catalog_Model_Collection_AttributeVariant $coll)
    {
        $variants = array();
        foreach ($coll as $var) {
            $variants[]= implode(';', array($var['value'], $var['param1'], $var['param2'],));
        }
        $text = implode('|', $variants);
        return $text;
    }

    public function createAttributeFromValues(array $values)
    {
        if (empty($values['code'])) {
            $values['code'] = uniqid('attribute_');
        }
        return $this->getMapper()->makeCustomObject($values);
    }


    public function createVariantFromValues(array $values)
    {
        $mapper = $this->getInjector()->getObject('Model_Mapper_XML_Variant');
        return $mapper->makeCustomObject($values);
    }

    public function setVariantFromValues(Model_Object_Interface $obj, array $values)
    {
        $mapper = $this->getInjector()->getObject('Model_Mapper_XML_Variant');
        return $mapper->setObjectFromValues($obj, $values);
    }

    public function createVariantCollection()
    {
        return $this->getInjector()->getObject('Model_Collection_Variant');
    }

    /**
     * finds one attribute by its code,otherwise returns false
     * @param string code
     * @return mixed Model_Object_Interface|bool
     */
    public function getOneByCode($code)
    {
        $coll = $this->getMapper()->fetchComplexByCode($code);
        if ( ! $coll->isEmpty()) {
            $result = $coll->current();
        }
        else {
            $result = FALSE;
        }
        return $result;
    }
    
    /**
     * gets real codes by aliases from config
     * @param string $alias
     * @return string 
     */
    public function getCodeByAlias($alias)
    {
        if (($code = Zend_Registry::get('catalog_config')->attributeAlias->{$alias}) AND ( ! empty($code))) {
            $result = $code;
        }
        else {
            $result = $alias;
        }
        return $result;
    }
    
}
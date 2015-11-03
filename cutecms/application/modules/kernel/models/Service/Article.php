<?php

class Model_Service_Article extends Model_Service_Abstract
{
    
    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Model_Object_Article',
        'Model_Collection_Interface' => 'Model_Collection_Article',
        'Model_Mapper_Interface' => 'Model_Mapper_Db_Article',
        'Model_Service_Language',
        'Model_Service_Topic' => 'Model_Service_ArticleTopic',
        'Model_Service_Helper_Gallery' => 'Model_Service_Helper_Content_Gallery',
        'Model_Service_Gallery',  
        'Model_Db_Table_GalleryRef',
        'Model_Service_Helper_Comment' => 'Model_Service_Helper_Content_Comment',
        'Model_Service_Comment',  
        'Model_Db_Table_CommentRef',
        'Model_Service_Helper_Multisite',
    );
    
    /**
     * initializes object
     * @see Model_Service_Abstract::init()
     */
    public function init()
    {
        $lang = $this->getInjector()->getObject('Model_Service_Language');
        $this->getMapper()->getPlugin('Description')->setLanguages($lang->getAllActive())->setCurrentLanguage($lang->getCurrent());
        $this->addHelper('Gallery',
                         $this->getInjector()->getObject('Model_Service_Helper_Gallery', $this, array(
                            'contentType' => lcfirst($this->getModelName()),
                            'refTableClass' => $this->getInjector()->getInjection('Model_Db_Table_GalleryRef'),
                            'linkedService' => $this->getInjector()->getObject('Model_Service_Gallery'),
                            'contentTitleField' => 'title',
                         )));        
        $this->addHelper('Comment',
                         $this->getInjector()->getObject('Model_Service_Helper_Comment', $this, array(
                            'contentType' => lcfirst($this->getModelName()),
                            'refTableClass' => $this->getInjector()->getInjection('Model_Db_Table_CommentRef'),
                            'linkedService' => $this->getInjector()->getObject('Model_Service_Comment'),
                            'contentTitleField' => 'title',
                         )));   
        $this->addHelper('Multisite', $this->getInjector()->getObject('Model_Service_Helper_Multisite', $this));
    }
    
    public function getComplexBySeoId($id) 
    {
        return $this->getMapper()->fetchComplexBySeoIdOrId($id);
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
        $values['topics'] = $this->getBindedTopicsIds($obj);
        $values = $values + $descs;
        return $values;
    }
    
    public function getBindedTopicsIds(Model_Object_Interface $obj)
    {
        $groups = $this->getInjector()->getObject('Model_Service_Topic')->getAllByArticle($obj);
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
    public function paginatorGetAllByTopic($topic, $rowsPerPage = NULL, $page = NULL)
    {
        if ($rowsPerPage === NULL) {
            $rowsPerPage = Zend_Registry::get('config')->default->paginator->rowsPerPage;
        }
        if ($page === NULL) {
            $page = Zend_Controller_Front::getInstance()->getRequest()->getParam('page');
        }
        $paginator = $this->getMapper()->paginatorFetchComplexByTopic($topic, $rowsPerPage, $page);
        return $paginator;
    }
    
    public function getLast($limit)
    {
        return $this->getMapper()->fetchComplexLast($limit);
    }
    
    public function getAllIdsByTopics($topics)
    {
        $items = $this->getMapper()->fetchComplexByTopics($topics, TRUE);
        $ids = array();
        foreach ($topics as $topic) {
            $ids []= $topic->id;
        }
        return $ids;
    }
        

    
} 
<?php

class Model_Service_ArticleTopic extends Model_Service_Abstract
{
    
    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Model_Object_ArticleTopic',
        'Model_Collection_Interface' => 'Model_Collection_ArticleTopic',
        'Model_Mapper_Interface' => 'Model_Mapper_Db_ArticleTopic',
        'Model_Service_Language',
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
        $obj = $this->get($id);
        $values = $obj->toArray();
        $descs = $this->getMapper()->getPlugin('Description')->fetchDescriptions($id);
        $values = $values + $descs;
        return $values;
    }

    

    /**
     * @param mixed Model_Object_Interface|int
     * @return Model_Collection_ArticleTopic
     */
    public function getAllByArticle($article)
    {
        if ($article instanceof Model_Object_Interface) {
            $article = $article->id;
        }
        return $this->getMapper()->fetchAllByArticleId($article);
    }


    public function getAllByParent($parent)
    {
        return $this->getMapper()->fetchComplexByParent($parent);
    }
    
    /**
     * get all one level children of current category in tree
     * @param int current category id (parent)
     * @param int
     * @param int
     * @return Zend_Paginator
     */
    public function paginatorGetAllByParent($parent, $rowsPerPage = NULL, $page = NULL)
    {
        if ($rowsPerPage === NULL) {
            $rowsPerPage = Zend_Registry::get('config')->default->paginator->rowsPerPage;
        }
        if ($page === NULL) {
            $page = Zend_Controller_Front::getInstance()->getRequest()->getParam('page');
        }
        if ($parent === NULL) {
            $parent = Zend_Controller_Front::getInstance()->getRequest()->getParam('parent');
        }
        $query = $this->getMapper()->fetchComplexByParent($parent, NULL, FALSE);
        $paginator = $this->getMapper()->paginator($query, $rowsPerPage, $page, Model_Object_Interface::STYLE_COMPLEX);
        return $paginator;
    }



    /**
     * get collection containing all parents of current object
     * @param int object's id
     * @param bool wether include current object itself to collection
     * @return Model_Collection_Interface
     */
    public function getParentsOf($id, $includeSelf = FALSE)
    {
        if ( ! $id) {
            return $this->getInjector()->getObject('Model_Collection_Interface');
        }
        else {
            return $this->getMapper()->fetchComplexParentsOf($id, $includeSelf);
        }
    }


    /**
     * get collection of categories in order of full ajared tree
     * excluding branch starting from $id
     * @param int
     * @param bool
     * @param bool
     * @return Model_Collection_Interface
     */
    public function getTreeWithoutBranch($id = NULL, $includeCurrentId = FALSE, $addRoot = TRUE)
    {
        $data = $this->getMapper()->getPlugin('Tree')->getTreeWithoutBranch($id, $includeCurrentId, $addRoot);
        return $this->getMapper()->makeComplexCollection($data);
    }



    /**
     * change position in list
     * @param int objectId
     * @param string (first|last|prev|next)
     */
    public function changeSorting($objId, $position)
    {
        $object = $this->get($objId);
        $this->getMapper()->getPlugin('Tree')->changeSorting($object, $position);
        return $this;
    }

    /**
     * get object by tree id
     * @param int $treeId
     * @return Model_Object_Interface
     */
    public function getByTreeId($treeId)
    {
        try {
            $object = $this->getMapper()->fetchOneByTreeId($treeId);
        }
        catch (Model_Mapper_Exception $e) {
            throw new Model_Service_Exception('article topic by tree_id = "'.$treeId.'" not found');
        }
        return $object;
    }
    
    /**
     * get all tree (optionally excluding branch) as array of options for form select
     * @param int $excludeBranchRootId
     * @param bool use id instead of tree_id
     * @return array
     */
    public function getFullTreeAsSelectOptions($excludeBranchRootId = NULL, $useId = FALSE)
    {
        $tree = $this->getTreeWithoutBranch($excludeBranchRootId);
        $list = array(0 => ' << '. $this->getTranslator()->_('Root') . ' >> ');
        if ($tree->count()) {
            foreach ($tree as $row) {
                $name = str_repeat(' - ', $row->tree_level) . $row->name;
                if ($useId === TRUE) {
                    $key = $row->id;
                }
                else {
                    $key = $row->tree_id;
                }
                $list[$key] = $name;
            }
        }
        return $list;
    }
    

    public function getTopicsList()
    {
        $result = $this->getMapper()->fetchTopicsList();
        return $result;
    }
    
    
    public function getChildrenIdsByRootIdArray(array $rootIds)
    {
        return $this->getMapper()->fetchChildrenIdsByRootIdArray($rootIds);
    }
    
    
} 
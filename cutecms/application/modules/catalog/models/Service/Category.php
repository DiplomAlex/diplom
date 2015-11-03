<?php

class Catalog_Model_Service_Category extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Catalog_Model_Object_Category',
        'Model_Collection_Interface' => 'Catalog_Model_Collection_Category',
        'Model_Mapper_Interface' => 'Catalog_Model_Mapper_Db_Category',
        'Model_Mapper_Importer' => 'Catalog_Model_Mapper_Config_ImporterCategory',
        'Model_Service_Language',
        'Model_Service_Helper_Importer' => 'Catalog_Model_Service_Helper_Importer_Category',
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
        $this->addHelper('Importer',$this->getInjector()->getObject('Model_Service_Helper_Importer', $this));
        $this->addHelper('Multisite', $this->getInjector()->getObject('Model_Service_Helper_Multisite', $this));        
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
        $tree = $this->getMapper()->getPlugin('Tree')->getNodeValues($obj->tree_id);
        $rcs = $this->getMapper()->getPlugin('Resource')->fetchResources($obj);
        $values = $values + $descs + $tree + $rcs;
        return $values;
    }



    /**
     * @param mixed (string|int) seo_id or id of category
     * @return Model_Object_Category
     */
    public function get($id)
    {
        if (is_numeric($id)) {
            $object = $this->getMapper()->fetchComplexOneById($id);
        }
        else if (is_string($id)) {
            $object = $this->getMapper()->fetchOneBySeoId($id);
        }
        else {
            throw new Model_Service_Exception('unknown parameter for get method - '.$id);
        }
        return $object;
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
    
    public function getFullTree($addRoot = TRUE)
    {
        return $this->getTreeWithoutBranch($addRoot);
    }


    /**
     * @return Model_Collection_Interface
     */
    public function getFullTreeSortedByLevel($activeOnly = FALSE)
    {
        return $this->getMapper()->fetchFullTreeSortedByLevel(TRUE, $activeOnly);
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

    public function getAllByParent($parent)
    {
        $result = $this->getMapper()->fetchComplexByParent($parent, NULL);
        return $result;
    }
    
    public function getAllActiveByParent($parent)
    {
        $result = $this->getMapper()->fetchComplexActiveByParent($parent);
        return $result;
    }
        
    public function paginatorGetFullTree($rowsPerPage = NULL, $page = NULL)
    {
        if ($rowsPerPage === NULL) {
            $rowsPerPage = Zend_Registry::get('config')->default->paginator->rowsPerPage;
        }
        if ($page === NULL) {
            $page = Zend_Controller_Front::getInstance()->getRequest()->getParam('page');
        }
        $data = $this->getFullTree();
        $paginator = $this->getMapper()->paginatorArray($data, $rowsPerPage, $page);
        return $paginator;
    }

    public function getChildrenIdsByRootIdArray(array $rootIds)
    {
        return $this->getMapper()->fetchChildrenIdsByRootIdArray($rootIds);
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
            foreach ($tree as $category) {
                $name = str_repeat(' - ', $category->tree_level - 1) . $category->name;
                if ($useId === TRUE) {
                    $key = $category->id;
                }
                else {
                    $key = $category->tree_id;
                }
                $list[$key] = $name;
            }
        }
        return $list;
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
            throw new Model_Service_Exception('category by tree_id = "'.$treeId.'" not found');
        }
        return $object;
    }


    /**
     * @param mixed Model_Object_Interface|int
     * @return Model_Collection_Category
     */
    public function getAllByItem($item)
    {
        if ($item instanceof Model_Object_Interface) {
            $item = $item->id;
        }
        return $this->getMapper()->fetchAllByItemId($item);
    }

    /**
     * 
     * @param mixed Model_Object_Interface|int  - item object or item id
     * @param mixed Model_Object_Interface|int  - category object or category id
     * @param Model_Collection_Interface|FALSE  - if item is not linked to any category return FALSE 
     */
    public function getParentsOfItem($item, $category = NULL)
    {
        if ( ! empty($category)) {
            if ($category instanceof Model_Object_Interface) {
                $categoryId = $category->id;
            }
            else {
                $categoryId = $category;
            }
            $parents = $this->getParentsOf($categoryId, TRUE);
        }
        else {
            $linkedColl = $this->getAllByItem($item);
            if ($linkedColl->count()) {
                $category = $linkedColl->get(0);
                $parents = $this->getParentsOf($category->id, TRUE);
            }
            else {
                $parents = FALSE;
            }
        }
        return $parents;
    }


    /**
     * @param string
     * @return Model_Object_Interface
     */
    public function getComplexBySeoId($seoId)
    {
        $result = $this->getMapper()->fetchOneBySeoId($seoId);
        return $result;
    }
    
    public function processImport()
    {
        return $this->getHelper('Importer')->process();
    }
    

}
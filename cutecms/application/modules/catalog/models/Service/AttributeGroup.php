<?php

class Catalog_Model_Service_AttributeGroup extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Catalog_Model_Object_AttributeGroup',
        'Model_Collection_Interface' => 'Catalog_Model_Collection_AttributeGroup',
        'Model_Mapper_Interface' => 'Catalog_Model_Mapper_Db_AttributeGroup',
        'Model_Service_Language',
    );



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
     * @return Model_Collection_AttributeGroup
     */
    public function getAllByAttribute($attr)
    {
        if ($attr instanceof Model_Object_Interface) {
            $attr = $attr->id;
        }
        return $this->getMapper()->fetchAllByAttributeId($attr);
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
            throw new Model_Service_Exception('attribute group by tree_id = "'.$treeId.'" not found');
        }
        return $object;
    }


    /**
     * get all tree (optionally excluding branch) as array of options for form select
     * @param int $excludeBranchRootId
     * @param bool use id instead of tree_id
     * @return array
     */
    public function getFullTreeAsSelectOptions($excludeBranchRootId = NULL, $useId = FALSE, $rootText = NULL)
    {
        $tree = $this->getTreeWithoutBranch($excludeBranchRootId);
        if ($rootText === NULL) {
            $rootText = ' << '. $this->getTranslator()->_('Root') . ' >> ';
        }
        $list = array(0 => $rootText);
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

    public function getGroupsList()
    {
        $result = $this->getMapper()->fetchGroupsList();
        return $result;
    }

    public function getGroupsWithAttributesList()
    {
        $result = $this->getMapper()->fetchGroupsWithAttributesList();
        return $result;
    }
}
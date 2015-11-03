<?php

class Model_Service_Faq extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
    	'Model_Mapper_Interface' => 'Model_Mapper_Db_Faq',
        'Model_Object_Interface' => 'Model_Object_Faq',
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


	/**
	 * @param mixed (string|int) seo_id or id of the page
	 * @return Model_Object_Page
	 */
	public function get($id)
	{
		if (is_numeric($id)) {
			$page = $this->getMapper()->fetchOneById($id);
		}
		else if (is_string($id)) {
			$page = $this->getMapper()->fetchOneBySeoId($id);
		}
		else {
			throw new Model_Service_Exception('unknown parameter for get method - '.$id);
		}
		return $page;
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
     * get all active objects (possible limited)
     * @param int
     * @return Model_Collection_Interface
     */
    public function getAllActive($limit = NULL)
    {
        return $this->getMapper()->fetchAllActive($limit);
    }


    public function paginatorGetAllActive($rowsPerPage = NULL, $page = NULL)
    {
        if ($rowsPerPage === NULL) {
            $rowsPerPage = Zend_Registry::get('config')->default->paginator->rowsPerPage;
        }
        if ($page === NULL) {
            $page = Zend_Controller_Front::getInstance()->getRequest()->getParam('page');
        }
        $paginator = $this->getMapper()->paginatorFetchAllActive(NULL, $rowsPerPage, $page);
        return $paginator;
    }


}
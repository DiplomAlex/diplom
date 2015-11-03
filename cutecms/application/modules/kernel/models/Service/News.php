<?php

class Model_Service_News extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
    	'Model_Mapper_Interface' => 'Model_Mapper_Db_News',
        'Model_Object_Interface' => 'Model_Object_News',
        'Model_Service_Language',
        'Model_Service_Helper_Multisite',
    );


    /**
     * initializes object
     * @see Model_Service_Abstract::init()
     */
    public function init()
    {
        $lang = Model_Service::factory('language');
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
        $obj = $this->getComplex($id);
        $values = $obj->toArray();
        $descs = $this->getMapper()->getPlugin('Description')->fetchDescriptions($id);
        $rcs = $this->getMapper()->getPlugin('Resource')->fetchResources($obj);
        $values = $values + $descs + $rcs;
        return $values;
    }


    /**
     * get several last news
     * @param int limit
     * @return Model_Collection_Interface
     */
    public function getLatestActive($limit = NULL)
    {
        return $this->getMapper()->fetchLatestActiveByDate($limit);
    }

    public function getLatestActiveForMainPage()
    {
        return $this->getMapper()->fetchLatestActive('news_main_page = 1');
    }

    /**
     * @param string
     * @return Model_Object_Interface
     */
    public function getComplexBySeoId($seoId)
    {
        return $this->getMapper()->fetchOneActiveBySeoId($seoId);
    }

    /**
     * @return Zend_Paginator
     */
    public function paginatorGetAllActive($rowsPerPage = NULL, $page = NULL, $topicId = NULL)
    {
        if ($rowsPerPage === NULL) {
            $rowsPerPage = Zend_Registry::get('config')->default->paginator->rowsPerPage;
        }
        if ($page === NULL) {
            $page = Zend_Controller_Front::getInstance()->getRequest()->getParam('page');
        }
        $paginator = $this->getMapper()->paginatorFetchLatestActive($rowsPerPage, $page, $topicId);
        return $paginator;
    }


    public function getAllIdsByTopics(array $topics)
    {
        $newsAll = $this->getMapper()->fetchComplexByTopics($topics);
        $list = array();
        foreach ($newsAll as $news) {
            $list []= $news->id;
        }
        return $list;
    }

    public function getAllByTopicSeoId($topic)
    {
        return $this->getMapper()->fetchComplexByTopicSeoId($topic);
    }

    public function getAllByNewsSeoIdFull($seo_id)
    {
        return $this->getMapper()->fetchComplexByNewsSeoIdFull($seo_id);
    }
    
}
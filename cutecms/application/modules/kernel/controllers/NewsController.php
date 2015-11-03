<?php

class NewsController extends Zend_Controller_Action
{
    
    /**
     * @var Model_Service_Interface
     */
    protected $_newsService = NULL;
    
    /**
     * @var Model_Service_Interface
     */
    protected $_topicService = NULL;
    
    
    protected function _getNewsService()
    {
        if ($this->_newsService === NULL) {
            $this->_newsService = Model_Service::factory('news');
            $site = Model_Service::factory('site')->getCurrent();
            $this->_newsService->getHelper('Multisite')->setCurrentSiteId($site->id);
        }
        return $this->_newsService;
    }
    
    protected function _getTopicService()
    {
        if ($this->_topicService === NULL) {
            $this->_topicService = Model_Service::factory('news-topic');
            $site = Model_Service::factory('site')->getCurrent();
            $this->_topicService->getHelper('Multisite')->setCurrentSiteId($site->id);
        }
        return $this->_topicService;
    }
    
    public function init()
    {
        if ($this->getRequest()->getActionName() != 'unsubscribe') {
            App_Event::factory('Controller__init', array($this))->dispatch();
            if ($this->getRequest()->isXmlHttpRequest()) {
                $this->view->layout()->disableLayout();
            }
        }
    }

    public function indexAction()
    {
        $this->view->rowsPerTopic = Zend_Registry::get('config')->news->indexRowsPerTopic;
        $this->view->news = $this->_getNewsService()->getLatestActive();
    }

    public function topicAction()
    {
        $topicId = $this->_getParam('topic_id');
        $this->view->news = $this->_getNewsService()->paginatorGetAllActive(
            $this->_getParamRowsPerPage(),
            $this->_getParam('page'),
            $topicId
        );
    }

    public function detailedAction()
    {
    	$this->view->news = $this->_getNewsService()->getComplexBySeoId($this->_getParam('seo_id'));
    }

    public function ajaxSubscribeAction()
    {
        $userId = Model_Service::factory('user')->getCurrent()->id;
        $topicId = $this->_getParam('topic_id');
        $this->view->subscribed = $this->_getTopicService()->switchSubscriptionState($topicId, $userId);
    }

    public function unsubscribeAction()
    {
        $key = $this->_getParam('key');
        $topicId = $this->_getParam('topic_id');
        $topicService = $this->_getTopicService();
        if ( ! $user = Model_Service::factory('user')->getOneByAuthKey($key)) {
            throw new Zend_Controller_Exception('user with authkey="'.$key.'" not found');
        }
        else {
            $topicService->switchSubscriptionState($topicId, $user->id);
            $this->view->topic = $topicService->get($topicId);
            $this->view->user = $user;
        }
    }
    
    protected function _getParamRowsPerPage()
    {
        return $this->_getParam('rowsPerPage', $this->getHelper('RowsPerPage')->getValue());
    }
    

}

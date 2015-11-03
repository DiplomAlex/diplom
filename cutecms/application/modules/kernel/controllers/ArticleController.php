<?php

class ArticleController extends Zend_Controller_Action
{ 
        
    protected $_articleService = NULL;
    protected $_topicService = NULL;
    
    protected function _getArticleService()
    {
        if ($this->_articleService === NULL) {
            $this->_articleService = Model_Service::factory('article');
            $site = Model_Service::factory('site')->getCurrent();
            $this->_articleService->getHelper('Multisite')->setCurrentSiteId($site->id);
        }
        return $this->_articleService;
    }

    protected function _getTopicService()
    {
        if ($this->_topicService === NULL) {
            $this->_topicService = Model_Service::factory('article-topic');
            $site = Model_Service::factory('site')->getCurrent();
            $this->_topicService->getHelper('Multisite')->setCurrentSiteId($site->id);
        }
        return $this->_topicService;
    }    
    
    public function init()
    {
        App_Event::factory('Controller__init', array($this))->dispatch();
    }
    
    public function indexAction()
    {
        $topicService = $this->_getTopicService();
        $articleService = $this->_getArticleService();
        $seoId = $this->_getParam('seo_id');
        if ($seoId) {
            $topic = $topicService->getComplexBySeoId($seoId);
            $topicId = $topic->id;
            $this->view->topic = $topic;
        }
        else {
            $topicId = NULL;
        }
        $this->view->topics = $topicService->getAllByParent($topicId);
        $this->view->articles = $articleService->paginatorGetAllByTopic(
            $topicId,
            $this->getHelper('RowsPerPage')->saveValue()->getValue(),
            $this->_getParam('page'),
            TRUE            
        );
        if ($script = $this->_getIndexViewScript()) {
            $this->renderScript($script);
        }
    }
    
    public function detailedAction()
    {
        $topicService = $this->_getTopicService();
        $articleService = $this->_getArticleService();      
        $topicSeoId = $this->_getParam('topic_seo_id');        
        $articleSeoId = $this->_getParam('seo_id');
        if ($topicSeoId) {
            $topic = $topicService->getComplexBySeoId($topicSeoId);
            $this->view->topic = $topic;
        }        
        if ($articleSeoId) {
            $article = $articleService->getComplexBySeoId($articleSeoId);
            $this->view->article = $article;
            $this->view->gallery = $articleService->getHelper('Gallery')->getLinkedToContent($article->id);
        }
        if ($script = $this->_getDetailedViewScript()) {
            $this->renderScript($script);
        }
        
    }
    
    protected function _getIndexViewScript()
    {
        return $this->_getParam('indexViewScript', FALSE);
    }

    protected function _getDetailedViewScript()
    {
        return $this->_getParam('detailedViewScript', FALSE);
    }
        
}
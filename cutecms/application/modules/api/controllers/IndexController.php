<?php

class Api_IndexController extends Zend_Controller_Action
{

    /**
     * Тело запроса
     * 
     * @var null|string
     */
    protected $_rawBody = null;

    /**
     * Параметры запроса
     * 
     * @var array 
     */
    protected $_params = array();

    /**
     * Инициализация
     */
    public function init()
    {
        header('Content-Type: text/xml');
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->authenticate();
        $this->assemble();
    }

    /**
     * Авторизация
     */
    public function authenticate()
    {
        if (!$this->getRequest()->isPost()) {
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array('reset' => 'true')));
        }

        $this->_rawBody = trim($this->getRequest()->getRawBody());
    }

    /**
     * Установка метода и параметров
     */
    public function assemble()
    {
        $success   = true;
        $bodyKey   = 'request';
        $paramsKey = 'params';
        $methodKey = 'method';

        try {
            $xml = new SimpleXMLElement($this->_rawBody);
        } catch (Exception $ex) {
            $success = false;
        }

        if ($success) {
            if (isset($xml->$bodyKey->$methodKey)) {
                if (($method = trim($xml->$bodyKey->$methodKey->__toString()))) {
                    $this->_params[$methodKey] = $method;
                }
            }

            if (isset($xml->$bodyKey->$paramsKey)) {
                $this->_params[$paramsKey] = $xml->$bodyKey->$paramsKey;
            }
        }
    }

    public function indexAction()
    {
        $server = new App_Rest_Server();
        $historyService = Model_Service::factory('api/history');
        $server->setClass(get_class(Model_Service::factory('api/rest')));
        $server->returnResponse(true);
        $method = isset($this->_params['method']) ? $this->_params['method'] : null;
        $history = $historyService->saveFromValues(
            array('request' => $this->_rawBody, 'request_method' => $method), true
        );
        $response = $server->handle($this->_params);
        $historyService->saveFromValues(array('id' => $history->id, 'response' => $response));

        echo $response;
    }

}
<?php

class Issues_CommentController extends Zend_Controller_Action
{

    protected $_defaultInjections = array(
        'Form_Delete' => 'App_Form_Question',
    );


    /**
     * @return App_DIContainer
     */
    public function getInjector()
    {
        if ($this->_injector === NULL) {
            $this->_injector = new App_DIContainer($this);
        }
        return $this->_injector;
    }

    public function init()
    {
        $this->injectDefaults();
        App_Event::factory('Issues_Controller__init', array($this))->dispatch();
    }

    public function indexAction()
    {
    }

    public function editAction()
    {
    }

    public function deleteAction()
    {
    }


}
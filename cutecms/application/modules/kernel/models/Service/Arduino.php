<?php

class Model_Service_Arduino extends Model_Service_Abstract
{
    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Model_Object_Arduino',
        'Model_Mapper_Interface' => 'Model_Mapper_Db_Arduino',
    );

    public function paginatorGetByUser($user, $rowsPerPage, $page)
    {
        if ($rowsPerPage === NULL) {
            $rowsPerPage = Zend_Registry::get('config')->default->paginator->rowsPerPage;
        }
        if ($page === NULL) {
            $page = Zend_Controller_Front::getInstance()->getRequest()->getParam('page');
        }

        return $this->getMapper()->paginatorFetchByUsers($user, $rowsPerPage, $page);
    }
}

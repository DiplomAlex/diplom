<?php

interface Model_Mapper_User extends Model_Mapper_Interface
{

/*

     * addons for complex select
     * @param Zend_Db_Select
     * @return Zend_Db_Select

    protected function _onFetchComplex(Zend_Db_Select $select);


     * addon actions when building complex object
     * @param Model_Object_Interface $object
     * @param array $values
     * @return Model_Object_Interface

    protected function _onBuildComplexObject(Model_Object_Interface $object, array $values = NULL, $addedPrefix = TRUE);
*/

    /**
     * find user by login
     * @return Model_Object_User
     */
    public function fetchOneByLogin($login);

}
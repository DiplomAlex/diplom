<?php

interface Model_Mapper_Db_Plugin_Interface
{

    /**
     * rising when
     * @var unknown_type
     */
	const EVENT_BUILD_COMPLEX = 'buildComplex';
	const EVENT_FETCH_COMPLEX = 'fetchComplex';
    const EVENT_BEFORE_SAVE = 'beforeSave';
    const EVENT_AFTER_SAVE = 'afterSave';
    const EVENT_BEFORE_SAVE_COMPLEX = 'beforeSaveComplex';
    const EVENT_AFTER_SAVE_COMPLEX = 'afterSaveComplex';
	const EVENT_BEFORE_DELETE = 'beforeDelete';
    const EVENT_AFTER_DELETE = 'afterDelete';

	/**
	 * event dispatcher
	 * @param string $event
	 * @param array $params trasmitted to object
	 * @return mixed
	 */
	public function triggerEvent($event, array $params = NULL);

    /**
     * observer for beforeSaveComplex event
     * @param Model_Object_Interface $obj
     * @param array $values
     * @param bool $isNew
     * @return mixed
     */
	public function onBeforeSaveComplex(Model_Object_Interface $obj, array $values, $isNew = FALSE);

    /**
     * observer for afterSaveComplex event
     * @param Model_Object_Interface $obj
     * @param array $values
     * @param bool $isNew
     * @return mixed
     */
    public function onAfterSaveComplex(Model_Object_Interface $obj, array $values, $isNew = FALSE);

    /**
     * checks if plugin has its own table when  (description, resource, tree - have; sorting, filters - have not)
     * @return unknown_type
     */
    public function hasTable();

    /**
     * getter for linked mapper
     * @return Model_Mapper_Db_Interface
     */
    public function getMapper();

    /**
     * setter for linked mapper
     * @return Model_Mapper_Db_Plugin_Interface $this
     */
    public function setMapper(Model_Mapper_Db_Interface $mapper);
}
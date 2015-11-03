<?php

interface Model_Mapper_XML_Plugin_Interface
{

    /**
     * rising when
     * @var unknown_type
     */
	const EVENT_BUILD_COMPLEX = 'buildComplex';

	/**
	 * event dispatcher
	 * @param string $event
	 * @param array $params trasmitted to object
	 * @return mixed
	 */
	public function triggerEvent($event, array $params = NULL);


    /**
     * getter for linked mapper
     * @return Model_Mapper_Db_Interface
     */
    public function getMapper();

    /**
     * setter for linked mapper
     * @return Model_Mapper_Db_Plugin_Interface $this
     */
    public function setMapper(Model_Mapper_XML_Interface $mapper);
}
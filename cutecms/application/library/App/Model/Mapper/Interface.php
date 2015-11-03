<?php

interface Model_Mapper_Interface
{

    const EVENT_MAP_OBJECT = 'mapObject';
    const EVENT_UNMAP_OBJECT = 'unmapObject';
    const EVENT_PAGINATION = 'pagination';

	/**
	 * @param int $id
	 * @return Model_Object_Interface
	 */
	//public function fetchOneById($id);

	/**
	 * @return array(Model_Object_Interface)
	 */
	//public function fetchAll();

    /*
	public function save(Model_Object_Interface $object);

	public function delete($object);

    public function makeSimpleObject(array $values, $addedPrefix = TRUE);

    public function makeComplexObject(array $values);

    public function makeCustomObject(array $values);
    */

}

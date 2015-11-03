<?php

interface Model_Collection_Interface extends ArrayAccess, Iterator, Countable
{

    /**
     * append object to collection
     *
     * @param Model_Object_Interface
     * @return Model_Collection_Interface
     */
    public function add(Model_Object_Interface $object);

    /**
     *
     *
     * @param array array(Model_Object_Interface)
     * @return Model_Collection_Interface
     */
    public function addArray(array $object);

    /**
     * @param int offset
     * @return Model_Object_Interface
     */
    public function get($offset);

    /**
     * @param int offset
     * @param Model_Object_Interface
     * @return Model_Collection_Interface
     */
    public function set($offset, Model_Object_Interface $object);

    /**
     * @param int offset
     * @return Model_Collection_Interface
     */
    public function remove($offset);



    /**
     * return as array
     *
     * @return array of Model_Object_Interface
     */
    public function toArray();

    /**
     * counts objects in collection
     *
     * @return int
     */
    //public function count();

    /**
     * checks if collection is empty
     */
    public function isEmpty();


    /**
     * returns current object in collection
     * @return Model_Object_Interface
     */
    /*public function current();*/

    /**
     * returns next object in collection
     * @return Model_Object_Interface
     */
    //public function next();

    /**
     * returns prev object in collection
     * @return Model_Object_Interface
     */
    public function prev();

    /**
     * returns last object in collection
     * @return Model_Object_Interface
     */
    public function last();

    /**
     * returns first object in collection
     * @return Model_Object_Interface
     */
    //public function rewind();

    /**
     * returns key of current element
     * @return int
     */
    //public function key();

    /**
     * part of Iterator interface
     * Checks if current position is valid
     * @return bool
     */
    //public function valid();

}
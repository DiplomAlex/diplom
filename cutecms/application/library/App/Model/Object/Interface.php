<?php

interface Model_Object_Interface extends ArrayAccess
{

    const STYLE_SIMPLE = 0;
    const STYLE_COMPLEX = 1;
    const STYLE_CUSTOM = 2;


	const EVENT_BEFORE_SAVE = 'beforeSave';
	const EVENT_AFTER_SAVE = 'afterSave';
    const EVENT_BEFORE_SAVE_COMPLEX = 'beforeSaveComplex';
    const EVENT_AFTER_SAVE_COMPLEX = 'afterSaveComplex';
	/*const EVENT_DELETE = 'delete';*/
    const EVENT_BEFORE_DELETE = 'beforeDelete';
    const EVENT_AFTER_DELETE = 'afterDelete';
    const EVENT_BEFORE_MAP = 'beforeMap';
    const EVENT_AFTER_MAP = 'afterMap';
    const EVENT_BEFORE_UNMAP = 'beforeUnmap';
    const EVENT_AFTER_UNMAP = 'afterUnmap';
    
	/**
	 * @param string event name
	 */
	public function trigger($event);

	/**
	 * @return array associative arrays of object's data
	 */
	public function toArray();





    /**
     * @return int
     */
    public function getMappingStyle();

    /**
     * @param int one of consts STATE_
     */
    public function setMappingStyle($state);

	/**
	 * checks if object was built as simple - with subobjects with default makeSimpleObject mapper
	 * @return bool
	 */
	public function isSimple();

    /**
     * checks if object was built as complex - with subobjects
     * @return bool
     */
    public function isComplex();

    /**
     * set mapping style to STYLE_COMPLEX
     * @return Model_Object_Interface
     */
    public function setIsComplex();

    /**
     * checks if object was built as complex - with subobjects
     * @return bool
     */
    public function isCustom();











	/**
	 * init object elements
	 */
	public function init();


	/**
	 * proxie to _form addElement
	 */
	public function addElement($name, $options = null);

    public function addElements(array $elements);


	/**
	 * proxie to _form addElement
	 */
    public function getElements();


	/**
	 * proxie to _form getValue
	 */
	public function getValue($name);

	/**
	 * sets element value
	 * @param string elem name
	 * @param mixed value
	 * @return $this
	 */
	public function setValue($name, $value);

	/**
	 * proxie to _form hasElement
	 */
	public function hasElement($name);


	/**
	 * proxie to _form populate
	 */
	public function populate(array $values);

	/**
	 * alias to addSubForm
	 * @param Model_Object_Interface
	 * @param string
	 * @return $this
	 */
	public function addSubObject(Model_Object_Interface $object, $name);

	/**
	 * checks subobject presence
	 * @param string
	 * @return bool
	 */
	public function hasSubObject($name);

	/**
	 * sets subobject
	 * @param Model_Object_Interface
	 * @param string
	 * @return $this
	 */
	public function setSubObject($object, $name);

	/**
	 * gets all subobjects
	 * @return array('Name'=>Model_Object_Interface, ...)
	 */
	public function getSubObjects();




}
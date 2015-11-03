<?php

require_once(realpath(dirname(__FILE__).'/AttrException.php'));

class Attr {

	private static $_mysqli = NULL;
	private static $_prefix = NULL;
	private static $_instance = NULL;
	public $attribute;
	public $value;
	public $entity;
	public $group;
	public $groupvalue;
	public $controller;

	public function load($cn) {
		$cfn = 'Attr'.$cn;
		require_once(realpath(dirname(__FILE__).'/'.$cfn.'.php'));
		$this->{strtolower($cn)} = new $cfn();
	}

	private function __construct() {
		$this->load('Attribute');
		$this->load('Value');
		$this->load('Entity');
		$this->load('Group');
		$this->load('GroupValue');
		$this->load('Controller');
	}

	public static function init($mysqli_connection, $prefix) {
		self::$_mysqli = $mysqli_connection;
		self::$_prefix = $prefix;
		self::$_instance = new self();
	}

	public static function mysqli() {
		return self::$_mysqli;
	}

	public static function prefix() {
		return self::$_prefix;
	}

	public static function inst() {
		if (self::$_instance) {
			return self::$_instance;
		} else {
			throw new AttrException("Can't get instance of Attr class, call init() first");
		}
	}

	public function setValues($uid, array $data) {
		return $this->entity->save($uid, $data, 'uid');
	}

	public function getValues($uid) {
		$data = $this->entity->get($uid, 'uid');
		return $data?$data:array();
	}

	public function getAttribute($name) {
		return $this->attribute->getByName($name);
	}

	public function getAttributes() {
		return $this->attribute->getPreloadedAttributes();
	}

	public function deleteValues($uid) {
		return $this->entity->delete($uid, 'uid');
	}

}
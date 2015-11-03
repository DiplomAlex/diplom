<?php

class AttrGroup extends AttrEntity {
	
	protected $table;
	protected $valueModel;

	public function __construct() {
		$this->table = 'group';
		$this->valueModel = 'groupvalue';
	}
}
<?php

class AttrGroupValue extends AttrValue {

	public function __construct() {
		$this->table = 'attribute_to_group';
		$this->cacheTable = 'group';
		$this->field = 'group_id';
	}
}
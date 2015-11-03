<?php

class AttrValue {
	
	protected $table;
	protected $field;
	protected $cacheTable;

	protected $deferredInvalidate = FALSE;
	protected $deferredInvalidateList = array();

	public function startDeferredInvalidate() {
		$this->deferredInvalidate = TRUE;
	}

	public function processDeferredInvalidate() {
		$this->deferredInvalidate = FALSE;
		foreach ($this->deferredInvalidateList as $key => $value) {
			$this->invalidateCache($key);
		}
	}

	public function __construct() {
		$this->table = 'value';
		$this->cacheTable = 'entity';
		$this->field = 'entity_id';
	}

	public function delete($attr_id, $entity_id = NULL) {
		//echo("\n AttrValue: delete");
		//print_r(array($attr_id, $entity_id));
		$l = Attr::mysqli();
		if ($entity_id) {
			if ($attr_id) {
				$q = $l->prepare('DELETE FROM `'.Attr::prefix().$this->table.'` WHERE `attr_id` = ? AND `'.$this->field.'` = ?');
				$q->bind_param('dd', $attr_id, $entity_id);
			} else {
				$q = $l->prepare('DELETE FROM `'.Attr::prefix().$this->table.'` WHERE `'.$this->field.'` = ?');
				$q->bind_param('d', $entity_id);
			}
		} else {
			$q = $l->prepare('DELETE FROM `'.Attr::prefix().$this->table.'` WHERE `attr_id` = ?');
			$q->bind_param('d', $attr_id);
		}
		$q->execute();
		$q->close();
		
		if ($entity_id) {
			Attr::inst()->{$this->cacheTable}->invalidateCache($entity_id);
		} else {
			if ($attr_id) {
				$this->invalidateCache($attr_id);
			}
		}
	}

	public function invalidateCache($attr_id) {
		if ($this->deferredInvalidate) {
			$this->deferredInvalidateList[$attr_id] = TRUE;
			return;
		}
		//echo("\n AttrValue: invalidateCache");
		$l = Attr::mysqli();
		$q = $l->prepare('UPDATE `'.Attr::prefix().$this->cacheTable.'` SET `cache_valid` = NULL WHERE `id` IN (SELECT `'.$this->field.'` FROM `'.Attr::prefix().$this->table.'` WHERE `attr_id` = ?)');
		$q->bind_param('d', $attr_id); 
		$q->execute();
		$q->close();
	}

	public function get($entity_id) {
		//echo("\n AttrValue: get");
		$l = Attr::mysqli();
		$value_int = $value_float = $value_datetime = $value_string = $value_boolean = $value_text = $attr_id = $name = $type = NULL;
		$q = $l->prepare('SELECT `value_int`, `value_float`, `value_datetime`, `value_string`, `value_boolean`, `value_text`, `attr_id`, `name`, `type` FROM `'.Attr::prefix().$this->table.'`, `'.Attr::prefix().'attribute` WHERE `id` = `attr_id` AND `'.$this->field.'` = ?');
		$q->bind_param('d', $entity_id);
		$q->execute();
		$q->bind_result($value_int, $value_float, $value_datetime, $value_string, $value_boolean, $value_text, $attr_id, $name, $type);
		$data = array();
		while ($q->fetch()) {
			$data[$attr_id] = array('name' => $name, 'type' => $type, 'value' => ${'value_'.$type});
		}
		$q->close();
		return $data;
	}

	public function save($entity_id, $attr_id, $value, $new = FALSE) {
		//echo("\n AttrValue: save");
		$l = Attr::mysqli();
		$attr = Attr::inst()->attribute->getById($attr_id);
		$types = array('int'=>'int', 'float'=>'float', 'datetime'=>'datetime', 'string'=>'string', 'boolean'=>'boolean', 'text'=>'text');
		$values = array();
		foreach ($types as $k => $t) {
			$types[$k] = '`value_'.$t.'` = '.($v = ($k == $attr['type'])?'?':'NULL');
			$values[] = $v;
		}
		if ($new) {
			$q = $l->prepare('INSERT INTO `'.Attr::prefix().$this->table.'` (`attr_id`, `entity_id`, `value_'.implode('`, `value_', array_keys($types)).'`) VALUES (?, ?, '.implode(', ', $values).')');
			$q->bind_param('dds', $attr_id, $entity_id, $value); 
		} else {
			$q = $l->prepare('UPDATE `'.Attr::prefix().$this->table.'` SET '.implode(', ', $types).' WHERE `attr_id`= ? AND `'.$this->field.'` = ?');
			$q->bind_param('sdd', $value, $attr_id, $entity_id); 
		}
		$q->execute();
		$q->close();
		Attr::inst()->{$this->cacheTable}->invalidateCache($entity_id);
	}

}
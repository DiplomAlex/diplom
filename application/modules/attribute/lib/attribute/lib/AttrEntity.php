<?php

class AttrEntity {

	protected $table;
	protected $valueModel;

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
		$this->table = 'entity';
		$this->valueModel = 'value';
	}

	public function invalidateCache($entity_id) {
		if ($this->deferredInvalidate) {
			$this->deferredInvalidateList[$entity_id] = TRUE;
			return;
		}
		//echo("\n AttrEntity: invalidateCache");
		//сброс сериализованного кеша значений
		$l = Attr::mysqli();
		$q = $l->prepare('UPDATE `'.Attr::prefix().$this->table.'` SET `cache_valid` = NULL WHERE `id` = ?');
		$q->bind_param('d', $entity_id); 
		$q->execute();
		$q->close();
	}

	public function delete($entity_id, $keyField = 'id') {
		//echo("\n AttrEntity: delete");
		Attr::inst()->{$this->valueModel}->startDeferredInvalidate();
		$data = $this->getByField($entity_id, $keyField);
		if (!$data['id']) return;
		$l = Attr::mysqli();
		$q = $l->prepare('DELETE FROM `'.Attr::prefix().$this->table.'` WHERE `id` = ?');
		$q->bind_param('d', $data['id']);	
		$q->execute();
		$q->close();
		Attr::inst()->{$this->valueModel}->delete(NULL, $data['id']);
	}

	public function saveCache($entity_id, $data) {
		//echo("\n AttrEntity: saveCache");
		$l = Attr::mysqli();
		$q = $l->prepare('UPDATE `'.Attr::prefix().$this->table.'` SET `cache_valid` = 1, `cache` = ? WHERE `id` = ?');
		$q->bind_param('sd', serialize($data), $entity_id); 
		$q->execute();
		$q->close();
	}
/*
	public function getCache(&$entity_id, $keyField = 'id') {
		//false if !valid
		echo("\n AttrEntity: getCache");
		$l = Attr::mysqli();
		$cache = $cache_valid = $id = NULL;
		$q = $l->prepare('SELECT `cache`, `cache_valid`, `id` FROM `'.Attr::prefix().$this->table.'` WHERE `'.$keyField.'` = ?');
		$q->bind_param('s', $entity_id);
		$q->execute();
		$q->bind_result($cache, $cache_valid, $id);
		$data = NULL;
		if ($q->fetch()) {
			$entity_id = $id;
			if ($cache_valid) {
				$data = @unserialize($cache);
				if (!$data) {
					$data = array();
				}
			}
		}
		$q->close();
		return $data;
	}
*/
	public function getByField(&$entity_id, $keyField = 'id') {
		//echo("\n AttrEntity: getByField");
		$l = Attr::mysqli();
		$q = $l->prepare('SELECT `cache`, `cache_valid`, `id` FROM `'.Attr::prefix().$this->table.'` WHERE `'.$keyField.'` = ?');
		$q->bind_param('s', $entity_id);
		$q->execute();
		$cache = $cache_valid = $id = NULL;
		$q->bind_result($cache, $cache_valid, $id);
		if (!$q->fetch()) {
			$entity_id = NULL;
		} else {
			$entity_id = $id;
		}
		$q->close();
		return array('cache' => $cache, 'cache_valid' => $cache_valid, 'id' => $id);
	}

	public function get(&$entity_id, $keyField = 'id') {
		//echo("\n AttrEntity: get");
		$data = $this->getByField($entity_id, $keyField);
		if (!$entity_id) {
			return NULL;
		}
		if ($data['cache_valid']) {
			//echo("\n Cache valid");
			return @unserialize($data['cache']);
		}
		$data = Attr::inst()->{$this->valueModel}->get($entity_id);
		$ret = array();
		foreach ($data as $v) {
			$ret[$v['name']] = $v['value'];
		}
		if (count($ret)) {
			$this->saveCache($entity_id, $ret);
		}
		return $ret;
	}

	public function save($entity_id, array $data, $keyField = 'id') {
		//echo("\n AttrEntity: save");
		Attr::inst()->{$this->valueModel}->startDeferredInvalidate();
		$this->startDeferredInvalidate();
		$_entity_id = $entity_id;
		$prev = $this->get($entity_id, $keyField);
		
		if (!$entity_id) {
			$l = Attr::mysqli();
			$q = $l->prepare('INSERT INTO `'.Attr::prefix().$this->table.'` (`'.$keyField.'`) VALUES (?)');
			$q->bind_param('s', $_entity_id); 
			$q->execute();
			$q->close();
			$entity_id = $l->insert_id;
			$keyField = 'id';
			$prev = array();
		}
		$attrN = Attr::inst()->attribute->getNamedPreloadedAttributes();
		//$attrI = Attr::inst()->attribute->getPreloadedAttributes();
		if (!count($data)) {
			$this->delete($entity_id, $keyField);
			return;
		}
		foreach ($data as $name => $value) {
			if (!isset($attrN[$name])) {
				Attr::inst()->{$this->valueModel}-processDeferredInvalidate();
				throw new AttrException("Unknown attribute '".$name."'");
			}
			if (NULL !== $value) {
				Attr::inst()->{$this->valueModel}->save($entity_id, $attrN[$name]['id'], $value, !isset($prev[$name]));
			} else {
				Attr::inst()->{$this->valueModel}->delete($attrN[$name]['id'], $entity_id);
			}
		}
		foreach ($prev as $name => $value) {
			if (!array_key_exists($name, $data)) {
				Attr::inst()->{$this->valueModel}->delete($attrN[$name]['id'], $entity_id);
			}
		}
		Attr::inst()->{$this->valueModel}->processDeferredInvalidate();
		$this->processDeferredInvalidate();
	}

}
<?php

class AttrAttribute {

	const 
		INT = 'int',
		FLOAT = 'float',
		DATETIME = 'datetime',
		STRING = 'string',
		BOOLEAN = 'boolean',
		TEXT = 'text';

	private $attribsId = array();
	private $attribsName = array();

	public function __construct() {
		$this->preloadAttributes();
	}

	public function preloadAttributes() {
		$this->attribsId = $this->getAll();
		$this->attribsName = array();
		foreach ($this->attribsId as $a) {
			$this->attribsName[$a['name']] = $a;
		}
	}

	public function getPreloadedAttributes() {
		return $this->attribsId;
	}

	public function getNamedPreloadedAttributes() {
		return $this->attribsName;
	}

	public function getByName($name) {
		return $this->attribsName[$name];
	}
	
	public function getById($id) {
		return $this->attribsId[$id];
	}

	public function getAll() {
		$l = Attr::mysqli();
		$prev_id = $prev_type = $prev_value = $prev_name = NULL;
		$q = $l->prepare('SELECT `id`, `name`, `type`, `value` FROM `'.Attr::prefix().'attribute`');
		$q->execute();
		$q->bind_result($prev_id, $prev_name, $prev_type, $prev_value);
		$data = array();
		while($q->fetch()) {
			$data[$prev_id] = array('id' => $prev_id, 'name' => $prev_name, 'type' => $prev_type, 'value' => $prev_value);
		}
		$q->close();
		return $data;
	}

	protected function invalidateCache($id) {
		//сброс сериализованного кеша значений в наборах значений и группах предустановок
		Attr::inst()->value->invalidateCache($id);
		Attr::inst()->groupvalue->invalidateCache($id);
	}

	public function save($name, $type, $default) {
		//сохранение данных атрибута. если нужно - конвертируются и значения уже существующих
		$l = Attr::mysqli();
		//если атрибут с таким именем уже есть - нужно получить его id, текущий тип и значение по умолчанию
		$prev = $this->getByName($name);
		if (!$prev) {
			$q = $l->prepare('INSERT INTO `'.Attr::prefix().'attribute` (`name`, `type`, `value`) VALUES (?,?,?)');
			$q->bind_param('sss', $name, $type, $default); 
			$q->execute();
			$q->close();
		} else {
			//если тип или значение по умолчанию отличаются от предыдущих - обновляем в таблице атрибутов
			if (($type_diff = $type != $prev['type']) || ($default != $prev['value'])) {
				$q = $l->prepare('UPDATE `'.Attr::prefix().'attribute` SET `type` = ?, `value` = ? WHERE `name` = ?');
				$q->bind_param('sss', $type, $default, $name);
				$q->execute();
				$q->close();

				//если тип отличается - нужно сбросить флаг валидности кеша у наборов значений и группах атрибутов
				if ($type_diff) {

					//конвертировать значения атрибутов в группах и наборах значений в новый тип, а старые значения очистить
					$types = array('int'=>'int', 'float'=>'float', 'datetime'=>'datetime', 'string'=>'string', 'boolean'=>'boolean', 'text'=>'text');
					unset($types[$type]);
					foreach ($types as $k => $t) {
						$types[$k] = '`value_'.$t.'` = NULL';
					}
					$q = $l->prepare('UPDATE `'.Attr::prefix().'value` SET `value_'.$type.'` = `value_'.$prev['type'].'`, '.implode(', ', $types).' WHERE `attr_id`= ?');
					$q->bind_param('d', $prev['id']); 
					$q->execute();
					$q->close();

					$q = $l->prepare('UPDATE `'.Attr::prefix().'attribute_to_group` SET `value_'.$type.'` = `value_'.$prev['type'].'`, '.implode(', ', $types).' WHERE `attr_id`= ?');
					$q->bind_param('d', $prev['id']); 
					$q->execute();
					$q->close();

					$this->invalidateCache($prev['id']);
				}
			}
		}
	}

	public function delete($name) {
		//удаление атрибута по имени
		$l = Attr::mysqli();
		$prev = $this->getByName($name);
		if ($prev) {
			
			$q = $l->prepare('DELETE FROM `'.Attr::prefix().'attribute` WHERE `id` = ?');
			$q->bind_param('d', $prev['id']);
			$q->execute();
			$q->close();

			Attr::inst()->value->delete($prev['id']);
			Attr::inst()->groupvalue->delete($prev['id']);

			$this->invalidateCache($prev['id']);
		}
		
	}

}
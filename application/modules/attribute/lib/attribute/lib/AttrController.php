<?php

class AttrController {
	
	public function dispatch($action, array $params) {
		$_action = $action;
		$action.= 'Action';
		if (method_exists($this, $action)) {
			$this->{$action}($params);
		} else {
			throw new AttrException("Attribute action '".$_action."' not exists");
		}
	}

	public function proxyAction($params) {
		if (!isset($params['path'])) {
			throw new AttrException("Filename is empty");
		}
		$staticDir = realpath(dirname(__FILE__).'/../static');
		$filePath = realpath($staticDir.'/'.$params['path']);
		if (0 !== strpos($filePath, $staticDir)) {
			header('HTTP/1.0 403 Forbidden');
			throw new AttrException("Access denied");
		}
		$ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
		switch ($ext) {
			case 'js':
				header('Content-Type: application/javascript');
				break;
			case 'css':
				header('Content-Type: text/css');
				break;
		}
		@readfile($filePath);
	}

	public function entityAction($params) {
		require_once(realpath(dirname(__FILE__).'/../tpl/entity.php'));
	}

	public function attributesAction($params) {
		require_once(realpath(dirname(__FILE__).'/../tpl/attributes.php'));
	}

	public function getValuesAction($params) {
		header('Content-type: application/json');
		$data = Attr::inst()->getValues(@$params['uid']);
		foreach ($data as $key => $value) {
			$attr = Attr::inst()->getAttribute($key);
			$data[$key] = array('type' => $attr['type'], 'value' => $value);
		}
		die(json_encode($data));
	}

	public function getAttributesAction($params) {
		header('Content-type: application/json');
		$data = Attr::inst()->getAttributes();
		die(json_encode($data));
	}

	public function setValuesAction($params) {
		header('Content-type: application/json');
		$data = array();
		foreach ($params as $key => $value) {
			if (0 === strpos($key, 'attr_')) {
				$data[str_replace('attr_', '', $key)] = $value;
			}
		}
		$data = Attr::inst()->setValues(@$params['uid'], $data);
		die(json_encode($data));
	}

	public function setAttributesAction($params) {
		$attr = Attr::inst()->attribute;
		$data = array();
		foreach ($params as $key => $value) {
			if (0 === strpos($key, 'attr_')) {
				$data[$key = str_replace('attr_', '', $key)] = array('default' => $value, 'type'=>@$params['type_'.$key]);
			}
		}
		$prev = Attr::inst()->getAttributes();
		$_old = array();
		foreach ($prev as $p) {
			$_old[$p['name']] = $p['name'];
			if (!isset($data[$p['name']])) {
				$attr->delete($p['name']);
			} else {
				$d = $data[$p['name']];
				$attr->save($p['name'], $d['type'], $d['default']);
			}
		}
		foreach ($data as $k => $d) {
			if (!isset($_old[$k])) {
				$attr->save($k, $d['type'], $d['default']);
			}
		}
		die();
	}

}
<?php
/**
* $Id: Dbtree.php,v 2.1 2005/09/21 19:32:45 Kuzma Exp $
*
* Copyright (C) 2005 Kuzma Feskov <kuzma@russofile.ru>
* Zend_DB adaptation Anatoly Lelevkin <weltkind@inbox.ru>
*
* KF_SITE_VERSION
*
* CLASS DESCRIPTION:
* This class can be used to manipulate nested sets of database table
* records that form an hierarchical tree.
*
* It provides means to initialize the record tree, insert record nodes
* in specific tree positions, retrieve node and parent records, change
* position of nodes and delete record nodes.
*
* The library works with Zend_Db
*
* The library works with support multilanguage interface of
* technology GetText (GetText autodetection).
*
* This source file is part of the KFSITE Open Source Content
* Management System.
*
* This file may be distributed and/or modified under the terms of the
* "GNU General Public License" version 2 as published by the Free
* Software Foundation and appearing in the file LICENSE included in
* the packaging of this file.
*
* This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
* THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
* PURPOSE.
*
* The "GNU General Public License" (GPL) is available at
* http://www.gnu.org/copyleft/gpl.html.
*
* CHANGELOG:
*
* v2.0
*
* [+] GetText autodetect added
*
* [+] DB libraries abstraction added
*/

class Model_Db_Plugin_NestedSets {
	/**
    * Detailed errors of a class (for the programmer and log-files)
    * array('error type (1 - fatal (write log), 2 - fatal (write log, send email)',
    * 'error info string', 'function', 'info 1', 'info 2').
    *
    * @var array
    */
	protected $ERRORS = array();

	/**
    * The information on a error for the user
    * array('string (error information)').
    *
    * @var array
    */
	protected $ERRORS_MES = array();

	/**
    * Name of the table where tree is stored.
    *
    * @var string
    */
	protected $table;

	/**
    * Unique number of node.
    *
    * @var bigint
    */
	protected $table_id;

	/**
    * @var integer
    */
	protected $table_left;

	/**
    * @var integer
    */
	protected $table_right;

	/**
    * Level of nesting.
    *
    * @var integer
    */
	protected $table_level;


	/**
    * Parent node.
    *
    * @var integer
    */
	protected $table_parent;


	/**
    * DB resource object.
    *
    * @var object
    */
	protected $res;

	/**
    * Databse layer object.
    *
    * @var object
    */
	protected $db;


	/**
	 *Additional condition
	 * @var string
	 * */
	protected $where = false;


	/**
    * The class constructor: initializes dbtree variables.
    *
    * @param string $table Name of the table
    * @param string $prefix A prefix for fields of the table(the example, mytree_id. 'mytree' is prefix)
    * @param object $db
    * @return object
    */
	public function __construct($table, $prefix, $db, $where = false) {
		$this->db = $db;
		$this->table = $table;
		$this->table_id = $prefix . '_id';
		$this->table_left = $prefix . '_left';
		$this->table_right = $prefix . '_right';
		$this->table_level = $prefix . '_level';
		$this->table_parent = $prefix . '_parent';

		if ($where!=false) $this->where=$where;

		unset($prefix, $table, $where);
	}

	/**
    * Sets initial parameters of a tree and creates root of tree
    * ATTENTION, all previous values in table are destroyed.
    *
    * @param array $data Contains parameters for additional fields of a tree (if is): 'filed name' => 'importance'
    * @return bool TRUE if successful, FALSE otherwise.
    */
	public function Clear($data = array()) {
		$sql = 'TRUNCATE ' . $this->table;

		$res = $this->db->query($sql);

		$sql = 'DELETE FROM ' . $this->table;

		$res = $this->db->query($sql);

		if (!empty($data)) {
			$fld_names = implode(', ', array_keys($data)) . ', ';
			$fld_values = '\'' . implode('\', \'', array_values($data)) . '\', ';
		}
        else {
            $fld_names = '';
            $fld_values = '';
        }

		$fld_names .= $this->table_left . ', ' . $this->table_right . ', ' . $this->table_level;
		$fld_values .= '1, 2, 0';

		$id = 0; /*$this->db->GenID($this->table . '_seq', 1);*/

		$sql = 'INSERT INTO ' . $this->table . ' (' . $this->table_id . ', ' . $fld_names . ') VALUES (' . $id . ', ' . $fld_values . ')';

		$res = $this->db->query($sql);

		return TRUE;
	}

	/**
    * Receives left, right and level for unit with number id.
    *
    * @param integer $section_id Unique section id
    * @param integer $cache Recordset is cached for $cache microseconds
    * @return array - left, right, level
    */
	public function GetNodeInfo($section_id, $cache = FALSE) {
		$sql = 'SELECT ' . $this->table_left . ', ' . $this->table_right . ', ' . $this->table_level . ', '.$this->table_parent.' FROM ' . $this->table . ' WHERE ' . $this->table_id . ' = ' . (int)$section_id;
		$res = $this->db->query($sql);

		if (0 == $res->rowCount()) {
			$this->ERRORS_MES[] = extension_loaded('gettext') ? _('no_element_in_tree') : 'no_element_in_tree';
			return FALSE;
		}
		$data = $res->fetch();
		unset($res);
		return array($data[$this->table_left], $data[$this->table_right], $data[$this->table_level], $data[$this->table_parent]);
	}

	/**
    * Receives parent left, right and level for unit with number $id.
    *
    * @param integer $section_id
    * @param integer $cache Recordset is cached for $cache microseconds
    * @param array $condition Array structure: array('and' => array('id = 0', 'id2 >= 3'), 'or' => array('sec = \'www\'', 'sec2 <> \'erere\'')), etc where array key - condition (AND, OR, etc), value - condition string
    * @return array - left, right, level
    */
	public function GetParentInfo($section_id, $condition = '', $cache = FALSE) {
		$node_info = $this->GetNodeInfo($section_id);
		if (FALSE === $node_info) {
			return FALSE;
		}
		list($leftId, $rightId, $level) = $node_info;
		$level--;
		if (!empty($condition)) {
			$condition = $this->_PrepareCondition($condition);
		}
		$sql = 'SELECT * FROM ' . $this->table
		. ' WHERE ' . $this->table_left . ' < ' . $leftId
		. ' AND ' . $this->table_right . ' > ' . $rightId
		. ' AND ' . $this->table_level . ' = ' . $level
		. $condition
		. ' ORDER BY ' . $this->table_left;

		$res=$this->db->query($sql);

		return $res->fetch();
	}


	/**
    * Add a new element in the tree to element with number $section_id.
    *
    * @param integer $section_id Number of a parental element
    * @param array $condition Array structure: array('and' => array('id = 0', 'id2 >= 3'), 'or' => array('sec = \'www\'', 'sec2 <> \'erere\'')), etc where array key - condition (AND, OR, etc), value - condition string
    * @param array $data Contains parameters for additional fields of a tree (if is): array('filed name' => 'importance', etc)
    * @return integer Inserted element id
    */
	public function Insert($section_id, $condition = '', $data = array()) {
		$node_info = $this->GetNodeInfo($section_id);
		if (FALSE === $node_info) {
			return FALSE;
		}
		list($leftId, $rightId, $level) = $node_info;
		$data[$this->table_left] = $rightId;
		$data[$this->table_right] = ($rightId + 1);
		$data[$this->table_level] = ($level + 1);
		$data[$this->table_parent] = $section_id;
		if (!empty($condition)) {
			$condition = $this->_PrepareCondition($condition);
		}
		$sql = 'UPDATE ' . $this->table . ' SET '
		. $this->table_left . '=CASE WHEN ' . $this->table_left . '>' . $rightId . ' THEN ' . $this->table_left . '+2 ELSE ' . $this->table_left . ' END, '
		. $this->table_right . '=CASE WHEN ' . $this->table_right . '>=' . $rightId . ' THEN ' . $this->table_right . '+2 ELSE ' . $this->table_right . ' END '
		. 'WHERE ' . $this->table_right . '>=' . $rightId;
		$sql .= $condition;

		//echo $sql;

		$this->db->beginTransaction();

		try{
			$this->db->query($sql);
			$this->db->insert($this->table, $data);
			$id = $this->db->lastInsertId();
			$this->db->commit();
			return $id;
		}
		catch (Exception $e){
			$this->db->rollBack();
		}
	}

	/**
    * Add a new element in the tree near element with number id.
    *
    * @param integer $ID Number of a parental element
    * @param array $condition Array structure: array('and' => array('id = 0', 'id2 >= 3'), 'or' => array('sec = \'www\'', 'sec2 <> \'erere\'')), etc where array key - condition (AND, OR, etc), value - condition string
    * @param array $data Contains parameters for additional fields of a tree (if is): array('filed name' => 'importance', etc)
    * @return integer Inserted element id
    */
	public function InsertNear($ID, $condition = '', $data = array()) {
		$node_info = $this->GetNodeInfo($ID);
		if (FALSE === $node_info) {
			return FALSE;
		}
		list($leftId, $rightId, $level) = $node_info;
		$data[$this->table_left] = ($rightId + 1);
		$data[$this->table_right] = ($rightId + 2);
		$data[$this->table_level] = ($level);

        /**
         * replaced by ddv - possible error?
         *
         * old code:
         * $data[$this->table_parent] = $section_id;
         */
        $data[$this->table_parent] = $ID;


		if (!empty($condition)) {
			$condition = $this->_PrepareCondition($condition);
		}
		$sql = 'UPDATE ' . $this->table . ' SET '
		. $this->table_left . ' = CASE WHEN ' . $this->table_left . ' > ' . $rightId . ' THEN ' . $this->table_left . ' + 2 ELSE ' . $this->table_left . ' END, '
		. $this->table_right . ' = CASE WHEN ' . $this->table_right . '> ' . $rightId . ' THEN ' . $this->table_right . ' + 2 ELSE ' . $this->table_right . ' END, '
		. 'WHERE ' . $this->table_right . ' > ' . $rightId;
		$sql .= $condition;

		$this->db->beginTransaction();

		try{
			$res = $db->query($sql);
			$this->db->insert($this->table, $data);
			$id=$this->db->lastInsertId();
			$this->db->commit();
			return $id;

		}
		catch (Exception $e){
			$this->db->rollBack();
		}
	}

	/**
		* Assigns a node with all its children to another parent.
		*
		* @param integer $ID node ID
		* @param integer $newParentId ID of new parent node
		* @param array $condition Array structure: array('and' => array('id = 0', 'id2 >= 3'), 'or' => array('sec = \'www\'', 'sec2 <> \'erere\'')), etc where array key - condition (AND, OR, etc), value - condition string
		* @return bool TRUE if successful, FALSE otherwise.
		*/
	public function MoveAll($ID, $newParentId, $condition = '') {
		$node_info = $this->GetNodeInfo($ID);
		if (FALSE === $node_info) {
			return FALSE;
		}
		list($leftId, $rightId, $level) = $node_info;
		$node_info = $this->GetNodeInfo($newParentId);
		if (FALSE === $node_info) {
			return FALSE;
		}
		list($leftIdP, $rightIdP, $levelP) = $node_info;
		if ($ID == $newParentId || $leftId == $leftIdP || ($leftIdP >= $leftId && $leftIdP <= $rightId) || ($level == $levelP+1 && $leftId > $leftIdP && $rightId < $rightIdP)) {
			$this->ERRORS_MES[] = extension_loaded('gettext') ? _('cant_move_tree') : 'cant_move_tree';
			return FALSE;
		}
		if (!empty($condition)) {
			$condition = $this->_PrepareCondition($condition);
		}
		if ($leftIdP < $leftId && $rightIdP > $rightId && $levelP < $level - 1) {
			$sql = 'UPDATE ' . $this->table . ' SET '
			. $this->table_level . ' = CASE WHEN ' . $this->table_left . ' BETWEEN ' . $leftId . ' AND ' . $rightId . ' THEN ' . $this->table_level.sprintf('%+d', -($level-1)+$levelP) . ' ELSE ' . $this->table_level . ' END, '
			. $this->table_right . ' = CASE WHEN ' . $this->table_right . ' BETWEEN ' . ($rightId+1) . ' AND ' . ($rightIdP-1) . ' THEN ' . $this->table_right . '-' . ($rightId-$leftId+1) . ' '
			. 'WHEN ' . $this->table_left . ' BETWEEN ' . $leftId . ' AND ' . $rightId . ' THEN ' . $this->table_right . '+' . ((($rightIdP-$rightId-$level+$levelP)/2)*2+$level-$levelP-1) . ' ELSE ' . $this->table_right . ' END, '
			. $this->table_left . ' = CASE WHEN ' . $this->table_left . ' BETWEEN ' . ($rightId+1) . ' AND ' . ($rightIdP-1) . ' THEN ' . $this->table_left . '-' . ($rightId-$leftId+1) . ' '
			. 'WHEN ' . $this->table_left . ' BETWEEN ' . $leftId . ' AND ' . $rightId . ' THEN ' . $this->table_left . '+' . ((($rightIdP-$rightId-$level+$levelP)/2)*2+$level-$levelP-1) . ' ELSE ' . $this->table_left . ' END , '
//            . $this->table_parent.' = '.$newParentId.'
            . $this->table_parent . ' = CASE WHEN ' . $this->table_id . ' = ' . $ID . ' THEN ' . $newParentId . ' ELSE ' . $this->table_parent . ' END '
			. ' WHERE ' . $this->table_left . ' BETWEEN ' . ($leftIdP+1) . ' AND ' . ($rightIdP-1);
		} elseif ($leftIdP < $leftId) {
			$sql = 'UPDATE ' . $this->table . ' SET '
			. $this->table_level . ' = CASE WHEN ' . $this->table_left . ' BETWEEN ' . $leftId . ' AND ' . $rightId . ' THEN ' . $this->table_level.sprintf('%+d', -($level-1)+$levelP) . ' ELSE ' . $this->table_level . ' END, '
			. $this->table_left . ' = CASE WHEN ' . $this->table_left . ' BETWEEN ' . $rightIdP . ' AND ' . ($leftId-1) . ' THEN ' . $this->table_left . '+' . ($rightId-$leftId+1) . ' '
			. 'WHEN ' . $this->table_left . ' BETWEEN ' . $leftId . ' AND ' . $rightId . ' THEN ' . $this->table_left . '-' . ($leftId-$rightIdP) . ' ELSE ' . $this->table_left . ' END, '
			. $this->table_right . ' = CASE WHEN ' . $this->table_right . ' BETWEEN ' . $rightIdP . ' AND ' . $leftId . ' THEN ' . $this->table_right . '+' . ($rightId-$leftId+1) . ' '
			. 'WHEN ' . $this->table_right . ' BETWEEN ' . $leftId . ' AND ' . $rightId . ' THEN ' . $this->table_right . '-' . ($leftId-$rightIdP) . ' ELSE ' . $this->table_right . ' END, '
//            . $this->table_parent.' = '.$newParentId.'
            . $this->table_parent . ' = CASE WHEN ' . $this->table_id . ' = ' . $ID . ' THEN ' . $newParentId . ' ELSE ' . $this->table_parent . ' END '
			. ' WHERE (' . $this->table_left . ' BETWEEN ' . $leftIdP . ' AND ' . $rightId. ' '
			. 'OR ' . $this->table_right . ' BETWEEN ' . $leftIdP . ' AND ' . $rightId . ')';
		} else {
			$sql = 'UPDATE ' . $this->table . ' SET '
			. $this->table_level . ' = CASE WHEN ' . $this->table_left . ' BETWEEN ' . $leftId . ' AND ' . $rightId . ' THEN ' . $this->table_level.sprintf('%+d', -($level-1)+$levelP) . ' ELSE ' . $this->table_level . ' END, '
			. $this->table_left . ' = CASE WHEN ' . $this->table_left . ' BETWEEN ' . $rightId . ' AND ' . $rightIdP . ' THEN ' . $this->table_left . '-' . ($rightId-$leftId+1) . ' '
			. 'WHEN ' . $this->table_left . ' BETWEEN ' . $leftId . ' AND ' . $rightId . ' THEN ' . $this->table_left . '+' . ($rightIdP-1-$rightId) . ' ELSE ' . $this->table_left . ' END, '
			. $this->table_right . ' = CASE WHEN ' . $this->table_right . ' BETWEEN ' . ($rightId+1) . ' AND ' . ($rightIdP-1) . ' THEN ' . $this->table_right . '-' . ($rightId-$leftId+1) . ' '
			. 'WHEN ' . $this->table_right . ' BETWEEN ' . $leftId . ' AND ' . $rightId . ' THEN ' . $this->table_right . '+' . ($rightIdP-1-$rightId) . ' ELSE ' . $this->table_right . ' END, '
            //. $this->table_parent.' = '.$newParentId.'
            . $this->table_parent . ' = CASE WHEN ' . $this->table_id . ' = ' . $ID . ' THEN ' . $newParentId . ' ELSE ' . $this->table_parent . ' END '
			. 'WHERE (' . $this->table_left . ' BETWEEN ' . $leftId . ' AND ' . $rightIdP . ' '
			. 'OR ' . $this->table_right . ' BETWEEN ' . $leftId . ' AND ' . $rightIdP . ')';
		}
		$sql .= $condition;

		$this->db->beginTransaction();
		try{
			$res=$this->db->query($sql);
			$this->db->commit();
			return TRUE;
		}
		catch (Exception $e){
			$this->db->rollBack();

		}
	}


	/**
    * Change items position.
    *
    * @param integer $id1 first item ID
    * @param integer $id2 second item ID
    * @return bool TRUE if successful, FALSE otherwise.
    */
	public function ChangePosition($id1, $id2) {
		$node_info = $this->GetNodeInfo($id1);
		if (FALSE === $node_info) {
			return FALSE;
		}
		list($leftId1, $rightId1, $level1) = $node_info;
		$node_info = $this->GetNodeInfo($id2);
		if (FALSE === $node_info) {
			return FALSE;
		}
		list($leftId2, $rightId2, $level2) = $node_info;
		$sql = 'UPDATE ' . $this->table . ' SET '
		. $this->table_left . ' = ' . $leftId2 .', '
		. $this->table_right . ' = ' . $rightId2 .', '
		. $this->table_level . ' = ' . $level2 .' '
		. 'WHERE ' . $this->table_id . ' = ' . (int)$id1;

		$this->db->beginTransaction();

		try{
			$res = $this->db->query($sql);


			$sql = 'UPDATE ' . $this->table . ' SET '
			. $this->table_left . ' = ' . $leftId1 .', '
			. $this->table_right . ' = ' . $rightId1 .', '
			. $this->table_level . ' = ' . $level1 .' '
			. 'WHERE ' . $this->table_id . ' = ' . (int)$id2;

			$res = $this->db->query($sql);
			$this->db->commit();
			return TRUE;
		}
		catch (Exception $e){
			$this->db->rollBack();
		}

	}

	/**
    * Swapping nodes within the same level and limits of one parent with all its children: $id1 placed before or after $id2.
    *
    * @param integer $id1 first item ID
    * @param integer $id2 second item ID
    * @param string $position 'before' or 'after' $id2
    * @param array $condition Array structure: array('and' => array('id = 0', 'id2 >= 3'), 'or' => array('sec = \'www\'', 'sec2 <> \'erere\'')), etc where array key - condition (AND, OR, etc), value - condition string
    * @return bool TRUE if successful, FALSE otherwise.
    */
	public function ChangePositionAll($id1, $id2, $position = 'after', $condition = '') {
		$node_info = $this->GetNodeInfo($id1);
		if (FALSE === $node_info) {
			$this->ERRORS_MES[] = extension_loaded('gettext') ? _('cant_change_position') : 'cant_change_position';
			return FALSE;
		}
		list($leftId1, $rightId1, $level1) = $node_info;
		$node_info = $this->GetNodeInfo($id2);
		if (FALSE === $node_info) {
			$this->ERRORS_MES[] = extension_loaded('gettext') ? _('cant_change_position') : 'cant_change_position';
			return FALSE;
		}
		list($leftId2, $rightId2, $level2) = $node_info;
		if ($level1 <> $level2) {
			$this->ERRORS_MES[] = extension_loaded('gettext') ? _('cant_change_position') : 'cant_change_position';
			return FALSE;
		}
		if ('before' == $position) {
			if ($leftId1 > $leftId2) {
				$sql = 'UPDATE ' . $this->table . ' SET '
				. $this->table_right . ' = CASE WHEN ' . $this->table_left . ' BETWEEN ' . $leftId1 . ' AND ' . $rightId1 . ' THEN ' . $this->table_right . ' - ' . ($leftId1 - $leftId2) . ' '
				. 'WHEN ' . $this->table_left . ' BETWEEN ' . $leftId2 . ' AND ' . ($leftId1 - 1) . ' THEN ' . $this->table_right . ' +  ' . ($rightId1 - $leftId1 + 1) . ' ELSE ' . $this->table_right . ' END, '
				. $this->table_left . ' = CASE WHEN ' . $this->table_left . ' BETWEEN ' . $leftId1 . ' AND ' . $rightId1 . ' THEN ' . $this->table_left . ' - ' . ($leftId1 - $leftId2) . ' '
				. 'WHEN ' . $this->table_left . ' BETWEEN ' . $leftId2 . ' AND ' . ($leftId1 - 1) . ' THEN ' . $this->table_left . ' + ' . ($rightId1 - $leftId1 + 1) . ' ELSE ' . $this->table_left . ' END '
				. 'WHERE ' . $this->table_left . ' BETWEEN ' . $leftId2 . ' AND ' . $rightId1;
			} else {
				$sql = 'UPDATE ' . $this->table . ' SET '
				. $this->table_right . ' = CASE WHEN ' . $this->table_left . ' BETWEEN ' . $leftId1 . ' AND ' . $rightId1 . ' THEN ' . $this->table_right . ' + ' . (($leftId2 - $leftId1) - ($rightId1 - $leftId1 + 1)) . ' '
				. 'WHEN ' . $this->table_left . ' BETWEEN ' . ($rightId1 + 1) . ' AND ' . ($leftId2 - 1) . ' THEN ' . $this->table_right . ' - ' . (($rightId1 - $leftId1 + 1)) . ' ELSE ' . $this->table_right . ' END, '
				. $this->table_left . ' = CASE WHEN ' . $this->table_left . ' BETWEEN ' . $leftId1 . ' AND ' . $rightId1 . ' THEN ' . $this->table_left . ' + ' . (($leftId2 - $leftId1) - ($rightId1 - $leftId1 + 1)) . ' '
				. 'WHEN ' . $this->table_left . ' BETWEEN ' . ($rightId1 + 1) . ' AND ' . ($leftId2 - 1) . ' THEN ' . $this->table_left . ' - ' . ($rightId1 - $leftId1 + 1) . ' ELSE ' . $this->table_left . ' END '
				. 'WHERE ' . $this->table_left . ' BETWEEN ' . $leftId1 . ' AND ' . ($leftId2 - 1);
			}
		}
		if ('after' == $position) {
			if ($leftId1 > $leftId2) {
				$sql = 'UPDATE ' . $this->table . ' SET '
				. $this->table_right . ' = CASE WHEN ' . $this->table_left . ' BETWEEN ' . $leftId1 . ' AND ' . $rightId1 . ' THEN ' . $this->table_right . ' - ' . ($leftId1 - $leftId2 - ($rightId2 - $leftId2 + 1)) . ' '
				. 'WHEN ' . $this->table_left . ' BETWEEN ' . ($rightId2 + 1) . ' AND ' . ($leftId1 - 1) . ' THEN ' . $this->table_right . ' +  ' . ($rightId1 - $leftId1 + 1) . ' ELSE ' . $this->table_right . ' END, '
				. $this->table_left . ' = CASE WHEN ' . $this->table_left . ' BETWEEN ' . $leftId1 . ' AND ' . $rightId1 . ' THEN ' . $this->table_left . ' - ' . ($leftId1 - $leftId2 - ($rightId2 - $leftId2 + 1)) . ' '
				. 'WHEN ' . $this->table_left . ' BETWEEN ' . ($rightId2 + 1) . ' AND ' . ($leftId1 - 1) . ' THEN ' . $this->table_left . ' + ' . ($rightId1 - $leftId1 + 1) . ' ELSE ' . $this->table_left . ' END '
				. 'WHERE ' . $this->table_left . ' BETWEEN ' . ($rightId2 + 1) . ' AND ' . $rightId1;
			} else {
				$sql = 'UPDATE ' . $this->table . ' SET '
				. $this->table_right . ' = CASE WHEN ' . $this->table_left . ' BETWEEN ' . $leftId1 . ' AND ' . $rightId1 . ' THEN ' . $this->table_right . ' + ' . ($rightId2 - $rightId1) . ' '
				. 'WHEN ' . $this->table_left . ' BETWEEN ' . ($rightId1 + 1) . ' AND ' . $rightId2 . ' THEN ' . $this->table_right . ' - ' . (($rightId1 - $leftId1 + 1)) . ' ELSE ' . $this->table_right . ' END, '
				. $this->table_left . ' = CASE WHEN ' . $this->table_left . ' BETWEEN ' . $leftId1 . ' AND ' . $rightId1 . ' THEN ' . $this->table_left . ' + ' . ($rightId2 - $rightId1) . ' '
				. 'WHEN ' . $this->table_left . ' BETWEEN ' . ($rightId1 + 1) . ' AND ' . $rightId2 . ' THEN ' . $this->table_left . ' - ' . ($rightId1 - $leftId1 + 1) . ' ELSE ' . $this->table_left . ' END '
				. 'WHERE ' . $this->table_left . ' BETWEEN ' . $leftId1 . ' AND ' . $rightId2;
			}
		}
		if (!empty($condition)) {
			$condition = $this->_PrepareCondition($condition);
		}
		$sql .= $condition;

		$this->db->beginTransaction();

		try{

			$res = $this->db->query($sql);
			$this->db->commit();
			return TRUE;

		}
		catch (Exception $e){
			$this->db->rollBack();
		}


	}

	/**
    * Delete element with number $id from the tree wihtout deleting it's children.
    *
    * @param integer $ID Number of element
    * @param array $condition Array structure: array('and' => array('id = 0', 'id2 >= 3'), 'or' => array('sec = \'www\'', 'sec2 <> \'erere\'')), etc where array key - condition (AND, OR, etc), value - condition string
    * @return bool TRUE if successful, FALSE otherwise.
    */
	public function Delete($ID, $condition = '') {
		$node_info = $this->GetNodeInfo($ID);
		if (FALSE === $node_info) {
			return FALSE;
		}
		list($leftId, $rightId, $level, $parent) = $node_info;
		if (!empty($condition)) {
			$condition = $this->_PrepareCondition($condition);
		}
		$sql = 'DELETE FROM ' . $this->table . ' WHERE ' . $this->table_id . ' = ' . (int)$ID;

		$this->db->beginTransaction();
		try{

			$res = $this->db->query($sql);

			$sql = 'UPDATE ' . $this->table . ' SET '
			. $this->table_level . ' = CASE WHEN ' . $this->table_left . ' BETWEEN ' . $leftId . ' AND ' . $rightId . ' THEN ' . $this->table_level . ' - 1 ELSE ' . $this->table_level . ' END, '
			. $this->table_right . ' = CASE WHEN ' . $this->table_right . ' BETWEEN ' . $leftId . ' AND ' . $rightId . ' THEN ' . $this->table_right . ' - 1 '
			. 'WHEN ' . $this->table_right . ' > ' . $rightId . ' THEN ' . $this->table_right . ' - 2 ELSE ' . $this->table_right . ' END, '
			. $this->table_left . ' = CASE WHEN ' . $this->table_left . ' BETWEEN ' . $leftId . ' AND ' . $rightId . ' THEN ' . $this->table_left . ' - 1 '
			. 'WHEN ' . $this->table_left . ' > ' . $rightId . ' THEN ' . $this->table_left . ' - 2 ELSE ' . $this->table_left . ' END, '
            . $this->table_parent . ' = CASE WHEN ' . $this->table_parent . ' = ' . (int) $ID . ' THEN ' . $parent . ' ELSE ' . $this->table_parent . ' END '
			. 'WHERE ' . $this->table_right . ' > ' . $leftId;
			$sql .= $condition;

			$res = $this->db->query($sql);
			$this->db->commit();
			return TRUE;
		}
		catch (Exception $e){
			$this->db->rollBack();
		}

	}

	/**
    * Delete element with number $ID from the tree and all it childret.
    *
    * @param integer $ID Number of element
    * @param array $condition Array structure: array('and' => array('id = 0', 'id2 >= 3'), 'or' => array('sec = \'www\'', 'sec2 <> \'erere\'')), etc where array key - condition (AND, OR, etc), value - condition string
    * @return bool TRUE if successful, FALSE otherwise.
    */
	public function DeleteAll($ID, $condition = '') {
		$node_info = $this->GetNodeInfo($ID);
		if (FALSE === $node_info) {
			return FALSE;
		}
		list($leftId, $rightId) = $node_info;
		if (!empty($condition)) {
			$condition = $this->_PrepareCondition($condition);
		}
		$sql = 'DELETE FROM ' . $this->table . ' WHERE ' . $this->table_left . ' BETWEEN ' . $leftId . ' AND ' . $rightId;

		$this->db->beginTransaction();

		try{
			$res = $this->db->query($sql);
			$deltaId = (($rightId - $leftId) + 1);

			$sql = 'UPDATE ' . $this->table . ' SET '
			. $this->table_left . ' = CASE WHEN ' . $this->table_left . ' > ' . $leftId.' THEN ' . $this->table_left . ' - ' . $deltaId . ' ELSE ' . $this->table_left . ' END, '
			. $this->table_right . ' = CASE WHEN ' . $this->table_right . ' > ' . $leftId . ' THEN ' . $this->table_right . ' - ' . $deltaId . ' ELSE ' . $this->table_right . ' END '
			. 'WHERE ' . $this->table_right . ' > ' . $rightId;
			$sql .= $condition;

			$res = $this->db->query($sql);

			$this->db->commit();
			return TRUE;
		}
		catch (Exception $e){
			$this->db->rollBack();
		}
	}

	/**
    * Returns all elements of the tree sortet by left.
    *
    * @param array $condition Array structure: array('and' => array('id = 0', 'id2 >= 3'), 'or' => array('sec = \'www\'', 'sec2 <> \'erere\'')), etc where array key - condition (AND, OR, etc), value - condition string
    * @param array $fields needed fields (if is): array('filed1 name', 'filed2 name', etc)
    * @param integer $cache Recordset is cached for $cache microseconds
    * @return array needed fields
    */
	public function Full($fields, $condition = '', $cache = FALSE) {
		if (!empty($condition)) {
			$condition = $this->_PrepareCondition($condition, TRUE);
		}
		if (is_array($fields)) {
			$fields = implode(', ', $fields);
		} else {
			$fields = '*';
		}
		$sql = 'SELECT ' . $fields . ' FROM ' . $this->table;
		$sql .= $condition;
		$sql .= ' ORDER BY ' . $this->table_left;

		//echo $sql; die;

		$res = $this->db->query($sql);

		$this->res = $res;
		return TRUE;
	}

	/**
    * Returns all elements of a branch starting from an element with number $ID.
    *
    * @param array $condition Array structure: array('and' => array('id = 0', 'id2 >= 3'), 'or' => array('sec = \'www\'', 'sec2 <> \'erere\'')), etc where array key - condition (AND, OR, etc), value - condition string
    * @param array $fields needed fields (if is): array('filed1 name', 'filed2 name', etc)
    * @param integer $cache Recordset is cached for $cache microseconds
    * @param integer $ID Node unique id
    * @return array - [0] => array(id, left, right, level, additional fields), [1] => array(...), etc.
    */
	public function Branch($ID, $fields, $condition = '', $cache = FALSE) {
		if (is_array($fields)) {
			$fields = 'A.' . implode(', A.', $fields);
		} else {
			$fields = 'A.*';
		}
		if (!empty($condition)) {
			$condition = $this->_PrepareCondition($condition, FALSE, 'A.');
		}
		$sql = 'SELECT ' . $fields . ', CASE WHEN A.' . $this->table_left . ' + 1 < A.' . $this->table_right . ' THEN 1 ELSE 0 END AS nflag FROM ' . $this->table . ' A, ' . $this->table . ' B WHERE B.' . $this->table_id . ' = ' . (int)$ID . ' AND A.' . $this->table_left . ' >= B.' . $this->table_left . ' AND A.' . $this->table_right . ' <= B.' . $this->table_right;
		$sql .= $condition;
		$sql .= ' ORDER BY A.' . $this->table_left;

		$res = $this->db->query($sql);

		$this->res = $res;
		return TRUE;
	}

	/**
    * Returns all parents of element with number $ID.
    *
    * @param array $condition Array structure: array('and' => array('id = 0', 'id2 >= 3'), 'or' => array('sec = \'www\'', 'sec2 <> \'erere\'')), etc where array key - condition (AND, OR, etc), value - condition string
    * @param array $fields needed fields (if is): array('filed1 name', 'filed2 name', etc)
    * @param integer $cache Recordset is cached for $cache microseconds
    * @param integer $ID Node unique id
    * @return array - [0] => array(id, left, right, level, additional fields), [1] => array(...), etc.
    */
	public function Parents($ID, $fields, $condition = '', $cache = FALSE) {
		if (is_array($fields)) {
			$fields = 'A.' . implode(', A.', $fields);
		} else {
			$fields = 'A.*';
		}
		if (!empty($condition)) {
			$condition = $this->_PrepareCondition($condition, FALSE, 'A.');
		}
		$sql = 'SELECT ' . $fields . ', CASE WHEN A.' . $this->table_left . ' + 1 < A.' . $this->table_right . ' THEN 1 ELSE 0 END AS nflag FROM ' . $this->table . ' A, ' . $this->table . ' B WHERE B.' . $this->table_id . ' = ' . (int)$ID . ' AND B.' . $this->table_left . ' BETWEEN A.' . $this->table_left . ' AND A.' . $this->table_right;
		$sql .= $condition;
		$sql .= ' ORDER BY A.' . $this->table_left;

		$res = $this->db->query($sql);

		$this->res = $res;
		return TRUE;
	}

	/**
    * Returns a slightly opened tree from an element with number $ID.
    *
    * @param array $condition Array structure: array('and' => array('id = 0', 'id2 >= 3'), 'or' => array('sec = \'www\'', 'sec2 <> \'erere\'')), etc where array key - condition (AND, OR, etc), value - condition string
    * @param array $fields needed fields (if is): array('filed1 name', 'filed2 name', etc)
    * @param integer $cache Recordset is cached for $cache microseconds
    * @param integer $ID Node unique id
    * @return array - [0] => array(id, left, right, level, additional fields), [1] => array(...), etc.
    */
	public function Ajar($ID, $fields, $condition = '', $cache = FALSE) {
		if (is_array($fields)) {
			$fields = 'A.' . implode(', A.', $fields);
		} else {
			$fields = 'A.*';
		}
		$condition1 = '';
		if (!empty($condition)) {
			$condition1 = $this->_PrepareCondition($condition, FALSE, 'B.');
		}
		$sql = 'SELECT A.' . $this->table_left . ', A.' . $this->table_right . ', A.' . $this->table_level . ' FROM ' . $this->table . ' A, ' . $this->table . ' B '
		. 'WHERE B.' . $this->table_id . ' = ' . (int)$ID . ' AND B.' . $this->table_left . ' BETWEEN A.' . $this->table_left . ' AND A.' . $this->table_right;
		$sql .= $condition1;
		$sql .= ' ORDER BY A.' . $this->table_left;

		$res = $this->db->query($sql);


		if (0 == $res->rowCount()) {
			$this->ERRORS_MES[] = _('no_element_in_tree');
			return FALSE;
		}
		$alen = $res->rowCount();
		$i = 0;
		if (is_array($fields)) {
			$fields = implode(', ', $fields);
		} else {
			$fields = '*';
		}
		if (!empty($condition)) {
			$condition1 = $this->_PrepareCondition($condition, FALSE);
		}
		$sql = 'SELECT ' . $fields . ' FROM ' . $this->table . ' WHERE (' . $this->table_level . ' = 1';
		while ($row = $res->fetch()) {
			if ((++$i == $alen) && ($row[$this->table_left] + 1) == $row[$this->table_right]) {
				break;
			}
			$sql .= ' OR (' . $this->table_level . ' = ' . ($row[$this->table_level] + 1)
			. ' AND ' . $this->table_left . ' > ' . $row[$this->table_left]
			. ' AND ' . $this->table_right . ' < ' . $row[$this->table_right] . ')';
		}
		$sql .= ') ' . $condition1;
		$sql .= ' ORDER BY ' . $this->table_left;

		$res = $this->db->query($sql);

		$this->res = $res;
		return TRUE;
	}

	/**
    * Returns amount of lines in result.
    *
    * @return integer
    */
	public function RecordCount() {
		return $this->res->rowCount();
	}

	/**
    * Returns the current row.
    *
    * @return array
    */
	public function NextRow() {
		return $this->res->fetch();
	}

	/**
    * Transform array with conditions to SQL query
    * Array structure:
    * array('and' => array('id = 0', 'id2 >= 3'), 'or' => array('sec = \'www\'', 'sec2 <> \'erere\'')), etc
    * where array key - condition (AND, OR, etc), value - condition string.
    *
    * @param array $condition
    * @param string $prefix
    * @param bool $where - True - yes, flase - not
    * @return string
    */
	protected function _PrepareCondition($condition, $where = FALSE, $prefix = '') {
		if (!is_array($condition)) {
			return $condition;
		}
		$sql = ' ';
		if (TRUE === $where) {
			$sql .= 'WHERE ' . $prefix;
		}
		$keys = array_keys($condition);
		for ($i = 0;$i < count($keys);$i++) {
			if (FALSE === $where || (TRUE === $where && $i > 0)) {
				$sql .= ' ' . strtoupper($keys[$i]) . ' ' . $prefix;
			}
			$sql .= implode(' ' . strtoupper($keys[$i]) . ' ' . $prefix, $condition[$keys[$i]]);
		}
		return $sql;
	}

    public function getResult()
    {
        return $this->res;
    }


    /**
     * get first child of the same parent and same level
     * @param int node id
     * @param mixed string|array
     * @return array
     */
    public function getFirstSibling($id, $condition = '')
    {
        if (!empty($condition)) {
            $condition = $this->_PrepareCondition($condition);
        }

        $sql =    ' SELECT A.* FROM '.$this->table.' as A '
                . ' LEFT JOIN '.$this->table.' as B'
                . ' ON A.'.$this->table_parent.' = B.'.$this->table_id
                . ' LEFT JOIN '.$this->table.' as C'
                . ' ON C.'.$this->table_parent.' = B.'.$this->table_id
                . ' WHERE '
                        . ' C.'.$this->table_id.' = '.$id
                        . ' AND A.'.$this->table_left.' < C.'.$this->table_left
                        . ' AND A.'.$this->table_left.' > B.'.$this->table_left
                        . ' AND A.'.$this->table_level.' = C.'.$this->table_level
                        . $condition
                . ' ORDER BY A.'.$this->table_left.' ASC'
                . ' LIMIT 1'
                ;
        $node = $this->db->query($sql)->fetch();
        return $node;
    }

    /**
     * get last child of the same parent and same level
     * @param int node id
     * @param mixed string|array
     * @return array
     */
    public function getLastSibling($id, $condition = '')
    {
        if (!empty($condition)) {
            $condition = $this->_PrepareCondition($condition);
        }

        $sql =    ' SELECT A.* FROM '.$this->table.' as A '
                . ' LEFT JOIN '.$this->table.' as B'
                . ' ON A.'.$this->table_parent.' = B.'.$this->table_id
                . ' LEFT JOIN '.$this->table.' as C'
                . ' ON C.'.$this->table_parent.' = B.'.$this->table_id
                . ' WHERE '
                        . ' C.'.$this->table_id.' = '.$id
                        . ' AND A.'.$this->table_right.' < B.'.$this->table_right
                        . ' AND A.'.$this->table_right.' > C.'.$this->table_right
                        . ' AND A.'.$this->table_level.' = C.'.$this->table_level
                        . $condition
                . ' ORDER BY A.'.$this->table_right.' DESC'
                . ' LIMIT 1'
                ;
        $node = $this->db->query($sql)->fetch();
        return $node;
    }



    /**
     * get prev child of the same parent and same level
     * @param int node id
     * @param mixed string|array
     * @return array
     */
    public function getPrevSibling($id, $condition = '')
    {
        if (!empty($condition)) {
            $condition = $this->_PrepareCondition($condition);
        }

        $sql =    ' SELECT A.* FROM '.$this->table.' as A '
                . ' LEFT JOIN '.$this->table.' as B'
                . ' ON A.'.$this->table_parent.' = B.'.$this->table_id
                . ' LEFT JOIN '.$this->table.' as C'
                . ' ON C.'.$this->table_parent.' = B.'.$this->table_id
                . ' WHERE '
                        . ' C.'.$this->table_id.' = '.$id
                        . ' AND A.'.$this->table_left.' < C.'.$this->table_left
                        . ' AND A.'.$this->table_left.' > B.'.$this->table_left
                        . ' AND A.'.$this->table_level.' = C.'.$this->table_level
                        . $condition
                . ' ORDER BY A.'.$this->table_left.' DESC'
                . ' LIMIT 1'
                ;
        $node = $this->db->query($sql)->fetch();
        return $node;
    }

    /**
     * get next child of the same parent and same level
     * @param int node id
     * @param mixed string|array
     * @return array
     */
    public function getNextSibling($id, $condition = '')
    {
        if (!empty($condition)) {
            $condition = $this->_PrepareCondition($condition);
        }

        $sql =    ' SELECT A.* FROM '.$this->table.' as A '
                . ' LEFT JOIN '.$this->table.' as B'
                . ' ON A.'.$this->table_parent.' = B.'.$this->table_id
                . ' LEFT JOIN '.$this->table.' as C'
                . ' ON C.'.$this->table_parent.' = B.'.$this->table_id
                . ' WHERE '
                        . ' C.'.$this->table_id.' = '.$id
                        . ' AND A.'.$this->table_right.' < B.'.$this->table_right
                        . ' AND A.'.$this->table_right.' > C.'.$this->table_right
                        . ' AND A.'.$this->table_level.' = C.'.$this->table_level
                        . $condition
                . ' ORDER BY A.'.$this->table_right.' ASC'
                . ' LIMIT 1'
                ;
        $node = $this->db->query($sql)->fetch();
        return $node;
    }

    public function countAll($condition = '')
    {
        if (!empty($condition)) {
            $condition = $this->_PrepareCondition($condition);
        }

        $sql =    ' SELECT COUNT(*) AS cnt FROM '.$this->table
                . ' WHERE 1=1 ' . $condition;
        $cnt = $this->db->query($sql)->fetch();
        return $cnt['cnt'];
    }

    public function getRootId($condition = '')
    {
        if (!empty($condition)) {
            $condition = $this->_PrepareCondition($condition);
        }

        $sql =    ' SELECT * FROM '.$this->table
                . ' WHERE '.$this->table_level.' = 0 ' . $condition
                . ' LIMIT 1';
        $row = $this->db->query($sql)->fetch();
        if ($row === NULL) {
            throw new Model_Db_Plugin_Exception('root node corrupted in tree: '.$this->table);
        }
        return $row[$this->table_id];
    }


    public function getAllParents($id)
    {
        $sql =    ' SELECT DISTINCT A.* FROM '.$this->table.' as A '
                . ' LEFT JOIN '.$this->table.' as B '
                . ' ON B.'.$this->table_id.' = '.$id
                . ' WHERE A.'.$this->table_left .' <= B.'.$this->table_left
                . '   AND A.'.$this->table_right.' >= B.'.$this->table_right
                . '   AND A.'.$this->table_level.' > 0 '
                . ' ORDER BY A.'.$this->table_level.', A.'.$this->table_left;
        $rows = $this->db->query($sql)->fetchAll();
        return $rows;
    }

}

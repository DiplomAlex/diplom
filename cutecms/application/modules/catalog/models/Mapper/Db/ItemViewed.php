<?php

class Catalog_Model_Mapper_Db_ItemViewed extends Model_Mapper_Db_Abstract
{
	protected $_defaultInjections = array(
		'Model_Db_Table_Interface' => 'Catalog_Model_Db_Table_ItemViewed',
		'Model_Object_Interface' => 'Catalog_Model_Object_ItemViewed',
        'Model_Collection_Interface' => 'Catalog_Model_Collection_ItemViewed',
    );
		
	public function add($visitor)
    {	
		$select = $this->getTable()->select()
					->where('viewed_item_id = ?', $visitor['viewed_item_id'])
					->where('viewed_user_ip = ?', $visitor['viewed_user_ip']);
		$select = $select->query()->fetchAll();								
		if (!empty($select)){
			$this->getTable()->update($visitor, array(
					'viewed_user_ip IN (?)'=>$visitor['viewed_user_ip'], 
					'viewed_item_id IN (?)'=>$visitor['viewed_item_id']
					));
		}else{
			$this->getTable()->insert($visitor);
		}
    }
	
	
	public function fetchByIpAndItemId($visitor)
    {	
		$select = $this->getTable()->select()
					->where('viewed_item_id = ?', $visitor['viewed_item_id'])
					->where('viewed_user_ip = ?', $visitor['viewed_user_ip']);
		$select = $select->query()->fetchAll();								
		return $select;
    }
	
	public function fetchByIpAndLimit($Ip, $limit)
    {	
		$select = $this->getTable()->select()
					->order('viewed_item_number DESC')
					->where('viewed_user_ip = ?', $Ip)
					->limit($limit);
					
		$select = $select->query()->fetchAll();								
		return $select;
    }
}
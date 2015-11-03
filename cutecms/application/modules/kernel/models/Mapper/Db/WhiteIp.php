<?php


class Model_Mapper_Db_WhiteIp extends Model_Mapper_Db_Abstract
{

    protected $_defaultInjections = array(
        'Model_Db_Table_Interface' => 'Model_Db_Table_WhiteIps',
        'Model_Object_Interface' => 'Model_Object_WhiteIp',
        'Model_Collection_Interface' => 'Model_Collection_WhiteIp',
    );

    public function ipExists($ip)
    {
        $select = $this->getTable()->select()->from('white_ip', array('cnt'=>'COUNT(wip_ip)'))->where('wip_ip = ?', $ip);
        $result = $select->query()->fetch();
        return ($result['cnt']>0);
    }

}
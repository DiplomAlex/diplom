<?php

class Model_Service_WhiteIp extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Model_Object_WhiteIp',
        'Model_Mapper_Interface' => 'Model_Mapper_Db_WhiteIp',
    );

    public function isInList($ip = NULL)
    {
        if ($ip === NULL) {
            $ip = $this->getCurrentIp();
        }
        $result = $this->getMapper()->ipExists($ip);
        return $result;
    }

    public function getCurrentIp()
    {
        return isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'];
    }

}


<?php

require_once 'App/Model/Mapper/Interface.php';

interface Model_Mapper_Site extends Model_Mapper_Interface
{

    /**
     * @param string $host
     * @param string $base
     * @return Model_Object_Interface
     */
    public function fetchOneByHost($host, $base = NULL);

}
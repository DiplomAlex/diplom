<?php

interface Model_Mapper_Config_Interface extends Model_Mapper_Interface
{
    public function makeSimpleObject(Zend_Config $conf);
    public function makeSimpleCollection(Zend_Config $conf);
}
<?php

require_once 'App/Model/Mapper/Interface.php';

interface Model_Mapper_XML_Interface extends Model_Mapper_Interface
{

    /**
     * @param mixed string|array|SimpleXMLElement
     * @return Model_Collection_Interface
     */
    public function makeSimpleCollection($xml);

}
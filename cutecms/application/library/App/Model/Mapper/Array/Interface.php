<?php

interface Model_Mapper_Array_Interface extends Model_Mapper_Interface
{


    public function makeSimpleObject(array $values);

    public function makeSimpleCollection(array $values);

    public function makeCustomObject(array $values);

    public function makeCustomCollection(array $values);

    public function makeComplexObject(array $values);

    public function makeComplexCollection(array $values);

}
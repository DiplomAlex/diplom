<?php

class Model_Object_Package extends Model_Object_Abstract
{

    public function init()
    {
        $this->addElements(array(
            'name',
            'title',
            'version',
            'installed',
            'enabled',
            'files',
            'events',
            'routes',
            'acl',
        ));
    }

}
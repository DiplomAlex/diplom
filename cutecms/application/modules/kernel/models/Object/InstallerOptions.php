<?php

class Model_Object_InstallerOptions extends Model_Object_Abstract
{

    public function init()
    {
        $this->addElements(array(
            'db_adapter',
            'db_host',
            'db_name',
            'db_user',
            'db_password',
            'db_import_dump',
            'host',
            'base_url',
            'site_name',
            'support_email',
            'support_name'
        ));
    }

}
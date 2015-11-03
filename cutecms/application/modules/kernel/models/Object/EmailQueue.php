<?php

class Model_Object_EmailQueue extends Model_Object_Abstract
{

    public function init()
    {

        $this -> addElements(array(
                                'id',
                                'to',
                                'to_name',
                                'from',
                                'from_name',
                                'subject',
                                'body_text',
                                'body_html',
                                'date_added',
                            ));

    }


}
<?php

class Social_Model_Object_Mail extends Model_Object_Abstract
{

    public function init()
    {
        $this -> addElements(array(
            'id',
            'status',
            'subject',
            'body',
            'date_sent',
            'sender_id',
            'sender_login',
            'sender_name',
            'recipient_id',
            'recipient_login',
            'recipient_name',
            'talking',
            'parent_id',
            'talking_subject',
        ));
    }

}
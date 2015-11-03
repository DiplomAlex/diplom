<?php

class Model_Object_UserHistory extends Model_Object_Abstract
{

    public function init()
    {
        $this->addElements(array(
            'id',
            'event',
            'user_id',
            'user_status',
            'user_sort',
            'user_export',
            'user_login',
            'user_binding',
            'user_password',
            'user_email',
            'user_dob',
            'user_rc_id',
            'user_role_id',
            'user_name',
            'user_last_login',
            'user_login_count',
            'user_date_added',
            'user_date_changed',
            'user_adder_id',
            'user_changer_id',
            'user_rows_per_page',
            'user_binded_count',
            'user_tel',
            'user_address',
            'user_where_know',
            'user_firstname',
            'user_fathersname',
            'user_lastname',
            'user_bonus_account',
            'user_comment',
            'user_guid',
        ));
    }

}
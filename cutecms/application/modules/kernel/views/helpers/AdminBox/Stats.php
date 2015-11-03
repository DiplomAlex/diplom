<?php

class View_Helper_AdminBox_Stats extends Zend_View_Helper_Abstract
{

    public function adminBox_Stats()
    {
        if ($user = Model_Service::factory('user')->getCurrent()) {
            $arr['loggedIn'] = TRUE;
            $arr['showClientStats'] = ($user->acl_role == 'client');
            if ($arr['showClientStats']) {
                $arr['msgQty'] = App_Event::factory('Social_Model_Service_Mail__getNewMailsCount')->dispatch()->getResponse();
                $discountData = unserialize($user->discount_fromform_data);
                $arr['TAQty'] =  (int) @$discountData['automats_qty_gum']+ (int) @$discountData['automats_qty_toys']+ (int) @$discountData['automats_qty_gaiters'];
                $arr['debt'] = 0;
            }
            else {
                $dirs = Model_Service::factory('config')->read('var/shipment_direction.xml');
                $day = strtolower(date('l'));
                if (count($dirs->{$day})>1) {
                    $direction = '';
                    foreach ($dirs->{$day} as $dir) {
                        $direction .='&nbsp;'. $dir;
                    }
                }
                else {
                    $direction = $dirs->{$day};
                }
                $arr['direction'] = $direction;
                $arr['shipmentsCount'] = App_Event::factory('Tickets_Model_Service_Document__countShipmentsOfToday')->dispatch()->getResponse();
                $arr['avgShipmentDays'] = App_Event::factory('Tickets_Model_Service_Document__getAvgInterval')->dispatch()->getResponse();
            }
        }
        else {
            $arr['loggedIn'] = FALSE;
        }
        $result = $this->view->partial('admin-box/stats.phtml', $arr);
        return $result;
    }

}

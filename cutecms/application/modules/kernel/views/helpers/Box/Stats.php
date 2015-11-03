<?php

class View_Helper_Box_Stats extends Zend_View_Helper_Abstract
{

    public function box_Stats()
    {
        /**
         * stats box is visible only for logged in users
         */
        if ($user = Model_Service::factory('user')->getCurrent()) {
            $arr['loggedIn'] = TRUE;
            $arr['showClientStats'] = ($user->acl_role == 'client');
            $arr['showManagerStats'] = ($user->acl_role == 'manager');
            /**
             * for clients - their own box
             */
            if ($arr['showClientStats']) {
                $arr['msgQty'] = App_Event::factory('Social_Model_Service_Mail__getNewMailsCount')->dispatch()->getData();
                $discountData = unserialize($user->discount_fromform_data);
                $arr['TAQty'] =  (int) @$discountData['automats_qty_gum']+ (int) @$discountData['automats_qty_toys']+ (int) @$discountData['automats_qty_gaiters'];
                $arr['debt'] = 0;
            }
            /**
             * separate box for coworkers
             */
            else {
                $day = strtolower(date('l'));
                /*
                $dirs = Model_Service::factory('config')->read('var/shipment_direction.xml');
                if (count($dirs->{$day})>1) {
                    $direction = '';
                    foreach ($dirs->{$day} as $dir) {
                        $direction .='&nbsp;'. $dir;
                    }
                }
                else {
                    $direction = $dirs->{$day};
                }
                $direction = trim($direction);
                if (empty($direction)) {
                    $direction = $this->view->translate('Направление не определено');
                }
                */
                $sched = App_Event::factory('Tickets_Model_Service_ShipmentSchedule__getAllGroupped', array($this))->dispatch()->getData();
                if ( ! isset($sched[$day]['is_in_box'])) {
                    $direction = $this->view->translate('Направление не определено');
                }
                else {
                    $list = array();
                    foreach ($sched[$day]['is_in_box'] as $ss) {
                        $list[] = $ss->transport_name . ' ' . $ss->direction;
                    }
                    $direction = implode(', ', $list);
                }
                $arr['direction'] = $direction;
                $arr['shipmentsCount'] = App_Event::factory('Tickets_Model_Service_Document__countShipmentsOfToday')->dispatch()->getData();
                $arr['avgShipmentDays'] = App_Event::factory('Tickets_Model_Service_Document__getAvgInterval')->dispatch()->getData();
                /**
                 * for managers
                 */
                if ($arr['showManagerStats']) {
                    $arr['msgQty'] = App_Event::factory('Social_Model_Service_Mail__getNewMailsCount')->dispatch()->getData();
                }
            }
        }
        else {
            $arr['loggedIn'] = FALSE;
        }
        $result = $this->view->partial('box/stats.phtml', $arr);
        return $result;
    }

}

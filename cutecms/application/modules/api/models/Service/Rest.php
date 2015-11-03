<?php

class Api_Model_Service_Rest
{

    /**
     * Получить остатки
     *
     * @return array
     */
    public function getRemains()
    {
        $answer = Model_Service::factory('api/remain')->getAllRemainsArray();

        if (empty($answer)) {
            $return = array(null);
        } else {
            $return = array('remain' => $answer);
        }

        return array('remains' => $return);
    }

    /**
     * Выгрузка остатков
     *
     * @param SimpleXMLElement $params
     * @return array
     */
    public function setRemains(SimpleXMLElement $params)
    {
        $updated = 0;

        if (isset($params->remains->remain)) {
            $updated = Model_Service::factory('api/remain')->setRemains($params->remains);
        }
        $message = $updated ? 'setRemains success' : 'No remains changed.';

        return array('message' => $message);
    }

    /**
     * Получить пользователей
     *
     * @return array
     */
    public function getUsers()
    {
        $answer = Model_Service::factory('user')->getAllUsersExport();

        if (empty($answer)) {
            $return = array(null);
        } else {
            $return = array('user' => $answer);
        }

        return array('users' => $return);
    }

    /**
     * Выгрузка пользователей
     *
     * @param SimpleXMLElement $params
     * @return array
     */
    public function setUsers(SimpleXMLElement $params)
    {
        $updated = 0;

        if (isset($params->users->user)) {
            $updated = Model_Service::factory('user')->setUsers($params->users);
        }
        $message = $updated ? 'setUsers success' : 'No users changed.';

        return array('message' => $message);
    }

    /**
     * Выгрузка заказов
     *
     * @param SimpleXMLElement $params
     * @return array
     */
    public function setOrders(SimpleXMLElement $params)
    {
        /** @var $orderService Checkout_Model_Service_Order */
        $orderService = Model_Service::factory('checkout/order');
        $updated = 0;
        if (isset($params->orders->order)) {
            $updated = $orderService->setOrders($params->orders);
        }
        $message = $updated ? 'writeOrders success' : 'No orders changed.';

        return array('message' => $message);
    }

    /**
     * Получить заказы
     *
     * @return array
     */
    public function getOrders()
    {
        $answer = Model_Service::factory('checkout/order')->getAllOrdersExport();

        if (empty($answer)) {
            $return = array(null);
        } else {
            $return = array('order' => $answer);
        }

        return array('orders' => $return);
    }
}
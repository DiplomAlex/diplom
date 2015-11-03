<?php

class Model_Service_UserHistory extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
            'Model_Object_Interface' => 'Model_Object_UserHistory',
            'Model_Mapper_Interface' => 'Model_Mapper_Db_UserHistory',
        );

    /**
     * История изменений
     *
     * @param $id
     * @return array
     */
    public function getHistoryByUserId($id)
    {
        $userService = Model_Service::factory('user');

        $data = $this->getMapper()->fetchHistoryByUserId($id);

        $history = array();
        foreach ($data as $key => $value) {
            $changedFlag = false;
            $changes = array();

            if ($key == 0) {
                $prevRow = $userService->getComplex($id)->toArray();
                $prevRow['uh_user_id'] = $prevRow['id'];
                unset($prevRow['id']);
            } else {
                $prevRow = $data[$key - 1];
            }

            foreach ($prevRow as $prevKey => $prevValues) {
                if ($prevKey[1] != 'h' || $prevKey == 'changer_id') {
                    $prevRow['uh_user_' . $prevKey] = $prevValues;
                    unset($prevRow[$prevKey]);
                }
            }

            if ($value['uh_user_status'] != $prevRow['uh_user_status']) {
                $changes['Статус'] = array('from' => $value['uh_user_status'], 'to' => $prevRow['uh_user_status']);
                $changedFlag = true;
            }

            if ($value['uh_user_login'] != $prevRow['uh_user_login']) {
                $changes['Логин'] = array('from' => $value['uh_user_login'], 'to' => $prevRow['uh_user_login']);
                $changedFlag = true;
            }

            if ($value['uh_user_email'] != $prevRow['uh_user_email']) {
                $changes['Е-mail'] = array('from' => $value['uh_user_email'], 'to' => $prevRow['uh_user_email']);
                $changedFlag = true;
            }

            if ($value['uh_user_role_id'] != $prevRow['uh_user_role_id']) {
                $changes['Роль'] = array('from' => $value['uh_user_role_id'], 'to' => $prevRow['uh_user_role_id']);
                $changedFlag = true;
            }

            if ($value['uh_user_name'] != $prevRow['uh_user_name']) {
                $changes['Имя'] = array('from' => $value['uh_user_name'], 'to' => $prevRow['uh_user_name']);
                $changedFlag = true;
            }

            if ($value['uh_user_address'] != $prevRow['uh_user_address']) {
                $changes['Адрес'] = array('from' => $value['uh_user_address'], 'to' => $prevRow['uh_user_address']);
                $changedFlag = true;
            }

            if ($value['uh_user_tel'] != $prevRow['uh_user_tel']) {
                $changes['Телефон'] = array('from' => $value['uh_user_tel'], 'to' => $prevRow['uh_user_tel']);
                $changedFlag = true;
            }

            if ($value['uh_user_dob'] != $prevRow['uh_user_dob']) {
                $changes['Дата рождения'] = array('from' => $value['uh_user_dob'], 'to' => $prevRow['uh_user_dob']);
                $changedFlag = true;
            }

            if ($value['uh_user_bonus_account'] != $prevRow['uh_user_bonus_account']) {
                $changes['Бонусный счет'] = array('from' => $value['uh_user_bonus_account'],
                                                  'to'   => $prevRow['uh_user_bonus_account']);
                $changedFlag = true;
            }

            if ($value['uh_user_comment'] != $prevRow['uh_user_comment']) {
                $changes['Примечание'] = array('from' => $value['uh_user_comment'],
                                               'to'   => $prevRow['uh_user_comment']);
                $changedFlag = true;
            }

            if ($changedFlag) {
                if ($prevRow['uh_user_changer_id'] != 0) {
                    $changer = $userService->getComplex($prevRow['uh_user_changer_id'])->name;
                } else {
                    $changer = '1C';
                }

                $history[] = array(
                    'date_changed' => $prevRow['uh_user_date_changed'],
                    'changer'      => $changer,
                    'changer_id'   => $prevRow['uh_user_changer_id'],
                    'changed'      => $changes,
                );
            }
        }

        return $history;
    }
}
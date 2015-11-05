<?php

class Model_Object_Observer_Log extends App_Event_Observer
{


    /**
     * update date_added, date_changed, adder_id, changer_id
     * @param Model_Object_Interface
     * @return $this
     */
    public function onBeforeSave()
    {
        $data = $this->getEvent()->getData();
        $object = $data[0];
        $user = Zend_Auth::getInstance()->getIdentity();

        if ($object->id) {
            if ($object->hasElement('date_changed')) {
                $object->date_changed = date('Y-m-d H:i:s');
            }
            if ($object->hasElement('changer_id')) {
                $object->changer_id = @$user->id;
            }
        }
        else {
            if ($object->hasElement('date_added')) {
                $object->date_added = date('Y-m-d H:i:s');
            }
            if ($object->hasElement('adder_id')) {
                $object->adder_id = @$user->id;
            }
        }
        return $object;
    }



}
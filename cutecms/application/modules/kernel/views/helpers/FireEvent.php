<?php

class View_Helper_FireEvent extends Zend_View_Helper_Abstract
{

    /**
     * @param string
     * @param array
     */
    public function fireEvent($eventName, array $params = NULL)
    {
        return App_Event::factory($eventName, $params)->dispatch()->getResponse();
    }

}
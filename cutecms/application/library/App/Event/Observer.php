<?php

class App_Event_Observer
{

    protected $_event;
    protected $_eventName;
    protected $_name;
    protected $_callback;

    /**
     * Checkes the observer's event_regex against event's name
     *
     * @param App_Event $event
     * @return boolean
     */
    public function isValidFor(App_Event $event)
    {
        return (($this->getEventName()===$event->getName()) AND ( ! $event->isFired()));
    }

    /**
     * Dispatches an event to observer's callback
     *
     * @param App_Event $event
     * @return App_Event_Observer
     */
    public function dispatch(App_Event $event)
    {
        if (!$this->isValidFor($event)) {
            return $this;
        }

        $callback = $this->getCallback();
        $this->setEvent($event);
        $_profilerKey = 'OBSERVER: '.(is_object($callback[0]) ? get_class($callback[0]) : (string)$callback[0]).' -> '.$callback[1];
        App_Profiler::start($_profilerKey);
        call_user_func($callback, $this);
        App_Profiler::stop($_profilerKey);

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param string
     * @return $this
     */
    public function setName($data)
    {
        $this->_name = $data;
        return $this;
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return $this->_eventName;
    }

    /**
     * @param string
     * @return $this
     */
    public function setEventName($data)
    {
        $this->_eventName = $data;
        return $this;
    }

    /**
     * @return array(className, methodName)
     */
    public function getCallback()
    {
        return $this->_callback;
    }

    /**
     * @param array(className, methodName)
     * @return $this
     */
    public function setCallback($data)
    {
        $this->_callback = $data;
        return $this;
    }

    /**
     * Get observer event object
     *
     * @return App_Event
     */
    public function getEvent()
    {
        return $this->_event;
    }

    /**
     * @param App_Event
     * @return $this
     */
    public function setEvent($data)
    {
        $this->_event = $data;
        return $this;
    }


    /**
     * gets data of event - all or just one element
     * @param int
     * @return mixed
     */
    public function getData($key = NULL)
    {
        $data = $this->getEvent()->getData();
        if ($key !== NULL) {
            $data = @$data[$key];
        }
        return $data;
    }
}
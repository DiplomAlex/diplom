<?php

class App_Event_Observer_Collection
{
    /**
     * Array of observers
     *
     * @var array
     */
    protected $_observers;

    /**
     * Initializes observers
     *
     */
    public function __construct()
    {
        $this->_observers = array();
    }

    /**
     * Returns all observers in the collection
     *
     * @return array
     */
    public function getAllObservers()
    {
        return $this->_observers;
    }

    /**
     * Returns observer by its name
     *
     * @param string $observerName
     * @return App_Event_Observer
     */
    public function getObserverByName($observerName)
    {
        return $this->_observers[$observerName];
    }

    /**
     * Adds an observer to the collection
     *
     * @param App_Event_Observer $observer
     * @return App_Event_Observer_Collection
     */
    public function addObserver(App_Event_Observer $observer)
    {
        $this->_observers[$observer->getName()] = $observer;
        return $this;
    }

    /**
     * Removes an observer from the collection by its name
     *
     * @param string $observerName
     * @return App_Event_Observer_Collection
     */
    public function removeObserverByName($observerName)
    {
        unset($this->_observers[$observerName]);
        return $this;
    }

    /**
     * Dispatches an event to all observers in the collection
     *
     * @param App_Event $event
     * @return App_Event_Observer_Collection
     */
    public function dispatch(App_Event $event)
    {
        foreach ($this->_observers as $observer) {
            $observer->dispatch($event);
        }
        return $this;
    }
}
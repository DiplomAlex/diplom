<?php

class App_Event
{
    /**
     * Observers collection
     *
     * @var App_Event_Observer_Collection
     */
    protected $_observers;


    /**
     * Event name
     *
     * @var string
     */
    protected $_name;

    /**
     * Event data
     *
     * @var mixed
     */
    protected $_data;


    /**
     * responses from observers
     */
    protected $_response;


    /**
     * Event fired shold not be dispatched any moe
     *
     * @var bool
     */
     protected $_fired = FALSE;


    /**
     * factory:
     *     creates event and adds all necessary observers to it
     *
     * @param string
     * @param array parameters to observers callback
     * @return App_Event
     */
    public static function factory($eventName, array $params = NULL)
    {
        $event = new self($eventName, $params);
        if (Zend_Registry::isRegistered('events')) {
            $allEvents = Zend_Registry::get('events');
            if (Zend_Controller_Front::getInstance()->getRequest()) {
                $moduleName = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
            }
            else {
                $moduleName = NULL;
            }
            $registryKey = 'events_'.$moduleName;
            if (( ! empty($moduleName)) AND ($moduleName != Zend_Controller_Front::getInstance()->getDefaultModule()) AND (Zend_Registry::isRegistered($registryKey))) {
                foreach (Zend_Registry::get($registryKey) as $name => $observers) {
                    $allEvents[$name] = $observers;
                }
            }
            if (isset($allEvents[$eventName])) foreach ($allEvents[$eventName] as $observerName=>$observer) {
                if ( ! $observer->class) {
                    throw new App_Event_Exception('check the events.xml format for event "'.$eventName.'"');
                }
                $obObj = new $observer->class;
                $obObj->setCallback(array($obObj, $observer->method));
                $obObj->setName($observerName);
                $event->addObserver($obObj);
            }
        }
        return $event;
    }





    /**
     * Constructor
     *
     * Initializes observers collection
     *
     * @param array $data
     */
    public function __construct($name, $data=NULL)
    {
        $this->_observers = new App_Event_Observer_Collection();
        $this->setData($data);
    }


    public function __destruct()
    {
        $this->_observers = NULL;
        $this->_data = NULL;
        $this->_name = NULL;
        $this->_fired = NULL;
    }

    /**
     * Returns all the registered observers for the event
     *
     * @return App_Event_Observer_Collection
     */
    public function getObservers()
    {
        return $this->_observers;
    }

    /**
     * Register an observer for the event
     *
     * @param App_Event_Observer $observer
     * @return App_Event
     */
    public function addObserver(App_Event_Observer $observer)
    {
        $this->getObservers()->addObserver($observer);
        return $this;
    }

    /**
     * Removes an observer by its name
     *
     * @param string $observerName
     * @return App_Event
     */
    public function removeObserverByName($observerName)
    {
        $this->getObservers()->removeObserverByName($observerName);
        return $this;
    }

    /**
     * Dispatches the event to registered observers
     *
     * @return App_Event
     */
    public function dispatch()
    {
        if ( ! $this->isFired()) {
            $this->getObservers()->dispatch($this);
        }
        return $this;
    }

    /**
     * Retrieve event name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set event name
     *
     * @param string
     * @return $this
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Set event data
     *
     * @param mixed
     * @return $this
     */
    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }


    /**
     * set fired status
     * @param bool
     * @return $this
     */
    public function setFired($fired = TRUE)
    {
        $this->_fired = (bool) $fired;
        return $this;
    }

    /**
     * get fired status
     * @return bool
     */
    public function isFired()
    {
        return $this->_fired;
    }


    /**
     * returns all response or one by index
     * @param mixed - index of _response array
     * @return mixed
     */
    public function getResponse($index = NULL)
    {
        if (($index !== NULL) AND ( ! is_bool($index))) {
            $result = @$this->_response[$index];
        }
        else if (is_array($this->_response) AND (count($this->_response) == 1) AND array_key_exists(0, $this->_response)) {
            $result = $this->_response[0];
        }
        else if (is_array($this->_response) AND ($index === TRUE)) {
            $result = implode('', $this->_response);
        }
        else {
            $result = $this->_response;
        }
        return $result;
    }

    /**
     * sets the resonse value or value of response element by index
     * @param mixed - if only one param - this is the value of all _response
     * @param mixed - if this param present - then it is the value and the first param is the index
     * @return $this
     */
    public function setResponse()
    {
        $args = func_get_args();
        $cnt = count($args);
        if ($cnt == 1) {
            $this->_response = $args[0];
        }
        else if ($cnt == 2) {
            $this->_response[$args[0]] = $args[1];
        }
        else {
            throw new App_Event_Exception('App_Event::setResponse can recieve 1 or 2 parameters only');
        }
        return $this;
    }

    /**
     * adds response to array of responses
     * @param mixed
     * @return $this
     */
    public function addResponse($resp)
    {
        if ($this->_response === NULL) {
            $this->_response = array();
        }
        else if ( ! is_array($this->_response)) {
            $this->_response = array($this->_response);
        }
        $this->_response[]=$resp;
        return $this;
    }


}
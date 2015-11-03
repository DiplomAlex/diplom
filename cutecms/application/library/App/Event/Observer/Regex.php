<?php

class App_Event_Observer_Regex extends App_Event_Observer
{
    /**
     * Checkes the observer's event_regex against event's name
     *
     * @param App_Event $event
     * @return boolean
     */
    public function isValidFor(App_Event $event)
    {
        return preg_match($this->getEventRegex(), $event->getName());
    }
}
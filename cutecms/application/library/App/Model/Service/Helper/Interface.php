<?php

interface Model_Service_Helper_Interface
{

    /**
     * bind service to helper
     * @param Model_Service_Interface
     * @return Model_Service_Helper_Interface $this
     */
    public function setService(Model_Service_Interface $service);

    /**
     * returns binded service
     * @return Model_Service_Interface
     */
    public function getService();
    
    /**
     * sets options of helper 
     * @param array $options
     */
    public function setOptions(array $options);
    
    /**
     * gets single option of the helper
     * @param $option
     * @return mixed
     */
    //public function getOption($option);


}
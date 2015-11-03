<?php

class Observer_Bootstrap extends App_Event_Observer
{

    /**
     * prepared to be called from Bootstrap->run()
     */
    public function beforeRun()
    {
        $bootstrap = $this->getData(0);
        App_Profiler::start('Bootstrap::run');

        $mailTrClass = Zend_Registry::get('config')->email->transportClass;
        if ( ! empty($mailTrClass)) {
            App_Mail::setDefaultTransport(new $mailTrClass);
        }

        if (Zend_Registry::get('config')->default->db->useTransactions > 0) {
            Zend_Db_Table::getDefaultAdapter()->beginTransaction();
        }
    }

    /**
     * prepared to be called from Bootstrap->run()
     */
    public function afterRun()
    {
        if (Zend_Registry::get('config')->default->db->useTransactions > 0) {
            Zend_Db_Table::getDefaultAdapter()->commit();
        }
        App_Profiler::stop('Bootstrap::run');

        /*if ((APPLICATION_ENV != 'production') AND ( ! Zend_Controller_Front::getInstance()->getRequest()->isXmlHttpRequest())) {
            echo '<div style="float:left"><pre>'
                    . 'Total execution time: ' . (App_Profiler::fetch('Bootstrap::run') + App_Profiler::fetch('Bootstrap::init'))
                    . "\r\n"
                    . 'Memory usage:' . memory_get_usage() . '('.memory_get_usage(TRUE).')'
                    . "\r\n"
                    ;
            //Zend_Debug::dump(App_Profiler::getTimersSlice(array('sum', 'count')));
            echo '</pre></div>';

        }*/


    }

}
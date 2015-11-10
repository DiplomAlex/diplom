<?php

class Lab_Labs_ArduinoController extends Zend_Controller_Action
{
    public function init()
    {
        App_Event::factory('Lab_Controller__init', array($this))->dispatch();
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->view->layout()->disableLayout();
            $this->getHelper('ViewRenderer')->setNoRender();
        }
        $this->view->headTitle('Lab Arduino');
    }

    public function lab1Action()
    {

    }

    public function ajaxUploadSketchAction()
    {
        $user = Model_Service::factory('user')->getCurrent();

        $data = array();
        if (isset($_GET['files'])) {
            $error = false;

            try {
                foreach ($_FILES as $file) {
                    $sketch = file_get_contents($file['tmp_name']);
                    if (strpos($sketch, '#include <arduino.h>') === false) {
                        $sketch = "#include <arduino.h>
                                        " . $sketch;
                    }
                    $result = Model_Service::factory('arduino')->saveFromValues(array(
                        'adder_id' => $user->id,
                        'date_added' => date('Y-m-d H:i:s'),
                        'sketch' => $sketch,
                    ), true);
                }
            } catch (Exception $e) {
                $error = true;
            }

            if ($error) {
                $data = array('success' => false, 'error' => $e->getMessage());
            } else {
                $data = array('success' => true, 'id' => $result->id);
            }
        } else {
            $data = array('success' => false, 'error' => 'No files');
        }

        $this->_helper->json($data);
    }

    public function ajaxAddTextSketchAction()
    {
        $user = Model_Service::factory('user')->getCurrent();

        try {
            $sketch = $this->_getParam('sketch');
            if (strpos($sketch, '#include <arduino.h>') === false) {
                $sketch = "#include <arduino.h>
                        " . $sketch;
            }
            $result = Model_Service::factory('arduino')->saveFromValues(array(
                'adder_id' => $user->id,
                'date_added' => date('Y-m-d H:i:s'),
                'sketch' => $sketch,
            ), true);

            $data = array('success' => true, 'id' => $result->id);
        } catch(Exception $e) {
            $data = array('success' => false, 'error' => $e->getMessage());
        }

        $this->_helper->json($data);
    }

    public function runSketchAction()
    {
        set_time_limit (0);
        $id = $this->_getParam('id');

        if (isset($id)) {
            $data = Model_Service::factory('arduino')->getComplex($id);
            $file = str_replace('\\', '/', APPLICATION_PUBLIC) . '/uploads/arduino/';

            if (file_put_contents($file . 'Lab1.cpp', $data->sketch)) {
                $this->view->console = $this->syscall('make -C ' . $file . ' upload');
                $this->syscall('make -C ' . $file . ' clean');
//                $this->syscall('C:\vlc\vlc.exe -I dummy screen:// :screen-fps=16.000000 :screen-caching=100 :sout=#transcode{vcodec=theo,vb=800,scale=1,width=600,height=480,acodec=mp3}:http{mux=ogg,dst=127.0.0.1:8080/desktop.ogg} :no-sout-rtp-sap :no-sout-standard-sap :ttl=1 :sout-keep');
                ser_open("COM3", 115200, 8, "None", "1", "None");
                $this->view->data = $data;
            }
        } else {
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->url(array(), 'lab-arduino-lab1'));
        }
    }

    protected function syscall($command)
    {
        $result = '';
        $proc = popen("($command)2>&1", "r");
        while (!feof($proc))
            $result .= fgets($proc);

        pclose($proc);

        return $result;
    }

    public function ajaxSendToSerialAction()
    {
        if (!ser_isopen()) {
            ser_open("COM3", 115200, 8, "None", "1", "None");
        }

        ser_write($this->_getParam('write'));
        sleep(2);
        $str = ser_read();
        ser_flush(true, true);
        $this->_helper->json(array('success' => true, 'str' => str_replace("192\r\n", '', $str)));
    }

    public function ajaxCloseSerialAction()
    {
        if (ser_isopen()) {
            ser_close();
        }
        return;
    }
}

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
        ser_open("COM3", 115200, 8, "None", "1", "None");
        if (ser_isopen()) {
            ser_write($this->_getParam('write'));
            $str = ser_read();
            ser_flush(true, true);
            ser_close();
            $this->_helper->json(array('success' => true, 'str' => $str));
        } else {
            $this->_helper->json(array('success' => false, 'error' => 'Port not open'));
        }
    }
}

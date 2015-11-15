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

                    $time = Model_Service::factory('arduino-line')->saveAndGetTime($result->id);
                }
            } catch (Exception $e) {
                $error = true;
            }

            if ($error) {
                $data = array('success' => false, 'error' => $e->getMessage());
            } else {
                $data = array('success' => true, 'id' => $result->id, 'time' => $time);
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

            $time = Model_Service::factory('arduino-line')->saveAndGetTime($result->id);

            $data = array('success' => true, 'id' => $result->id, 'time' => $time);
        } catch(Exception $e) {
            $data = array('success' => false, 'error' => $e->getMessage());
        }

        $this->_helper->json($data);
    }

    public function runSketchAction()
    {
        set_time_limit (0);
        $id = $this->_getParam('id');
        $currentInLine= true;
        $line = Model_Service::factory('arduino-line')->getAll();

        if (!isset($id)){
            $this->getHelper('Redirector')->gotoUrlAndExit($this->view->url(array(), 'lab-arduino-lab1'));
        }

        if ($line->count()){
            $currentInLine = ($line->current()->sketch_id == $id);
            Model_Service::factory('arduino-line')->saveFromValues(array(
                'id' => $line->current()->id,
                'date_start' => date('Y-m-d H:i:s'),
            ));
        } else {
            Model_Service::factory('arduino-line')->saveFromValues(array(
                'sketch_id' => $id,
                'date_added' => date('Y-m-d H:i:s'),
                'date_start' => date('Y-m-d H:i:s'),
            ));
        }

        if ($currentInLine) {
            $data = Model_Service::factory('arduino')->getComplex($id);

            if (!$data->console) {
                $file = str_replace('\\', '/', APPLICATION_PUBLIC) . '/uploads/arduino/';

                if (file_put_contents($file . 'Lab1.cpp', $data->sketch)) {
                    $this->view->console = $this->syscall('make -C ' . $file . ' upload');
                    $this->syscall('make -C ' . $file . ' clean');
//                $this->syscall('C:\vlc\vlc.exe -I dummy screen:// :screen-fps=16.000000 :screen-caching=100 :sout=#transcode{vcodec=theo,vb=800,scale=1,width=600,height=480,acodec=mp3}:http{mux=ogg,dst=127.0.0.1:8080/desktop.ogg} :no-sout-rtp-sap :no-sout-standard-sap :ttl=1 :sout-keep');

                    Model_Service::factory('arduino')->saveFromValues(array(
                        'id' => $data->id,
                        'console' => $this->view->console,
                    ));
                    ser_open("COM3", 115200, 8, "None", "1", "None");
                } else {
                    $this->view->console = 'Ошибка открытия файла для записи';
                }
                $this->view->data = $data;
            } else {
                $this->getHelper('Redirector')->gotoUrlAndExit($this->view->url(array(), 'lab-arduino-lab1'));
            }
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

        if (ser_isopen()) {
            $write = $this->_getParam('write');
            $id = $this->_getParam('id');
            ser_write($write);
            sleep(2);
            $str = ser_read();
            ser_flush(true, true);

            Model_Service::factory('arduinoIO')->saveFromValues(array(
                'sketch_id' => $id,
                'in' => $write,
                'out' => str_replace("192\r\n", '', $str),
            ));
            $this->_helper->json(array('success' => true, 'str' => str_replace("192\r\n", '', $str)));
        } else {
            $this->_helper->json(array('success' => false, 'error' => 'Ошибка открытия порта'));
        }
    }

    public function ajaxCloseSerialAction()
    {
        if (ser_isopen()) {
            ser_close();
        }

        Model_Service::factory('arduino-line')->deleteBySketchId($this->_getParam('id', 0));
    }

    public function ajaxReloadAction()
    {
        $user = Model_Service::factory('user')->getCurrent();

        $sketch = $this->_getParam('sketch');
        if (strpos($sketch, '#include <arduino.h>') === false) {
            $sketch = "#include <arduino.h>
                        " . $sketch;
        }
        $file = str_replace('\\', '/', APPLICATION_PUBLIC) . '/uploads/arduino/';

        if (file_put_contents($file . 'Lab1.cpp', $sketch)) {
            $console = $this->syscall('make -C ' . $file . ' upload');
            $this->syscall('make -C ' . $file . ' clean');

            $data = Model_Service::factory('arduino')->saveFromValues(array(
                'adder_id' => $user->id,
                'date_added' => date('Y-m-d H:i:s'),
                'sketch' => $sketch,
                'console' => $console,
            ), true);

            $this->_helper->json(array('success' => true, 'console' => $console, 'sketch' => $sketch, 'id' => $data->id));
        } else {
            $this->_helper->json(array('success' => false, 'error' => 'Ошибка записи файла'));
        }
    }

    public function ajaxFreeLineAction()
    {
        Model_Service::factory('arduino-line')->deleteBySketchId($this->_getParam('id', 1));
    }

    public function ajaxCheckLineTopAction()
    {
        $rows = Model_Service::factory('arduino-line')->getAll();

        if ($rows->count()) {
            $this->_helper->json(array('success' => ($rows->current()->sketch_id == $this->_getParam('id'))));
        } else {
            $this->_helper->json(array('success' => true));
        }
    }

    public function ajaxGetTimeAction()
    {
        $time = Model_Service::factory('arduino-line')->getTimeWithoutCurrent($this->_getParam('id'));

        $this->_helper->json(array('time_correct' => $time));
    }
}

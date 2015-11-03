<?php

class Model_Service_EmailQueue extends Model_Service_Abstract
{


    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Model_Object_EmailQueue',
        'Model_Mapper_Interface' => 'Model_Mapper_Db_EmailQueue',
    );


    public function addToQueue(Model_Object_Interface $email)
    {
        $this->saveComplex($email);
    }

    public function sendTop()
    {
        $emails = $this->getMapper()->fetchTop(Zend_Registry::get('config')->email->sendQtyLimit);
        foreach ($emails as $email) {
            App_Mail::factory()
                    ->addTo($email->to, $email->to_name)
                    ->setFrom($email->from, $email->from_name)
                    ->setSubject($email->subject)
                    ->setBodyHtml($email->body_html)
                    ->setBodyText($email->body_text)
                    ->send();
            $this->delete($email);
        }
        return $emails->count();
    }

}

<?php

class Social_Model_Service_Mail extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Social_Model_Object_Mail',
        'Model_Mapper_Interface' => 'Social_Model_Mapper_Db_Mail',
    );

    /**
     * get mails collection in paginator
     * @param int rowsPerPage
     * @param int page
     * @param int sender id
     * @param int recipient id
     * @param int status
     * @param string talking id
     * @return Zend_Paginator
     */
    public function paginatorGetCorrespondence($rowsPerPage = NULL, $page = NULL, $sender = NULL, $recipient = NULL, $status = NULL, $talking = NULL)
    {
        if ($rowsPerPage === NULL) {
            $rowsPerPage = Zend_Registry::get('social_config')->default->paginator->rowsPerPage;
        }
        if ($page === NULL) {
            $page = Zend_Controller_Front::getInstance()->getRequest()->getParam('page');
        }
        return $this->getMapper()->paginatorFetchCorrespondence($rowsPerPage, $page, $sender, $recipient, $status, $talking);
    }


    /**
     * get mails collection
     * @param int rowsPerPage
     * @param int page
     * @param int sender id
     * @param int recipient id
     * @param int status
     * @param string talking id
     * @return Model_Collection_Interface
     */
    public function getCorrespondence($sender = NULL, $recipient = NULL, $status = NULL, $talking = NULL, $bothDirections = FALSE, $setReadStatus = TRUE)
    {
        $data = $this->getMapper()->fetchCorrespondence($sender, $recipient, $status, $talking, TRUE, $bothDirections);
        if ($setReadStatus === TRUE) {
            $this->getMapper()->updateCorrespondenceStatus(
                                   Zend_Registry::get('social_config')->mail->status->read,
                                   $status,
                                   $talking
                               );
        }
        return $data;
    }

    public function getNewMailsCount()
    {
        return $this->getMapper()->fetchNewMailsCount(NULL, Model_Service::factory('user')->getCurrent()->id);
    }

    /**
     * send message to recipient
     * @param mixed Model_Object_Interface | array
     */
    public function send($values)
    {
        $sender = Zend_Auth::getInstance()->getIdentity();
        if (is_array($values)) {

            $mail = $this->create();
            $mail->recipient_id = $values['recipient_id'];
            $mail->subject = $values['subject'];
            $mail->body = $values['body'];
            if (( ! isset($values['talking'])) OR ($values['talking'] === NULL)) {
                if ( ! empty($values['parent_id'])) {
                    try {
                        $talking = $this->get($values['parent_id'])->talking;
                    }
                    catch (Model_Exception $e) {
                        $this->_throwException('parentId is illegal - no such mail found');
                    }
                }
                else {
                    $talking = $this->_generateTalkingId();
                }
            }
            else {
                $talking = $values['talking'];
            }
            $mail->talking = $talking;
        }
        else if ($values instanceof Model_Object_Interface) {
            $mail = $values;
        }
        else {
            $this->_throwException('invalid values transmitted to send method');
        }
        $mail->sender_id = $sender->id;
        $mail->status = Zend_Registry::get('social_config')->mail->status->sent;
        $mail->date_sent = date('Y-m-d H:i:s');
        $this->save($mail);
    }

    protected function _generateTalkingId($senderId = NULL, $recipientId = NULL, $subject = NULL)
    {
        return md5(App_Uuid::get());
    }

    /**
     * get first message of talking
     */
    public function getFirstOfTalking($talking)
    {
        $this->getMapper()->fetchFirstOfTalking($talking);
    }

    /**
     * create correct mail object
     */
    public function newMessage($senderId, $recipientId, $subject, $talking, $parentId)
    {
        if ($parentId) {
            $parentMail = $this->get($parentId);
            $talking = $parentMail->talking;
            if ($senderId == $parentMail->sender_id) {
                $recipientId = $parentMail->recipient_id;
            }
            else {
                $recipientId = $parentMail->sender_id;
            }
            if ( ! $subject) {
                $subject = 'Re: '.$parentMail->subject;
            }
        }
        else if ($talking) {
            $parentMail = $this->getFirstOfTalking($talking);
            if ($senderId == $parentMail->sender_id) {
                $recipientId = $parentMail->recipient_id;
            }
            else {
                $recipientId = $parentMail->sender_id;
            }
            if ( ! $subject) {
                $subject = 'Re: '.$parentMail->subject;
            }
        }
        $newMail = $this->create();
        $newMail->sender_id = $senderId;
        $newMail->recipient_id = $recipientId;
        try {
            $newMail->recipient_name = Model_Service::factory('user')->get($recipientId)->name;
        }
        catch (Model_Exception $e) {
            $this->_throwException('illegal reciever id was transmitted to newMessage method');
        }
        $newMail->talking = $talking;
        $newMail->subject = $subject;
        $newMail->parent_id = $parentId;
        return $newMail;
    }



    /**
     * returns statuses list
     * @return array
     */
    public function getStatusesList()
    {
        return Zend_Registry::get('social_config')->mail->status;
    }

    /**
     * brule for recipients who allowed for currently logged in sender
     */
    public function getAllowedMessageRecipients()
    {
        $user = Zend_Auth::getInstance()->getIdentity();
        $rcps = array();
        /**
         * TODO : add data fetching
         */
        if ($user->acl_role == 'client') {
            /*1) add my manager*/
            /*2) add all with role 'director'*/
        }
        else if ($user->acl_role == 'manager') {
            /*1) add all coworkers except myself*/
            /*2) add my clients*/
        }
        else if ($user->acl_role == 'keeper') {
            /*1) add all coworkers*/
        }
        else if ($user->acl_role == 'director') {
            /*1) add all coworkers except myself*/
            /*2) add all clients*/
        }
    }

}
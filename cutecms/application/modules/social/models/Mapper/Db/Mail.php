<?php

class Social_Model_Mapper_Db_Mail extends Model_Mapper_Db_Abstract
{

    protected $_defaultInjections = array(
        'Model_Db_Table_Interface' => 'Social_Model_Db_Table_Mails',
        'Model_Object_Interface' => 'Social_Model_Object_Mail',
        'Model_Collection_Interface' => 'Social_Model_Collection_Mail',
    );


    protected function _onFetchComplex(Zend_Db_Select $select)
    {
        $user = Zend_Auth::getInstance()->getIdentity();
        $select->distinct(TRUE);
        $select->joinLeft(
                    array('recipient'=>'user'),
                    'recipient.user_id = mail.mail_recipient_id',
                    array('mail_recipient_login'=>'recipient.user_login', 'mail_recipient_name'=>'recipient.user_name')
                 )
               ->joinLeft(
                    array('sender'=>'user'),
                    'sender.user_id = mail.mail_sender_id',
                    array('mail_sender_login'=>'sender.user_login', 'mail_sender_name'=>'sender.user_name')
                 )
               ->joinLeft(
                    array('talk'=>'mail'),
                    'talk.mail_talking = mail.mail_talking AND ISNULL(talk.mail_parent_id)',
                    array('mail_talking_subject' => 'talk.mail_subject')
                 )
               ->where('mail.mail_sender_id = ? OR (mail.mail_recipient_id = ? AND mail.mail_status > 0)', array($user->id))
               ->group('mail.mail_id')
               ->order(array('talk.mail_date_sent DESC', 'mail_talking ASC', 'mail.mail_date_sent DESC'))
                 ;
        return $select;
    }

    public function paginatorFetchCorrespondence($rowsPerPage, $page, $sender = NULL, $recipient = NULL, $status = NULL, $talking = NULL)
    {
        return $this->paginator($this->fetchCorrespondence($sender, $recipient, $status, $talking, FALSE), $rowsPerPage, $page);
    }

    public function fetchCorrespondence($sender = NULL, $recipient = NULL, $status = NULL, $talking = NULL, $fetch = FALSE, $bothDirections = FALSE)
    {
        $where = array();
        if ($bothDirections === TRUE) {
            if ($sender !== NULL) {
                $where['mail.mail_sender_id = ? OR mail.mail_recipient_id = ?'] = $sender;
            }
            if ($recipient !== NULL) {
                $where['mail.mail_sender_id = ? OR mail.mail_recipient_id = ?'] = $recipient;
            }
        }
        else {
            if ($sender !== NULL) {
                $where['mail.mail_sender_id = ?'] = $sender;
            }
            if ($recipient !== NULL) {
                $where['mail.mail_recipient_id = ?'] = $recipient;
            }
        }
        if ($status !== NULL) {
            $where['mail.mail_status = ?'] = $status;
        }
        if ($talking !== NULL) {
            $where['mail.mail_talking = ?'] = $talking;
        }
        return $this->fetchComplex($where, $fetch);
    }


    public function updateCorrespondenceStatus($newStatus, $oldStatus = NULL, $talking = NULL)
    {
        $where = array();
        if ($oldStatus !== NULL) {
            $where['mail_status = ?'] = $oldStatus;
        }
        if ($talking !== NULL) {
            $where['mail_talking = ?'] = $talking;
        }
        $where['mail_recipient_id = ? AND mail_status > 0'] = Model_Service::factory('user')->getCurrent()->id;
        $this->getTable()->update(array('mail_status'=>$newStatus), $where);
        return $this;
    }



    public function fetchFirstOfTalking($talking, $sender = NULL)
    {
        $select = $this->fetchCorrespondence($sender, NULL, NULL, $talking, FALSE)
                       ->reset('order')->order('mail.mail_date_sent ASC')
                       ->limit(1);
        if ($data = $select->query()->fetchAll()) {
            $mail = $this->makeComplexObject(current($data));
        }
        else {
            $mail = NULL;
        }
        return $mail;
    }

    public function fetchNewMailsCount($senderId = NULL, $recipientId = NULL)
    {
        $select = $this->getTable()->select()->from('mail', array('cnt'=>'COUNT(mail_id)'))
                                             ->where('mail_status = 1');
        if ($senderId !== NULL) {
            $select->where('mail_sender_id = ?', $senderId);
        }
        if ($recipientId !== NULL) {
            $select->where('mail_recipient_id = ?', $recipientId);
        }
        $row = $select->query()->fetch();
        return $row['cnt'];
    }


}
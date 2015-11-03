<?php

class Model_Service_NewsTopic extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
    	'Model_Mapper_Interface' => 'Model_Mapper_Db_NewsTopic',
        'Model_Object_Interface' => 'Model_Object_NewsTopic',
        'Model_Service_Language',
        'Model_Service_Helper_Multisite',
    );


    /**
     * initializes object
     * @see Model_Service_Abstract::init()
     */
    public function init()
    {
        $lang = $this->getInjector()->getObject('Model_Service_Language');
        $this->getMapper()->getPlugin('Description')->setLanguages($lang->getAllActive())->setCurrentLanguage($lang->getCurrent());
        $this->addHelper('Multisite', $this->getInjector()->getObject('Model_Service_Helper_Multisite', $this));
    }


    /**
     * get objects fields values for edit form
     * @return array
     */
    public function getEditFormValues($id)
    {
        $obj = $this->getComplex($id);
        $values = $obj->toArray();
        $descs = $this->getMapper()->getPlugin('Description')->fetchDescriptions($id);
        $values = $values + $descs;
        return $values;
    }


    public function switchSubscriptionState($ntopicId, $userId)
    {
        if ($this->userIsSubscribed($ntopicId, $userId)) {
            $this->getMapper()->removeSubscription($ntopicId, $userId);
            return FALSE;
        }
        else {
            $this->getMapper()->addSubscription($ntopicId, $userId);
            return TRUE;
        }
    }

    public function userIsSubscribed($ntopicId, $userId)
    {
        return $this->getMapper()->userIsSubscribed($ntopicId, $userId);
    }


    public function subscribeUnsubscribeEmail($email)
    {
        if ($this->isSubscribedEmail($email)){
            $this->getMapper()->removeSubscribedEmail($email);
            return FALSE;
        } else {
            $this->getMapper()->addSubscribedEmail($email);
            return TRUE;
        }
    }
    
    public function unsubscribeUsersByEmail($email)
    {
        return $this->getMapper()->fetchUnsubscribeUsersByEmail($email);
    }

    public function isSubscribedEmail($email)
    {
        return $this->getMapper()->fetchIsSubscribedEmail($email);
    }

    public function getEmailSubscribersList()
    {
        return $this->getMapper()->fetchAllEmailSubscribers();
    }

    /**
     * get subscribers of topic discticted by email
     */
    public function getSubscribersList($ntopicId)
    {
        $coll = $this->getMapper()->fetchAllSubscribers($ntopicId);
        $list = array();
        foreach ($coll as $user) {
            $list[$user->email] = $user;
        }
        return $list;
    }
    
    /**
     * @return int
     */
    public function getTotalSubscribersCount()
    {
        return $this->getMapper()->fetchTotalSubscribersCount();
    }
    
     public function addSubscribeEmail($email)
    {
        $this->getMapper()->addSubscribedEmail($email);
        return TRUE;
    }
}
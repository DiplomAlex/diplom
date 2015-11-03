<?php

class Model_Mapper_Db_NewsTopic extends Model_Mapper_Db_Abstract
{

	protected $_defaultInjections = array(
		'Model_Db_Table_Interface' => 'Model_Db_Table_NewsTopics',
		'Model_Object_Interface' => 'Model_Object_NewsTopic',
        'Model_Collection_Interface' => 'Model_Collection_NewsTopic',

        'Model_Db_Table_Description' => 'Model_Db_Table_NewsTopicsDescription',
        'Model_Mapper_Db_Plugin_Description',
        'Model_Mapper_Db_Plugin_Sorting',
        'Model_Mapper_Db_User',

        'Model_Db_Table_UserNewsSubscription',
        'Model_Db_Table_UserNewsSubscriptionLog',
        'Model_Db_Table_Users',
	
        'Model_Mapper_Db_Plugin_Multisite' => 'Model_Mapper_Db_Plugin_Multisite_ManyToMany',
        'Model_Mapper_Db_Site',
        'Model_Db_Table_SiteRef' => 'Model_Db_Table_NewsTopicSiteRef',                        
	
	);



    public function init()
    {
        $this->addPlugin(
            'Description',
            $this ->getInjector()
                  ->getObject(
                    'Model_Mapper_Db_Plugin_Description',
                    array(
                        'mapper' => $this,
                        'table' => $this->getInjector()->getObject('Model_Db_Table_Description'),
                        'refColumn' => 'ntopic_id',
                        'descFields' => array(
                            'name', 'brief', 'full',
                        ),
                    )
                  )
        )
        ;
        // $this->addPlugin('Multisite', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Multisite', array(
            // 'siteMapper' => $this->getInjector()->getObject('Model_Mapper_Db_Site'),
            // 'refTable' => $this->getInjector()->getObject('Model_Db_Table_SiteRef'),
            // 'refEntityColumn' => 'ntopic_id',
        // )));                            
    }


    /**
     * make paginator for complex fetching
     * @param mixed array|string
     * @param int
     * @param int
     */
    public function paginatorFetchComplex($where, $rowsPerPage, $page)
    {
        $query = $this->fetchComplex($where, FALSE)->order('ntopic_sort ASC');

        return $this->paginator($query,  $rowsPerPage, $page, Model_Object_Interface::STYLE_COMPLEX);
    }


    public function userIsSubscribed($ntopicId, $userId)
    {
        $row = $this->getTable('user-news-subscription')->select()->from('user_news_subscription', array('uns_id'))
                                   ->where('uns_ntopic_id = ?', $ntopicId)
                                   ->where('uns_user_id = ?', $userId)
                                   ->query()->fetch();
        return (bool) $row['uns_id'];
    }

    public function addSubscription($ntopicId, $userId)
    {
        $row = $this->getTable('user-news-subscription')->insert(array(
            'uns_user_id' => $userId,
            'uns_ntopic_id' => $ntopicId,
        ));
        $row = $this->getTable('user-news-subscription-log')->insert(array(
            'log_user_id' => $userId,
            'log_ntopic_id' => $ntopicId,
            'log_action' => 'insert',
        ));
        
    }

    public function removeSubscription($ntopicId, $userId)
    {
        /*
        $row = $this->getTable('user-news-subscription')->delete(array(
            'uns_user_id' => $userId,
            'uns_ntopic_id' => $ntopicId,
        ));
        */
        
        $row = $this->getTable('user-news-subscription')->delete(
            'uns_user_id = '. (int) $userId.' AND uns_ntopic_id = '. (int) $ntopicId
        );
        
        $row = $this->getTable('user-news-subscription-log')->insert(array(
            'log_user_id' => $userId,
            'log_ntopic_id' => $ntopicId,
            'log_action' => 'delete',
        ));
    }


    public function fetchAllSubscribers($ntopicId)
    {
        $table  = $this->getTable('users');
        $select = $table->select()->from($table->info('name'), $table->info('cols'))
                                  ->setIntegrityCheck(FALSE)
                                  ->joinLeft(array('uns'=>'user_news_subscription'), 'uns_user_id = user_id', array())
                                  ->where('uns_ntopic_id = ?', $ntopicId)
                                  ;
        return $this->getMapper('user')->makeComplexCollection($select->query()->fetchAll());
    }
    
    public function fetchAllEmailSubscribers()
    {
        $this->db = Zend_Db_Table::getDefaultAdapter();
        return $this->db->query('SELECT * FROM email_news_subscription')->fetchAll();
    }

    public function fetchUnsubscribeUsersByEmail($email)
    {
        $this->db = Zend_Db_Table::getDefaultAdapter();
        $select = $this->db->select()
                                    ->from('email_news_subscription')
                                    ->where('ens_email = ?', $email);
        $emails = $this->db->query($select)->fetchAll();
        
        $select = $this->db->select()
                                    ->from('user_news_subscription')
                                    -> joinLeft(array('user'=>'user'),
                                                    'user.user_id = uns_user_id',
                                                    array('id'=>'user.user_id',
                                                          'login'=>'user.user_login',
                                                          'email'=>'user.user_email'))
                                    ->where('user.user_email = ?', $email);
        $users = $this->db->query($select)->fetchAll();
        
        if (count($emails)){
            foreach ($emails as $singleEmail){
                $where = $this->db->quoteInto('ens_email = ?', $singleEmail['ens_email']);
                $this->db->delete('email_news_subscription', $where);
            }
        }
        
        if (count($users)){
            foreach ($users as $singleUser){
                $where = $this->db->quoteInto('uns_user_id = ?', $singleUser['id']);
                $this->db->delete('user_news_subscription', $where);
            }
        }
    }
    
    public function fetchTotalSubscribersCount()
    {
        $table = $this->getTable('user-news-subscription');
        $select = $table->select()->from($table->info('name'), array('cnt'=>'COUNT(DISTINCT uns_user_id)'));
        $row = $select->query()->fetch();
        $result = $row['cnt'];
        return $result;
    }

    public function fetchIsSubscribedEmail($email)
    {
        $this->db = Zend_Db_Table::getDefaultAdapter();        
        $select = $this->db->select()
                                    ->from('email_news_subscription')
                                    ->where('ens_email = ?', $email);
        $email = $this->db->query($select)->fetchAll();        
        if (count($email)) {$result = true;} else {$result = false;};
        return $result;
    }
    
    public function addSubscribedEmail($email)
    {
        $this->db = Zend_Db_Table::getDefaultAdapter();
        $data = array();
    	$data['ens_email'] = $email;
        $this->db->insert('email_news_subscription', $data);
    }
    
    public function removeSubscribedEmail($email)
    {
        $this->db = Zend_Db_Table::getDefaultAdapter();
        $where = $this->db->quoteInto('ens_email = ?', $email);
        $this->db->delete('email_news_subscription', $where);
    }
}
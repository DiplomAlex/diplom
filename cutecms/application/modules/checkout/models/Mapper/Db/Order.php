<?php

class Checkout_Model_Mapper_Db_Order extends Model_Mapper_Db_Abstract
{

    protected $_defaultInjections = array(
        'Model_Db_Table_Interface'   => 'Checkout_Model_Db_Table_Order',
        'Model_Object_Interface'     => 'Checkout_Model_Object_Order',
        'Model_Collection_Interface' => 'Checkout_Model_Collection_Order',
        'Model_Mapper_Interface'     => 'Checkout_Model_Mapper_Db_Order',

        'Model_Mapper_Item'          => 'Checkout_Model_Mapper_XML_CartItem',
        'Model_Mapper_Brule'         => 'Checkout_Model_Mapper_XML_Brule',
        'Model_Mapper_Shipment'      => 'Checkout_Model_Mapper_XML_Shipment',
        'Model_Mapper_Payment'       => 'Checkout_Model_Mapper_XML_Payment',

        'Model_Mapper_Db_Plugin_Filter_Order' => 'Checkout_Model_Mapper_Db_Plugin_Filter_Order',
    
        'Model_Mapper_Db_Plugin_Multisite' => 'Model_Mapper_Db_Plugin_Multisite_OneToMany',
        'Model_Mapper_Db_Site',
    
    );


    public function init()
    {
        $this
            ->addPlugin('Filters', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Filter_Order',
                array(
                    'filter_number',
                    'filter_status',
                    'filter_client',
                )
            ));
        $this->addPlugin('Multisite', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Multisite', array(
            'siteMapper' => $this->getInjector()->getObject('Model_Mapper_Db_Site'),
        )));                    
            
    }

    protected function _onFetchComplex(Zend_Db_Select $select)
    {
        $select->joinLeft(
                    array('client' => 'user'),
                    'client.user_id = order_client_id',
                    array('order_client_login'=>'client.user_login', 
                    	  'order_client_name'=>'client.user_name', 
                          'order_client_email'=>'client.user_email',)
                 )
               ->order('order_date_added DESC');
        return $select;
    }


    protected function _onBuildComplexObject(Model_Object_Interface $obj, array $values = NULL, $addedPrefix = TRUE)
    {
        $obj->items     = $obj->items_xml;
        $obj->brules    = $obj->brules_xml;
        $obj->shipment  = $obj->shipment_xml;
        $obj->payment   = $obj->payment_xml;
        return $obj;
    }


    protected function _preSaveComplex(Model_Object_Interface $obj, array $values)
    {
        $obj->items_xml = $this->getInjector()->getObject('Model_Mapper_Item')->unmapCollectionToXML($obj->items);
        $obj->brules_xml = $this->getInjector()->getObject('Model_Mapper_Brule')->unmapCollectionToXML($obj->brules);
        $obj->shipment_xml = $this->getInjector()->getObject('Model_Mapper_Shipment')->unmapObjectToXML($obj->shipment);
        $obj->payment_xml = $this->getInjector()->getObject('Model_Mapper_Payment')->unmapObjectToXML($obj->payment);
        $obj->total = Model_Service::factory('checkout/order')->calculateTotal($obj);
        return $obj;
    }


    /**
     * make paginator for complex fetching
     * @param Model_Object_Interface client
     * @param int
     * @param int
     */
    public function paginatorFetchComplexByUser(Model_Object_Interface $user, $rowsPerPage, $page)
    {
        $query = $this->fetchComplex(NULL, FALSE)->where('order_client_id = ?', $user->id);
        return $this->paginator($query,  $rowsPerPage, $page, Model_Object_Interface::STYLE_COMPLEX);
    }

    public function updateStatus($id, $status)
    {
        $this->getTable()->update(
            array('order_status' => $status), array('order_id = (?)' => $id)
        );
    }

    public function getOrderById($id)
    {
        $row = $this->getTable()->fetchRow(array('order_id = (?)' => $id));

        if (!$row) {
            throw new Exception("Нет записи с id - $id");
        }

        return $row;
    }
    
    public function fetchAllByClient($uid)
    {
        $select = $this->fetchComplex(NULL, FALSE)->where('order_client_id = ?', $uid);

        return $this->makeComplexCollection($select->query()->fetchAll());
    }

    /**
     * Fetch complex by id
     *
     * @param $oid
     *
     * @return Model_Collection_Interface
     */
    public function fetchComplexOrderById($oid)
    {
        $select = $this->fetchComplex(NULL, FALSE)->where('order_id = ?', $oid);

        return $this->makeSimpleCollection($select->query()->fetchAll());
    }

    /**
     * Получить новые товары
     *
     * @param $startTime
     *
     * @return Checkout_Model_Collection_Order
     */
    public function fetchAllExport($startTime)
    {
        $select = $this->getTable()->select()
            ->where('order_export = ?', 1)
            ->where('order_date_changed < ? OR isnull(order_date_changed)', date('Y-m-d H:i:s', $startTime));

        return $this->makeComplexCollection($select->query()->fetchAll());
    }

    /**
     * Отметить выгруженые
     *
     * @param $startTime
     * @return $this
     */
    public function clearExport($startTime)
    {
        $this->getTable()->update(
            array('order_export' => '0'),
            array('order_export' => '1',
                  'order.order_date_changed < ? OR isnull(order.order_date_changed)' => date(
                      'Y-m-d H:i:s', $startTime
                  ))
        );

        return $this;
    }

    /**
     * Fetch order by guid
     *
     * @param $guid
     *
     * @return Checkout_Model_Object_Order
     */
    public function fetchOrderByGuid($guid)
    {
        $res = $this->getTable()->select()
            ->where('order_guid = ?', (string)$guid)
            ->query()
            ->fetch();

        unset($res['items']);

        if ($res) return $this->makeComplexObject($res);
        else return null;
    }

    /**
     * Set order guid
     *
     * @param $guid
     * @param $oid
     *
     * @return int
     */
    public function setOrderGuid($guid, $oid)
    {
        return $this->getTable()->update(array('order_guid' => $guid), array('order_id = ?' => $oid));
    }
}
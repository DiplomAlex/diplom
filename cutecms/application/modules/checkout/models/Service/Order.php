<?php

class Checkout_Model_Service_Order extends Model_Service_Abstract
{

    protected $_defaultInjections
        = array(
            'Model_Object_Interface'             => 'Checkout_Model_Object_Order',
            'Model_Collection_Interface'         => 'Checkout_Model_Collection_Order',
            'Model_Mapper_Interface'             => 'Checkout_Model_Mapper_Db_Order',
            'Model_Mapper_CartItem'              => 'Checkout_Model_Mapper_Db_CartItem',

            'Model_Mapper_Item'                  => 'Checkout_Model_Mapper_XML_CartItem',
            'Model_Mapper_Brule'                 => 'Checkout_Model_Mapper_XML_Brule',
            'Model_Mapper_Shipment'              => 'Checkout_Model_Mapper_XML_Shipment',
            'Model_Mapper_Payment'               => 'Checkout_Model_Mapper_XML_Payment',

            'Model_Mapper_Array_Shipment'        => 'Checkout_Model_Mapper_Array_Shipment',
            'Model_Mapper_Array_Payment'         => 'Checkout_Model_Mapper_Array_Payment',

            'Model_Object_Brule'                 => 'Checkout_Model_Object_Brule',
            'Model_Collection_Brule'             => 'Checkout_Model_Collection_Brule',

            'Model_Object_Shipment'              => 'Checkout_Model_Object_Shipment',
            'Model_Collection_Shipment'          => 'Checkout_Model_Collection_Shipment',
            'Model_Object_Payment'               => 'Checkout_Model_Object_Payment',
            'Model_Collection_Payment'           => 'Checkout_Model_Collection_Payment',

            'Model_Service_Helper_BruleTotal'    => 'Checkout_Model_Service_Helper_Brule_Total',
            'Model_Service_Helper_BruleShipment' => 'Checkout_Model_Service_Helper_Brule_Shipment',
            'Model_Service_Helper_BrulePayment'  => 'Checkout_Model_Service_Helper_Brule_Payment',
            'Checkout_Model_Collection_CartItem',
            'Checkout_Model_Object_CartItem',

            'Checkout_Model_Service_Helper_Brule_Total_TotalSumm',
            'Checkout_Model_Service_Helper_Brule_Total_DiscountBySumm',
            'Checkout_Model_Service_Helper_Brule_Total_DiscountByQty',
            'Checkout_Model_Service_Helper_Brule_Total_Shipment',

            'Model_Service_Helper_Multisite',
        );


    protected $_session = NULL;

    public function init()
    {
        $this->addHelper('Multisite', $this->getInjector()->getObject('Model_Service_Helper_Multisite', $this));
        $this->addHelper('BruleTotal', $this->getInjector()->getObject('Model_Service_Helper_BruleTotal', $this));
        $this->addHelper('BruleShipment', $this->getInjector()->getObject('Model_Service_Helper_BruleShipment', $this));
        $this->addHelper('BrulePayment', $this->getInjector()->getObject('Model_Service_Helper_BrulePayment', $this));
    }

    /**
     * Proxy for IDE
     *
     * @param null $name
     *
     * @return Checkout_Model_Mapper_Db_Order
     */
    public function getMapper($name = null)
    {
        return parent::getMapper($name);
    }

    protected function _session()
    {
        if ($this->_session === NULL) {
            $this->_session = new Zend_Session_Namespace(__CLASS__);
        }
        return $this->_session;
    }
    
    public function create()
    {
        $obj = $this->getInjector()->getObject('Model_Object_Interface');
        $userService = Model_Service::factory('user');
        if ($userService->isAuthorized()) {
            $obj->client_id = $userService->getCurrent()->id;            
        }
        return $obj;
    }

    public function getAvailableShipments()
    {
        return $this->getHelper('BruleShipment')
                    ->getAvailableShipments($this->_session()->order->payment,
                                            $this->_session()->order);
    }

    public function getAvailablePayments()
    {
        return $this->getHelper('BrulePayment')
                    ->getAvailablePayments($this->_session()->order->payment,
                                            $this->_session()->order);
    }

    public function getStatusesList()
    {
        $list = Zend_Registry::get('checkout_config')->orderStatus->toArray();
        return $list;
    }

    public function getStatusCodeByValue($val)
    {
        $list = $this->getStatusesList();
        $result = FALSE;
        foreach ($list as $code=>$value) {
            if ($value == $val) {
                $result = $code;
                break;
            }
        }
        return $result;
    }

    public function resetCurrent($fillItemsFromCart = TRUE)
    {
        $this->_session()->order = $this->create();
        $this->_session()->order->adder_id = Model_Service::factory('user')->getCurrent()->id;
        if ($fillItemsFromCart) {
            $cartService = Model_Service::factory('checkout/cart');
            $this->setItems($cartService->getAll());
            /*$cartService->reset();*/
        }
        $this->_session()->order->currency = Model_Service::factory('currency')->getCurrent()->code;
        return $this;
    }

    public function getCurrent()
    {
        return $this->_session()->order;
    }

    public function setCurrentFromPreorder(Model_Object_Interface $preorder)
    {
        $this->resetCurrent();
        $this->setItems($preorder->items);
        $this->_session()->order->currency = $preorder->currency;
        return $this;
    }

    public function setItems(Model_Collection_Interface $items)
    {
        $this->_session()->order->items = $items;
        return $this;
    }

    public function getItems()
    {
        return $this->_session()->order->items;
    }

    public function setPayment(Model_Object_Interface $payment)
    {
        $this->_session()->order->payment = $payment;
        return $this;
    }

    public function getPayment()
    {
        return $this->_session()->order->payment;
    }

    public function setPaymentFromValues(array $values)
    {
        $mapper = $this->getInjector()->getObject('Model_Mapper_Array_Payment');
        if ($payment = $this->_session()->order->payment) {
            $payment = $mapper->mapSimpleObject($payment, $values);
        }
        else {
            $payment = $mapper->makeSimpleObject($values);
        }
        $this->setPayment($payment);
        return $this;
    }

    public function setShipment(Model_Object_Interface $shipment)
    {
        $this->_session()->order->shipment = $shipment;
        return $this;
    }

    public function getShipment()
    {
        return $this->_session()->order->shipment;
    }

    public function setShipmentFromValues(array $values)
    {
        $mapper = $this->getInjector()->getObject('Model_Mapper_Array_Shipment');
        if ($shipment = $this->_session()->order->shipment) {
            $shipment = $mapper->mapSimpleObject($shipment, $values);
        }
        else {
            $shipment = $mapper->makeSimpleObject($values);
        }
        $this->setShipment($shipment);
        return $this;
    }

    public function createItemCollection()
    {
        return $this->getInjector()->getObject('Model_Collection_Interface');
    }

    /**
     * confirm current order, init client and save it
     */
    public function confirmCurrent()
    {
        $this->confirm($this->getCurrent());
        return $this;
    }

    public function confirm(Model_Object_Interface $o)
    {
        $config = Zend_Registry::get('checkout_config');
        $o->is_confirmed = true;
        if ($config->order->waitForApprove > 0) {
            $o->status = $config->orderStatus->created;
        } else {
            $o->status = $config->orderStatus->processing;
        }

        /** @var $userService Model_Service_User */
        $userService = Model_Service::factory('user');
        if ($userService->isAuthorized()) {
            $o->client_id = $userService->getCurrent()->id;
        }
        $o->total = $this->calculateTotal($o);
        $o->export = 1;

        $o->send_mail_to_client = true;

        $this->saveComplex($o);
        /** @var $cartService Checkout_Model_Service_Cart */
        $cartService = Model_Service::factory('checkout/cart');
        $cartService->reset();

        return $this;
    }

    public function paginatorGetAllByUser(Model_Object_Interface $user = NULL, $rowsPerPage = NULL, $page = NULL)
    {
        if ($user === NULL) {
            $user = Model_Service::factory('user')->getCurrent();
        }
        if ($rowsPerPage === NULL) {
            $rowsPerPage = Zend_Registry::get('config')->default->paginator->rowsPerPage;
        }
        if ($page === NULL) {
            $page = Zend_Controller_Front::getInstance()->getRequest()->getParam('page');
        }
        $paginator = $this->getMapper()->paginatorFetchComplexByUser($user, $rowsPerPage, $page);
        return $paginator;
    }

    public function calculateTotal(Model_Object_Interface $order = NULL)
    {
        if ($order === NULL) {
            $order = $this->_session()->order;
        }
        /** @var $bruleHelper Checkout_Model_Service_Helper_Brule_Total */
        $bruleHelper = $this->getHelper('BruleTotal');
        $result = $bruleHelper->setOrder($order)->calculateTotal();

        return $result;
    }

    public function calculateTotalQty(Model_Collection_Interface $items = NULL)
    {
        if ($items === NULL) {
            $items = $this->_session()->order->items;
        }
        $qty = 0;
        foreach ($items as $key=>$item) {
            $qty += (float) $item->qty;
        }
        return $qty;
    }

    /********* items ***********/
    public function parseItemsFromXML($xml)
    {
        /** @var $mapper Checkout_Model_Mapper_XML_CartItem */
        $mapper = $this->getInjector()->getObject('Model_Mapper_Item');
        $items = null;
        if (!empty($xml)) {
            $items = new SimpleXMLElement($xml);
        }

        return $mapper->makeSimpleCollection($items);
    }

    public function parseItemsToXML(Catalog_Model_Collection_Attribute $coll)
    {
        /** @var $mapper Checkout_Model_Mapper_XML_CartItem */
        $mapper = $this->getInjector()->getObject('Model_Mapper_Item');
        return $mapper->unmapCollectionToXML($coll);
    }

    /********** brules *************/
    public function parseBrulesFromXML($xml)
    {
        /** @var $mapper Checkout_Model_Mapper_XML_Brule */
        $mapper = $this->getInjector()->getObject('Model_Mapper_Brule');
        $brules = null;
        if (!empty($xml)) {
            $brules = new SimpleXMLElement($xml);
        }

        return $mapper->makeSimpleCollection($brules);
    }

    public function parseBrulesToXML(Catalog_Model_Collection_Attribute $coll)
    {
        /** @var $mapper Checkout_Model_Mapper_XML_Brule */
        $mapper = $this->getInjector()->getObject('Model_Mapper_Brule');
        return $mapper->unmapCollectionToXML($coll);
    }

    /************* shipment ****************/
    public function parseShipmentFromXML($xml)
    {
        $mapper = $this->getInjector()->getObject('Model_Mapper_Shipment');
        if ( ! empty($xml)) {
            $shipment = new SimpleXMLElement($xml);
        }
        else {
            $shipment = NULL;
        }
        return $mapper->makeSimpleObject($shipment);
    }

    public function parseShipmentToXML(Catalog_Model_Collection_Attribute $coll)
    {
        $mapper = $this->getInjector()->getObject('Model_Mapper_Shipment');
        return $mapper->unmapObjectToXML($coll);
    }


    /***************payment ******************/
    public function parsePaymentFromXML($xml)
    {
        $mapper = $this->getInjector()->getObject('Model_Mapper_Payment');
        if ( ! empty($xml)) {
            $payment = new SimpleXMLElement($xml);
        }
        else {
            $payment = NULL;
        }
        return $mapper->makeSimpleObject($payment);
    }

    public function parsePaymentToXML(Catalog_Model_Collection_Attribute $coll)
    {
        $mapper = $this->getInjector()->getObject('Model_Mapper_Payment');
        return $mapper->unmapObjectToXML($coll);
    }
    
    public function getCatalogItems($page, $rowsPerPage, $searchQ = NULL, $searchBy = NULL, $sortBy = NULL, $sortDirection = NULL)
    {
        $itemsData = App_Event::factory('Checkout_Model_Service_Order__onGetCatalogItems', array($page, $rowsPerPage, $searchQ, $searchBy, $sortBy, $sortDirection))->dispatch()->getResponse();
        return $itemsData;
    }

    public function updateStatus($id, $status)
    {
        return $this->getMapper()->updateStatus($id, $status);
    }

    public function getOrderById($id)
    {
        return $this->getMapper()->getOrderById($id);
    }
    
    public function getAllByClient($uid)
    {
        return $this->getMapper()->fetchAllByClient($uid);
    }

    /**
     * Получить массив
     *
     * @return array
     */
    public function getAllOrdersExport()
    {
        $startTime = time();
        /** @var $userService Model_Service_User */
        $userService = Model_Service::factory('user');

        $data = $this->getMapper()->fetchAllExport($startTime);

        $orders = array();
        foreach ($data as $val) {
            $items = $this->_prepareItemsForUpload($val->items);
            $orders[] = array(
                'guid'        => $val->guid,
                'date'        => $val->date_added,
                'client_guid' => $userService->get($val->client_id)->guid,
                'items'       => array('item' => $items),
                'total'       => number_format($val->total, 2, ',', ' '),
                'status'      => $val->status
            );
        }

        $this->getMapper()->clearExport($startTime);

        return $orders;
    }

    /**
     * Добавить/обновить запись
     *
     * @param SimpleXMLElement $orders
     * @return int
     */
    public function setOrders(SimpleXMLElement $orders)
    {
        ini_set('memory_limit', '512M');
        /** @var $userService Model_Service_User */
        $userService = Model_Service::factory('user');
        /** @var $currencyService Model_Service_Currency */
        $currencyService = Model_Service::factory('currency');
        $success = 0;

        foreach ($orders->order as $order) {
            $order = $this->_objectToArray($order);
            $orderObj = $this->getOrderByGuid($order['guid']);

            /* Если пользователь не найден по guid - возвращаем неудачу */
            if (null == ($user = $userService->getUserByGuid($order['client_guid']))) continue;

            if (null !== $orderObj) {
                /* Modify order */
                /* send email for user about modify order */
                $orderObj->send_mail_to_client = true;
            } else {
                /* New order */
                $orderObj = $this->create();

                $orderObj->id = null;
                $orderObj->guid = $order['guid'];
                $orderObj->currency = $currencyService->getDefault()->code;
            }

            $orderObj->client_email = $user->email;
            $orderObj->client_name = $user->name;
            $orderObj->adder_id = $orderObj->client_id = $user->id;
            $orderObj->items = $this->_makeComplexItems($order['items']['item'], $orderObj->items);
            $orderObj->export = 0;
            $orderObj->changer_id = null;
            $orderObj->status = $order['status'];
            $orderObj->date_changed = date('Y-m-d H:i:s');

            try {
                $this->saveComplex($orderObj);
                $success++;
            } catch (Exception $e) {}
        }

        return $success;
    }

    /**
     * Get order by guid
     *
     * @param $guid
     *
     * @return Checkout_Model_Object_Order
     */
    public function getOrderByGuid($guid)
    {
        return $this->getMapper()->fetchOrderByGuid($guid);
    }

    /**
     * Convert object ot array
     *
     * @param $object
     *
     * @return array
     */
    private function _objectToArray($object)
    {
        $resArr = array();
        if (!empty($object)) {
            $arrObj = is_object($object) ? get_object_vars($object) : $object;
            foreach ($arrObj as $key => $val) {
                if (is_array($val) || is_object($val)) {
                    $resArr[$key] = (!(is_numeric($val) && $val == 0) && empty($val)) ?
                        null :
                        $this->_objectToArray($val);
                } else {
                    $resArr[$key] = (!(is_numeric($val) && $val == 0) && empty($val)) ?
                        null :
                        (string)$val;
                }
            }
        }

        return $resArr;
    }

    /**
     * Make complex from array items
     *
     * @param array $data
     * @param Checkout_Model_Collection_CartItem $existingItems
     *
     * @return Checkout_Model_Collection_CartItem
     */
    private function _makeComplexItems(array $data, Checkout_Model_Collection_CartItem $existingItems)
    {
        /** @var $collection Checkout_Model_Collection_CartItem */
        $collection = $this->getInjector()->getObject('Checkout_Model_Collection_CartItem');

        if ($data[0]) {
            foreach ($data as $val) {
                $collection->add($this->_preMakeComplexItems($val, $existingItems));
            }
        } else {
            $collection->add($this->_preMakeComplexItems($data, $existingItems));
        }

        return $collection;
    }

    /**
     * Create item object
     *
     * @param array $data
     * @param Checkout_Model_Collection_CartItem $existingItems
     *
     * @return Checkout_Model_Object_CartItem
     * @throws Model_Service_Exception
     */
    private function _preMakeComplexItems(array $data, Checkout_Model_Collection_CartItem $existingItems)
    {
        /** @var $itemService Catalog_Model_Service_Item */
        $itemService = Model_Service::factory('catalog/item');

        /** @var $itemObj Checkout_Model_Object_CartItem */
        $itemObj = $this->getInjector()->getObject('Checkout_Model_Object_CartItem');

        $tmpItems = clone($existingItems);
        if ($item = $tmpItems->findByElement('sku', $data['sku'], true)) {
            /* Update existing item on order */
            foreach (array_keys($data) as $key) {
                if ($key == 'price') {
                    $item->{$key} = $this->_setNumbersForSql($data[$key]);
                } else {
                    $item->{$key} = $data[$key];
                }
            }
            $itemObj = $item;
        } else {
            /* Create new item */
            $data['qty'] = ($data['qty']) ? $data['qty'] : 1;
            $data['id'] = $itemService->getItemIdBySku($data['sku']);
            $item = $this->_createFromArray($data);
            $itemObj->populate($itemService->itemToArray($item));
        }

        $itemObj->remain_price = $itemObj->price;

        return $itemObj;
    }

    /**
     * Fetch and update item object
     *
     * @param array $values
     *
     * @return Model_Object_Interface
     * @throws Model_Service_Exception
     */
    private function _createFromArray(array $values)
    {
        /** @var $itemService Catalog_Model_Service_Item */
        $itemService = Model_Service::factory('catalog/item');

        if ($values['id']) {
            $item = $itemService->getComplex($values['id']);
        } else {
            $item = $itemService->create();
        }

        if (array_key_exists('attributes', $values)) {
            if (is_array($values['attributes'])) {
                foreach ($values['attributes'] as $attrCode => $attrValue) {
                    $item->attributes->findOneByCode(
                        $attrCode
                    )->current_value = $attrValue;
                }
            }
        } else {
            foreach ($values as $attrCode => $attrValue) {
                if ($attr = $item->attributes->findOneByCode($attrCode)) {
                    $attr->current_value = $attrValue;
                }
            }
        }

        foreach (array_keys($values) as $key) {
            if ($key == 'price') {
                $item->{$key} = $this->_setNumbersForSql($values[$key]);
            } else {
                $item->{$key} = $values[$key];
            }
        }

        return $item;
    }

    /**
     * Set number format for MySQL
     *
     * @param $number
     *
     * @return mixed|null
     */
    private function _setNumbersForSql($number)
    {
        if (null == $number) return null;

        return str_replace(',', '.', str_replace(' ', '', $number));
    }

    /**
     * Prepare items for 1C format
     *
     * @param $items
     *
     * @return array
     */
    private function _prepareItemsForUpload($items)
    {
        $preparedItems = array();
        foreach ($items as $item) {
            $preparedItems[] = array(
                'sku'      => $item->sku,
                'code'     => $item->code,
                'qty'      => $item->qty,
                'probe'    => $item->probe,
                'size'     => number_format($item->size, 2, '.', ''),
                'material' => $item->material,
                'price'    => number_format($item->price, 2, ',', ' ')
            );
        }

        return $preparedItems;
    }
}
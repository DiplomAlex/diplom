<?php

class Checkout_Model_Service_Preorder extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface'     => 'Checkout_Model_Object_Preorder',
        'Model_Collection_Interface' => 'Checkout_Model_Collection_Preorder',
        'Model_Mapper_Interface'     => 'Checkout_Model_Mapper_Db_Preorder',

        'Model_Mapper_Item'          => 'Checkout_Model_Mapper_XML_CartItem',
        'Model_Mapper_Brule'         => 'Checkout_Model_Mapper_XML_Brule',
        'Model_Mapper_Shipment'      => 'Checkout_Model_Mapper_XML_Shipment',
        'Model_Mapper_Payment'       => 'Checkout_Model_Mapper_XML_Payment',

        'Model_Object_Brule' => 'Checkout_Model_Object_Brule',
        'Model_Collection_Brule' => 'Checkout_Model_Collection_Brule',

        'Model_Object_Shipment'     => 'Checkout_Model_Object_Shipment',
        'Model_Collection_Shipment' => 'Checkout_Model_Collection_Shipment',
        'Model_Object_Payment'      => 'Checkout_Model_Object_Payment',
        'Model_Collection_Payment'  => 'Checkout_Model_Collection_Payment',

        'Model_Service_Helper_BruleTotal' => 'Checkout_Model_Service_Helper_Brule_Total',
        'Model_Service_Helper_BruleShipment' => 'Checkout_Model_Service_Helper_Brule_Shipment',
        'Model_Service_Helper_BrulePayment' => 'Checkout_Model_Service_Helper_Brule_Payment',


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



    protected function _session()
    {
        if ($this->_session === NULL) {
            $this->_session = new Zend_Session_Namespace(__CLASS__);
        }
        return $this->_session;
    }


    public function getAvailableShipments()
    {
        return Model_Service::factory('checkout/brule')->getAll(Checkout_Model_Service_Brule::TYPE_SHIPMENT);
    }

    public function getAvailablePayments()
    {
        return Model_Service::factory('checkout/brule')->getAll(Checkout_Model_Service_Brule::TYPE_PAYMENT);
    }

    public function getStatusesList()
    {
        $list = Zend_Registry::get('checkout_config')->preorderStatus->toArray();
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
        $this->_session()->preorder = $this->create();
        $this->_session()->preorder->adder_id = Model_Service::factory('user')->getCurrent()->id;
        if ($fillItemsFromCart) {
            $this->setItems(Model_Service::factory('checkout/cart')->getAll());
        }
        $this->_session()->preorder->currency = Model_Service::factory('currency')->getCurrent()->code;
        return $this;
    }


    public function getCurrent()
    {
        return $this->_session()->preorder;
    }


    public function setItems(Model_Collection_Interface $items)
    {
        $this->_session()->preorder->items = $items;
        return $this;
    }
    
    public function loadCurrent($id = NULL)
    {
        $this->resetCurrent();
        $curr = $this->getCurrent();
        if ($id) {
            $preorder = $this->getComplex($id);
            $curr->id = $preorder->id;
            $curr->items = $preorder->items;
            $cartService = Model_Service::factory('checkout/cart');
            $cartService->setItems($curr->items);
        }
        else {
            $this->saveComplex($curr);
        }
        return $curr;        
    }

    public function getItems()
    {
        return $this->_session()->preorder->items;
    }




    public function setPayment(Model_Object_Interface $payment)
    {
        $this->_session()->preorder->payment = $payment;
        return $this;
    }

    public function getPayment()
    {
        return $this->_session()->preorder->payment;
    }

    public function setPaymentFromValues(array $values)
    {
        $mapper = $this->getInjector()->getObject('Model_Mapper_Payment');
        $payment = $mapper->makeSimpleObject($values);
        $this->setPayment($payment);
        return $this;
    }




    public function setShipment(Model_Object_Interface $shipment)
    {
        $this->_session()->preorder->shipment = $shipment;
        return $this;
    }

    public function getShipment()
    {
        return $this->_session()->preorder->shipment;
    }

    public function setShipmentFromValues(array $values)
    {
        $mapper = $this->getInjector()->getObject('Model_Mapper_Shipment');
        $shipment = $mapper->makeSimpleObject($values);
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
        $o->is_confirmed = TRUE;
        if ($config->preorder->waitForApprove > 0) {
            $o->status = $config->preorderStatus->created;
        }
        else {
            $o->status = $config->preorderStatus->processing;
        }
        if ( ! (int) $o->client_id) {
            $o->client_id = Model_Service::factory('user')->getCurrent()->id;
        }
        $o->total = $this->calculateTotal($o);
        $user = Model_Service::factory('user')->getCurrent();
        $o->adder_id = $user->id;
        $this->saveComplex($o);
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
            $order = $this->_session()->preorder;
        }
        return $this->getHelper('BruleTotal')->setOrder($order)->calculateTotal();
    }

    public function calculateTotalQty(Model_Collection_Interface $items = NULL)
    {
        if ($items === NULL) {
            $items = $this->_session()->preorder->items;
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
        $mapper = $this->getInjector()->getObject('Model_Mapper_Item');
        if ( ! empty($xml)) {
            $items = new SimpleXMLElement($xml);
        }
        else {
            $items = NULL;
        }
        return $mapper->makeSimpleCollection($items);
    }

    public function parseItemsToXML(Catalog_Model_Collection_Attribute $coll)
    {
        $mapper = $this->getInjector()->getObject('Model_Mapper_Item');
        return $mapper->unmapCollectionToXML($coll);
    }

/********** brules *************/

    public function parseBrulesFromXML($xml)
    {
        $mapper = $this->getInjector()->getObject('Model_Mapper_Brule');
        if ( ! empty($xml)) {
            $brules = new SimpleXMLElement($xml);
        }
        else {
            $brules = NULL;
        }
        return $mapper->makeSimpleCollection($brules);
    }

    public function parseBrulesToXML(Catalog_Model_Collection_Attribute $coll)
    {
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


}
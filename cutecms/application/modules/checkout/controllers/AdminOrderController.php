<?php

class Checkout_AdminOrderController extends Zend_Controller_Action
{

    protected $_session = NULL;

    protected function _session()
    {
        if ($this->_session === NULL) {
            $this->_session = new Zend_Session_Namespace(__CLASS__);
        }
        return $this->_session;
    }

    public function init()
    {
        App_Event::factory('AdminController__init', array($this))->dispatch();
    }

    public function indexAction()
    {
        $this->getHelper('ReturnUrl')->remember();

        $filter = new Checkout_Form_AdminOrderFilter;
        $filter->populate($this->getRequest()->getParams());
        $this->view->filter = $this->view->renderForm($filter, 'admin-order/filter.phtml');
        $service = Model_Service::factory('checkout/order');
        $siteId = $this->getHelper('AdminMultisite')->getSiteId();
        $service->getHelper('Multisite')->setCurrentSiteId($siteId);

        $this->view->orders = $service->paginatorGetAll(
            $this->getHelper('RowsPerPage')->saveValue()->getValue(),
            $this->_getParam('page')
        );
    }

    public function deleteAction()
    {
        if ( ! $submitUrl = $this->getHelper('ReturnUrl')->get()) {
            $submitUrl = $this->view->stdUrl(array('id'=>NULL), 'index');
        }
        $service = Model_Service::factory('checkout/order');
        $order = $service->getComplex($this->_getParam('id'));
        $form = new App_Form_Question;
        $form->setMethod('POST');
        if ($this->getRequest()->isPost()) {
            if ($form->getAnswer()=='yes') {
                try {
                    if (( ! $this->getHelper('AdminMultisite')->isAllowedMultisite()) AND ($order->site_id != $this->getHelper('AdminMultisite')->getSiteId())) {
                        throw new Model_Exception;
                    }
                    $service->delete($order);
                    $this->getHelper('flashMessenger')->addMessage($this->view->translate('Order "%1$s" deleted', $order->id));
                }
                catch (Model_Exception $e) {
                    $this->getHelper('flashMessenger')->addMessage('!'.$this->view->translate('Unable to delete order "%1$s")', $order->id));
                }
            }
            else {
                $this->getHelper('flashMessenger')->addMessage('Deletion cancelled');
            }
            $this->getHelper('Redirector')->gotoUrlAndExit($submitUrl);
        }
        else {
            $this->view->order = $order;
            $this->view->form = $form;
        }
    }


    public function editAction()
    {
        if ( ! $cancelUrl = $this->getHelper('ReturnUrl')->get()) {
            $cancelUrl = $this->view->stdUrl(array('id'=>NULL), 'index');
        }
        if ( ! $submitUrl = $this->getHelper('ReturnUrl')->get()) {
            $submitUrl = $this->view->stdUrl(array('id'=>NULL), 'index');
        }
        /** @var $service Checkout_Model_Service_Order */
        $service = Model_Service::factory('checkout/order');
        $siteId = $this->getHelper('AdminMultisite')->getSiteId();
        $service->getHelper('Multisite')->setCurrentSiteId($siteId);

        // init form
        $form = new Checkout_Form_AdminOrderEdit;
        $this->_extendEditForm($form);
        // if 'cancel' was pressed - get away
        if ($form->getAnswer() == 'cancel') {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Order edition cancelled'));
            $this->getHelper('Redirector')->gotoUrlAndExit($cancelUrl);
        }
        $this->_session()->editingOrderId = $this->_getParam('id');
        if ( ! $this->getRequest()->isPost()) {
        // if it was just called (via get)
            $values = array();
            if ( (int) $this->_getParam('id')) {
                //load $values from db
                $values = $service->getEditFormValues($this->_getParam('id'));
                $rqs = '';
                if (is_array($values['shipment']->client_requisites)) {
                    $rqs = implode(' / ', $values['shipment']->client_requisites);
                }
                if ( ! empty($values['shipment']->params['comment'])) {
                    $rqs .= '<br>'.$this->view->translate('Комментарий').': '.$values['shipment']->params['comment'];
                }
                $values['client_requisites_spec'] = $rqs;

            } else {
                //init $values
                $values = $service->create()->toArray();
                if ($this->_getParam('client_id')) {
                    $values['client_id'] = $this->_getParam('client_id');
                }
            }
            $this->_session()->editingItems = $values['items'];
            $this->_session()->editingShipment = $values['shipment'];
            $this->_session()->editingPayment = $values['payment'];
            $this->_session()->editingBrules = $values['brules'];
            $this->_session()->currency = $values['currency'];
            $form->populate($values);
            $this->view->form = $form;
            $this->view->values = $values;

            return;
        } else {
            // if the form was posted
            $values = $this->getRequest()->getParams();
            if ( (int) $values['id'] > 0) {
                $orderData = $service->getEditFormValues($this->_getParam('id'));
                $values = array_merge($orderData, $values);
            }
            $values['items'] = $this->_session()->editingItems;
            $values['shipment'] = $this->_session()->editingShipment;
            $values['payment'] = $this->_session()->editingPayment;
            $values['brules'] = $this->_session()->editingBrules;
            $form->populate($values);
            $this->view->form = $form;
            $this->view->values = $values;
        }
        // validate it
        if ( ! $form->isValid($values)) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Form validation failed'));
            return;
        }

        // save
        $values['export'] = 1;
        $service->saveFromValues($values);
        // add message to flash queue
        $this->getHelper('flashMessenger')->addMessage($this->view->translate('Order saved'));
        //redirect
        $this->getHelper('Redirector')->gotoUrlAndExit($submitUrl);
        return;
    }


    /**
     * get request for items from flexigrid
     * if $_GET['hash'] - requesting concrete item, otherwise - all items of order
     */
    public function ajaxGetItemAction()
    {
        $rowId = $this->_getParam('rowId');
        if (empty($rowId)) {
            $rows = array();
            $i = 0;
            foreach ($this->_session()->editingItems as $row) {
                $rows[] = array(
                    'id' => $row['hash'],
                    'cell' => array(
                        ++$i,
                        $row['name'],
                        $row['attributes_text'],
                        $row['code'],
                        $this->view->order_Summ($row['remain_price'], $this->_session()->currency),
                        $row['qty'],
                        $this->view->order_Summ($row['remain_price']*$row['qty'], $this->_session()->currency),
                        $row['material'],
                        $row['probe'],
                        $row['size'],
                    ),
                );
            }
            $answer = array(
                'page' => '1',
                'total' => ($this->_session()->editingItems instanceof Countable?$this->_session()->editingItems->count():'0'),
                'rows' => $rows,
            );
        }  else {
            $rowId = substr($rowId, 3); /* remove prefix "row" added by jquery.flexigrid.js */
            $answer = array();
            foreach ($this->_session()->editingItems as $key=>$var) {
                if ($var['hash'] == $rowId) {
                    $answer = $var->toArray();
                    break;
                }
            }

        }

        $this->_helper->json(($answer));
    }

    public function ajaxDeleteItemAction()
    {
        $rows = $this->_getParam('rows');
        foreach ($rows as $row) {
            $rowId = substr($row, 3); /*remove the prefix "row" from id*/
            foreach ($this->_session()->editingItems as $key=>$var) {
                if ($var['hash'] == $rowId) {
                    $this->_session()->editingItems->remove($key);
                    break;
                }
            }
        }
        echo 'ok';
        exit;
    }


    public function ajaxEditItemAction()
    {
        $rowId = substr($this->_getParam('rowId'), 3);
        $values = $this->getRequest()->getParams();
        $cartService = Model_Service::factory('checkout/cart');
        $found = FALSE;
        $values['remain_price'] = $values['price'];
        foreach ($this->_session()->editingItems as $item) {
            if ($item['hash'] == $rowId) {

                foreach ($values as $valKey=>$valVal) {
                    if ($item->hasElement($valKey)) {
                        $item[$valKey] = $valVal;
                    }
                }
                $found = TRUE;
                break;
            }
        }
        if ( ! $found) {
            $values['id'] = $values['catalog_item_id'];
            $item = $cartService->createItem($values);
            $this->_session()->editingItems->add($item);
        }
        echo 'ok';
        exit;
    }

    /**
     * get one or all brules of currently editing item:
     * if $_REQUEST['rowId'] isset then recieves one brule by its hash otherwise - returns all
     */
    public function ajaxGetBruleAction()
    {
        /** @var $service Checkout_Model_Service_Order */
        $service = Model_Service::factory('checkout/order');
        $order = $service->getComplex($this->_session()->editingOrderId);
        $tots = $service->getHelper('BruleTotal')
                        ->setOrder($order)->setItems($this->_session()->editingItems)
                        ->getRows();
        $rows = array();
        foreach ($tots as $row) {
            $rows[] = array(
                'id' => uniqid('brule'),
                'cell' => array($row['title'], $this->view->order_Percent($row['percent']), $this->view->order_Summ($row['summ'], $this->_session()->currency)),
            );
        }
        $answer = array(
            'page' => '1',
            'total' => count($tots),
            'rows' => $rows,
        );
        echo Zend_Json::encode($answer);

        exit;
    }

    public function ajaxGetCatalogItemsAction()
    {
        $service = Model_Service::factory('checkout/order');
        $page = $this->_getParam('page');
        $selForm = new Checkout_Form_AdminOrderItemSelect();
        $rowsPerPage = $selForm->getElement('items_list')->getAttrib('rp');
        $searchQ = $this->_getParam('query');
        $searchBy = $this->_getParam('qtype');
        $sortBy = $this->_getParam('sortname');
        $sortDirection = $this->_getParam('sortorder');
        $itemsData = $service->getCatalogItems($page, $rowsPerPage, $searchQ, $searchBy, $sortBy, $sortDirection);
        $rows = array();
        foreach ($itemsData['items'] as $row) {
            $rows []= array(
                'id' => $row['catalog_item_id'],
                'cell' => array($row['name'], $row['price'], $row['sku'], $row['attributes_text'],),
            );
        }
        $answer = array(
            'page' => $page,
            'total' => $itemsData['totalItems'],
            'rows' => $rows,
        );
        echo Zend_Json::encode($answer);
        $this->view->layout()->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();
    }


    /**
     * serve mass actions from list
     */
    public function massAction()
    {
        if ( ! $massAction = $this->_getParam('mass_action')) {
            throw new Zend_Controller_Action_Exception('parameter mass_action should be set');
        }

        if (( ! $massCheck = $this->_getParam('mass_check')) OR ( ! is_array($massCheck))) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('No rows were selected for mass action'));
        }
        else {
            $this->{'_mass_'.$massAction}($massCheck);
        }

        $this->getHelper('Redirector')->gotoUrlAndExit($this->view->stdUrl(array('id'=>NULL, 'mass_check'=>NULL, 'mass_action'=>NULL), 'index'));
    }

    protected function _mass_delete(array $massCheck)
    {
        try {
            Model_Service::factory('checkout/order')->deleteByIdArray($massCheck);
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Rows were deleted'));
        }
        catch (Model_Exception $e) {
            $this->getHelper('flashMessenger')->addMessage($this->view->translate('Some rows were not deleted'));
        }
    }

    protected function _extendEditForm(Zend_Form $form)
    {
        /*
        if ($this->getHelper('AdminMultisite')->isAllowedMultisite()) {            
            $form->addElement('select', 'site_id', array('label'=>$this->view->translate('Web-site')));
            $sites = Model_Service::factory('site')->getAllAsSelectOptions($this->view->translate('None'));
            $form->site_id->setMultiOptions($sites);
        }
        else {
            $form->addElement('hidden', 'site_id');
        }
         */
        $form->addElement('hidden', 'site_id');
    }

}

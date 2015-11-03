<?php

class Checkout_Model_Mapper_Db_Preorder extends Model_Mapper_Db_Abstract
{

    protected $_defaultInjections = array(
        'Model_Db_Table_Interface'   => 'Checkout_Model_Db_Table_Preorder',
        'Model_Object_Interface'     => 'Checkout_Model_Object_Preorder',
        'Model_Collection_Interface' => 'Checkout_Model_Collection_Preorder',
        'Model_Mapper_Interface'     => 'Checkout_Model_Mapper_Db_Preorder',

        'Model_Mapper_Item'          => 'Checkout_Model_Mapper_XML_CartItem',
        'Model_Mapper_Brule'         => 'Checkout_Model_Mapper_XML_Brule',
        'Model_Mapper_Shipment'      => 'Checkout_Model_Mapper_XML_Shipment',
        'Model_Mapper_Payment'       => 'Checkout_Model_Mapper_XML_Payment',

        'Model_Mapper_Db_Plugin_Filter_Preorder' => 'Checkout_Model_Mapper_Db_Plugin_Filter_Preorder',
        'Model_Mapper_Db_Plugin_Multisite' => 'Model_Mapper_Db_Plugin_Multisite_OneToMany',
        'Model_Mapper_Db_Site',    
    );


    public function init()
    {
        $this
            ->addPlugin('Filters', $this->getInjector()->getObject('Model_Mapper_Db_Plugin_Filter_Preorder',
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
                    'client.user_id = preorder_adder_id',
                    array('preorder_client_id' => 'client.user_id',
                          'preorder_client_login'=>'client.user_login', 
                          'preorder_client_name'=>'client.user_name', )
                 )
               ->order('preorder_date_added DESC')
                 ;
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
        $obj->total = Model_Service::factory('checkout/preorder')->calculateTotal($obj);
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
        $query = $this->fetchComplex(NULL, FALSE)->where('preorder_client_id = ?', $user->id);
        return $this->paginator($query,  $rowsPerPage, $page, Model_Object_Interface::STYLE_COMPLEX);
    }


}
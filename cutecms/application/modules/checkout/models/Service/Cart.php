<?php

class Checkout_Model_Service_Cart extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface'     => 'Checkout_Model_Object_CartItem',
        'Model_Collection_Interface' => 'Checkout_Model_Collection_CartItem',
        'Model_Mapper_Interface'     => 'Checkout_Model_Mapper_XML_CartItem',
        'Model_Service_Helper_Brule' => 'Checkout_Model_Service_Helper_Brule_Total',
        'Checkout_Model_Service_Helper_Brule_Total_TotalSumm',
        'Checkout_Model_Service_Helper_Brule_Total_DiscountBySumm',
        'Checkout_Model_Service_Helper_Brule_Total_DiscountByQty',
        'Checkout_Model_Service_Helper_Brule_Total_Shipment',
    );

    protected $_session = NULL;


    public function init()
    {
        $this->addHelper('Brule', $this->getInjector()->getObject('Model_Service_Helper_Brule', $this));
    }



    protected function _session()
    {
        if ($this->_session === NULL) {
            $this->_session = new Zend_Session_Namespace(__CLASS__);
        }
        if ( ! $this->_session->items) {
            $this->_session->items = $this->getInjector()->getObject('Model_Collection_Interface');
        }
        return $this->_session;
    }

    public function createItemFromArray(array $values)
    {
        return $this->getMapper()->makeSimpleObjectFromArray($values);
    }

    public function add(Model_Object_Interface $item)
    {
        $found = FALSE;
        foreach ($this->_session()->items as $existItem) {
            if ($this->_isTheSameItem($existItem, $item)) {
                $existItem->qty += $item->qty;
                $found = TRUE;
            }
        }
        if ( ! $found) {
            $this->_session()->items->add($item);
        }
        return $this;
    }
    
    protected function _isTheSameItem(Model_Object_Interface $item1, Model_Object_Interface $item2)
    {
        $isTheSame = TRUE;
        $fields = array('id', 'seo_id', 'sku', 'catalog_item_id',
                        'code', 'unit', 'price', 
                        'param1', 'param2', 'param3', 
                        'name', 'brief', 'full', 
                        'attributes_html', 'bundles_html',  );
        foreach ($fields as $field) {
            if ($item1->{$field} != $item2->{$field}) {
                $isTheSame = FALSE;
                break;
            }
        }
        return $isTheSame;
    }
        
    public function setItems(Model_Collection_Interface $items)
    {
        $this->_session()->items = $items;
        return $this;
    }

    public function getAll()
    {		
		$this->recalculateItemsPrices($this->_session()->items);
		return $this->_session()->items;
    }

    public function recalculate(array $values)
    {
        foreach ($values as $hash=>$qty) {
            $item = $this->_session()->items->findOneByHash($hash);
            if ($qty == 0) {
            	$this->remove($hash);
            }
            else {
	            $item['qty'] = $qty;
	            $newPrice = $this->recalculateItemPrice($item);	            
	            App_Debug::dump($newPrice);
	            $item['price'] = $newPrice[0];
            }
        }
        return $this;
    }
    
    public function recalculateItemPrice($item)
    {
		$price = App_Event::factory('Checkout_Model_Service_Cart__onRecalculatePrice', array($item))->dispatch()->getResponse();
		return $price;
    }
    
    public function recalculateItemsPrices($items)
    {
		
        if ($items->count()) {
		
		    $itemPrices = App_Event::factory('Checkout_Model_Service_Cart__onRecalculatePrices', array($items))->dispatch()->getResponse();
			foreach ($this->_session()->items as $item) {
					$item->price = $itemPrices[$item->catalog_item_id];
				}
			return $itemPrices;
        }else{
		    return FALSE;
        }        
    }

    public function remove($hash)
    {
        foreach ($this->_session()->items as $key=>$item) {
            if ($item['hash'] == $hash) {
                $this->_session()->items->remove($key);
                break;
            }
        }
        return $this;
    }

    public function removeArray(array $hashes)
    {
        foreach ($hashes as $hash) {
            $this->_session()->items->removeByElement('hash', $hash);
        }
        return $this;
    }
    
    public function clean()
    {
        $this->_session()->items->clean();
        return $this;
    }

    public function calculateTotal(Model_Collection_Interface $items = NULL)
    {
        if ($items === NULL) {
            $items = $this->getAll();
        }
        $total = $this->getHelper('Brule')->setItems($items)->calculateTotal();
        return $total;
    }

    public function calculateTotalQty(Model_Collection_Interface $items = NULL)
    {
        if ($items === NULL) {
            $items = $this->_session()->items;
        }
        return $this->getHelper('Brule')->setItems($items)->calculateTotalQty();
    }

    public function createItem(array $values)
    {
        $values['catalog_item_id'] = $values['id'];
        unset($values['id']);
        $item = $this->getMapper()->makeCustomObject($values);
        return $item;
    }

    public function reset()
    {
        $this->_session()->items = NULL;
        return $this;
    }

}
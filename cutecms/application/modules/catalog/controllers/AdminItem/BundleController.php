<?php

class Catalog_AdminItem_BundleController extends Zend_Controller_Action
{
    
    protected $_defaultInjections = array();
    
    public function init()
    {
        $this->_helper->AdminItem($this->_defaultInjections);        
    }
    


    /*********************************************************************************/
    /********************* item bundles ************************************************/
    /*********************************************************************************/


    /**
     * get one or all bundles of currently editing item:
     * if $_REQUEST['rowId'] isset then recieves one bundle by its hash otherwise - returns all
     */
    public function ajaxGetAction()
    {
        $rowId = $this->_getParam('id');
        if (empty($rowId)) {
            $rows = array();
            foreach ($this->getHelper('AdminItem')->session()->editingBundles as $row) {
                $rows[] = array(
                    'id' => $row['hash'],
                    'cell' => array($this->_getBundleSortingHtml($row), $row['code'], $row['status'], $row['name'], $row['is_required'],),
                );
            }
            $answer = array(
                'page' => max( (int) $this->_getParam('page'), 1),
                'total' => $this->getHelper('AdminItem')->session()->editingBundles->count(),
                'rows' => $rows,
            );
        }
        else {
            $answer = array();
            foreach ($this->getHelper('AdminItem')->session()->editingBundles as $key=>$bundle) {
                if ($bundle['hash'] == $rowId) {
                    $statuses = Model_Service::factory('catalog/item-bundle')->getAllStatuses(TRUE);
                    $answer = array(
                        'code'               => $bundle['code'],
                        'status'             => $statuses[$bundle['status']],
                        'name'               => (string) $bundle['name'],
                        'is_required'        => $this->_yesNo($bundle['is_required']),
                        'price'              => (string) $bundle['price'],
                        'qty'                => (string) $bundle['qty'],
                    );                    
                    $this->getHelper('AdminItem')->session()->editingSubitems = $bundle->subitems;
                    break;
                }
            }
        }
        echo Zend_Json::encode($answer);
    }

    private function _yesNo($val)
    {
        $yesNo = array(
            0 => $this->view->tranlsate('нет'),
            1 => $this->view->tranlsate('да'),
        );
        return $yesNo[ (int) (bool) $val];
    }

    /**
     * edit existing attribute of item
     * checks duplications by code
     * $_REQUEST should contain a map of the attribute ($field=>$value)
     */
    public function ajaxEditAction()
    {
        $service = Model_Service::factory('catalog/item-bundle');
        $rowId = $this->_getParam('id');
        $values = $this->getRequest()->getParams();
        $duplicate = FALSE;
        $found = FALSE;
        foreach ($this->getHelper('AdminItem')->session()->editingBundles as $bundle) {
            if ($bundle['hash'] == $rowId) {
                $bundle->code = $values['code'];
                $bundle->name = $values['name'];
                $bundle->status = $values['status'];
                $bundle->is_required = $values['is_required'];
                $bundle->param1 = $values['param1'];
                $found = TRUE;
                break;
            }
        }
        if ( ! $found) {
            $new = $service->createBundleFromValues(array(
                'code' => $values['code'],
                'status' => $values['status'],
                'name' => $values['name'],
                'is_required' => $values['is_required'],
            ));
            $this->getHelper('AdminItem')->session()->editingBundles->add($new);
        }
        $answer = array(
            'code' => $values['code'],
            'name' => $values['name'],
            'status' => $values['status'],
            'is_required' => $values['is_required'],
        );
        if ( ! $found) {
            $answer['id'] = $new['hash'];
        }
        else {
            $answer['id'] = $bundle['hash'];
        }
        echo Zend_Json::encode($answer);
        
    }

    /**
     * $_REQUEST['rows'] contains array of attributes hashes (each added with prefix "row")
     */
    public function ajaxDeleteAction()
    {
        $rows = $this->_getParam('rows');
        foreach ($rows as $rowId) {
            foreach ($this->getHelper('AdminItem')->session()->editingBundles as $key=>$bundle) {
                if ($bundle['hash'] == $rowId) {
                    $this->getHelper('AdminItem')->session()->editingBundles->remove($key);
                }
            }
        }
        echo 'ok';
    }



    /*********************************************************************************/
    /********************* subitems ************************************************/
    /*********************************************************************************/


    /**
     * get all variants of attribute
     */
    public function ajaxGetSubitemAction()
    {
        $rowId = $this->_getParam('row_id');
        $isSimple = (bool) $this->_getParam('simple');
        $answer = array();
        foreach ($this->getHelper('AdminItem')->session()->editingBundles as $key=>$bundle) {
            if ($bundle['hash'] == $rowId) {
                foreach ($bundle['subitems'] as $row) {
                    $rows[] = array(
                        'id' => $row['hash'],
                        'cell' => array($this->_getSubitemSortingHtml($row, $bundle), $row['id'], $row['sku'], $row['spec_as_text'], $row['alias'], $row['price'], $row['param1'], $row['min_qty'], $row['max_qty'],),
                    );
                }
                $answer = array(
                    'page' => '1',
                    'total' => $bundle['subitems']->count(),
                    'rows' => $rows,
                );
                break;
            }
        }
        echo Zend_Json::encode($answer);
    }



    /**
     * edit existing variant of attribute
     */
    public function ajaxEditSubitemAction()
    {
        $serviceBundle = Model_Service::factory('catalog/item-bundle');
        $serviceSubitem = Model_Service::factory('catalog/subitem');
        $bundleId = $this->_getParam('row_id');
        $rowId = $this->_getParam('id');
        $values = $this->getRequest()->getParams();
        $answer = array();
        foreach ($this->getHelper('AdminItem')->session()->editingBundles as $bundle) {
            if ($bundle['hash'] == $bundleId) {
                $found = FALSE;
                foreach ($bundle['subitems'] as $sub) {
                    if ($sub['hash'] == $rowId) {
                        $serviceSubitem->copyFromItem($values['item_id'], $sub);
                        $sub['alias'] = $values['alias'];
                        $sub['price'] = $values['price'];
                        $sub['param1'] = $values['param1'];
                        $sub['min_qty'] = $values['min_qty'];
                        $sub['max_qty'] = $values['max_qty'];
                        $found = TRUE;
                        $answer = $sub;
                        break;
                    }
                }
                if ( ! $found) {
                    $new = $serviceSubitem->createFromValues(array(
                        'id'      => $values['item_id'],
                        'alias'   => $values['alias'],
                        'price'   => $values['price'],
                        'param1'   => $values['param1'],
                        'min_qty' => $values['min_qty'],
                        'max_qty' => $values['max_qty'],
                    ));
                    $bundle['subitems']->add($new);
                    $answer = $new;
                }
                break;
            }
        }

        echo Zend_Json::encode($answer);
    }

    /**
     * delete variant of attribute
     */
    public function ajaxDeleteSubitemAction()
    {
        $rowId = $this->_getParam('row_id');
        $subrows = $this->_getParam('subrows');
        foreach ($this->getHelper('AdminItem')->session()->editingBundles as $key=>$bundle) {
            if ($bundle['hash'] == $rowId) {
                foreach ($subrows as $subrow) {
                    $bundle['subitems']->removeByElement('hash', $subrow);
                }
            }
        }
        echo 'ok';
    }

    
    /**************************    items      *****************************/ 
    
    public function ajaxGetItemsAction()
    {
        $service = Model_Service::factory('catalog/subitem');
        $page = $this->_getParam('page');        
        
        $search = array();
        if ($this->_getParam('_search')=='true') {
            if ($q = $this->_getParam('sku')) {
                $search['sku'] = $q;
            }
            if ($q = $this->_getParam('spec_as_html')) {
                $search['name'] = $q;
            }
        }
        
        $order = array();
        $sortOrder = $this->_getParam('sord');
        if (($sortField = $this->_getParam('sidx')) AND ( ! empty($sortField))) {
            switch($sortField) {
                case 'sku': 
                case 'price':
                    $order[$sortField] = $sortOrder;
                    break;
                case 'spec_as_html':
                    $order['name'] = $sortOrder;
                    break;
            }
        }
        
        $data = $service->paginatorGetAllAvailable($search, $order, $this->getHelper('RowsPerPage')->saveValue()->getValue(), $page);
        $rows = array();
        foreach ($data as $row) {
            $rows[] = array(
                'id' => $row['id'],
                'cell' => array($row['id'], $row['sku'], $row['spec_as_text'], $row['price']),
            );
            
        }
        $answer = array(
            'page' => max( (int) $page, 1),
            'total' => $data->count(),
            'rows' => $rows,
        );
        echo Zend_Json::encode($answer);
    }


    protected function _getBundleSortingHtml(Model_Object_Iterface $bundle)
    {
        $html = $this->view->adminListSorting(array(
            'class'=>'bundle-sorting', 
            'htmlAttribs'=>'onclick="return bundleSortingClick(this);"',
            'hrefFirst' => $this->view->stdUrl(array('position'=>'first', 'hash'=>$bundle->hash), 'ajax-change-bundle-sorting', 'admin-item_bundle', 'catalog'),
            'hrefPrev'  => $this->view->stdUrl(array('position'=>'prev',  'hash'=>$bundle->hash), 'ajax-change-bundle-sorting', 'admin-item_bundle', 'catalog'),
            'hrefNext'  => $this->view->stdUrl(array('position'=>'next',  'hash'=>$bundle->hash), 'ajax-change-bundle-sorting', 'admin-item_bundle', 'catalog'),
            'hrefLast'  => $this->view->stdUrl(array('position'=>'last',  'hash'=>$bundle->hash), 'ajax-change-bundle-sorting', 'admin-item_bundle', 'catalog'),
        ));
        
        return $html;
    }
    
    protected function _getSubitemSortingHtml(Model_Object_Iterface $sub, Model_Object_Iterface $bundle)
    {
        $html = $this->view->adminListSorting(array(
            'class'=>'subitem-sorting', 
            'htmlAttribs'=>'onclick="return subitemSortingClick(this);" bundle="'.$bundle->hash.'"',
            'hrefFirst' => $this->view->stdUrl(array('position'=>'first', 'hash'=>$sub->hash, 'bundle'=>$bundle->hash), 'ajax-change-subitem-sorting', 'admin-item_bundle', 'catalog'),
            'hrefPrev'  => $this->view->stdUrl(array('position'=>'prev',  'hash'=>$sub->hash, 'bundle'=>$bundle->hash), 'ajax-change-subitem-sorting', 'admin-item_bundle', 'catalog'),
            'hrefNext'  => $this->view->stdUrl(array('position'=>'next',  'hash'=>$sub->hash, 'bundle'=>$bundle->hash), 'ajax-change-subitem-sorting', 'admin-item_bundle', 'catalog'),
            'hrefLast'  => $this->view->stdUrl(array('position'=>'last',  'hash'=>$sub->hash, 'bundle'=>$bundle->hash), 'ajax-change-subitem-sorting', 'admin-item_bundle', 'catalog'),
        ));
        return $html;
    }
    
    /**
     * change sorting of bundles in collection
     */
    public function ajaxChangeBundleSortingAction()
    {
        $pos = $this->_getParam('position');
        $hash = $this->_getParam('hash');
        $coll = $this->getHelper('AdminItem')->session()->editingBundles;
        $index = $coll->findOneIndexByHash($hash);
        $coll->changeSorting($index, $pos);
        echo 'ok';
    }    
    
    /**
     * change sorting of bundles in collection
     */
    public function ajaxChangeSubitemSortingAction()
    {
        $pos = $this->_getParam('position');
        $hash = $this->_getParam('hash');
        $bundleHash = $this->_getParam('bundle');
        $bundle = $this->getHelper('AdminItem')->session()->editingBundles->findOneByHash($bundleHash);
        $index = $bundle->subitems->findOneIndexByHash($hash);
        $bundle->subitems->changeSorting($index, $pos);
        echo 'ok';
    }    
    
}
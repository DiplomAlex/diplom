<?php

class Catalog_Model_Service_Subitem extends Model_Service_Abstract
{
    
    protected $_defaultInjections = array(
        'Model_Object_Interface' => 'Catalog_Model_Object_Subitem',
        'Model_Collection_Interface' => 'Catalog_Model_Collection_Subitem',
        'Model_Mapper_Interface' => 'Catalog_Model_Mapper_Db_Subitem',
    );
    
    public function init()
    {
        $lang = Model_Service::factory('language');
        $this->getMapper()->getPlugin('Description')->setLanguages($lang->getAllActive())->setCurrentLanguage($lang->getCurrent());
    }
    
    
    public function createFromValues(array $values)
    {
        $sub = $this->createFromItem($values['id']);
        $sub->alias = $values['alias'];
        $sub->param1 = $values['param1'];
        $sub->param2 = $values['param2'];
        $sub->param3 = $values['param3'];
        return $sub;
    }
    
    public function createFromItem($id)
    {
        $itemService = Model_Service::factory('catalog/item');
        $sub = $this->create();
        if ($id) {
            $item = $itemService->getComplex($id);
            foreach ($sub->getElements() as $el=>$val) {
                if ($item->hasElement($el)) {
                    $sub->{$el} = $item->{$el};
                }
            }
            /*$sub->price = $itemService->calculatePrice($item);*/
        }
        $specs = $itemService->getAttributesSpecification($item);
        $sub->attributes = $specs['array'];
        $sub->attributes_text = $specs['text'];
        $sub->attributes_html = $specs['html'];
        return $sub;
    }
    
    public function copyFromItem($itemId, $sub)
    {
        $itemService = Model_Service::factory('catalog/item');
        if ($itemId) {
            $item = $itemService->getComplex($itemId);
            foreach ($sub->getElements() as $el=>$val) {
                if ($item->hasElement($el)) {
                    $sub->{$el} = $item->{$el};
                }
            }
        }
        else {
            $item = $itemService->create();
        }
        $specs = $itemService->getAttributesSpecification($item);
        $sub->attributes = $specs['array'];
        $sub->attributes_text = $specs['text'];
        $sub->attributes_html = $specs['html'];
        $sub->price = $itemService->calculatePrice($item);
        return $sub;
    }
    
    public function paginatorGetAllAvailable(array $search = NULL, array $order = NULL, $rowsPerPage = NULL, $page = '1')
    {
        return $this->getMapper()->paginatorFetchAllAvailable($search, $order, $rowsPerPage, $page);
    }
    
    /**
     * 
     * @param $xml
     * @return array
     */
    public function getAttributesSpecification($attrs)
    {
        return Model_Service::factory('catalog/item')->getAttributesSpecification($attrs);
    }
    
}
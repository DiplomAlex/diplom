<?php

class Api_Model_Service_Remain extends Model_Service_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface'     => 'Api_Model_Object_Remain',
        'Model_Mapper_Interface'     => 'Api_Model_Mapper_Db_Remain',
        'Model_Collection_Interface' => 'Api_Model_Collection_Remain',
        'Catalog_Model_Service_Item'
    );

    /**
     * Proxy for IDE
     *
     * @param null $name
     * @return Api_Model_Mapper_Db_Remain
     */
    public function getMapper($name = null)
    {
        return parent::getMapper($name);
    }

    /**
     * Добавить/обновить запись остатка
     *
     * @param SimpleXMLElement $remains
     * @return int
     */
    public function setRemains(SimpleXMLElement $remains)
    {
        $this->getMapper()->clearRemains();
        $codes = $this->getMapper()->fetchDistinctField('code');

        foreach ($remains->remain as $remain) {
            $remain = $this->_prepareToSetRemainValues($remain);

            if (in_array($remain['remain_code'], $codes)) {
                $this->getMapper()->poolUpdate('remain', $remain, array('remain_code = ?' => $remain['remain_code']));
            } else {
                $this->getMapper()->poolInsert('remain', $remain);
            }
        }

        $this->getMapper()->poolUpdate('remain');
        $this->getMapper()->poolInsert('remain');

        return $this->getMapper()->getPoolUpdateCounter() + $this->getMapper()->getPoolInsertCounter();
    }

    /**
     * Подготовить значения остатков для сохранения
     *
     * @param SimpleXMLElement $remain
     * @return array
     */
    private function _prepareToSetRemainValues(SimpleXMLElement $remain)
    {
        $remain = (array) $remain;
        $prefix = $this->getMapper()->getTable()->getColumnPrefix() . '_';

        $remain['size'] = App_Utf8::strip_non_ascii($remain['size']);
        $remain['size'] = str_replace(' ', '', $remain['size']);
        $remain['size'] = str_replace(',', '.', $remain['size']);
        $remain['size'] = round((float)$remain['size'], 6);
        $remain['size'] = $remain['size'] != 0 ? $remain['size']: null;

        $remain['weight'] = App_Utf8::strip_non_ascii($remain['weight']);
        $remain['weight'] = str_replace(' ', '', $remain['weight']);
        $remain['weight'] = str_replace(',', '.', $remain['weight']);
        $remain['weight'] = round( (float) $remain['weight'], 6);

        $remain['price'] = App_Utf8::strip_non_ascii($remain['price']);
        $remain['price'] = str_replace(' ', '', $remain['price']);
        $remain['price'] = str_replace(',', '.', $remain['price']);
        $remain['price'] = round( (float) $remain['price'], 2);

        $remain['in_stock'] = isset($remain['in_stock']) ? (int) ((bool) $remain['in_stock']) : 1;

        foreach ($remain as $key => $value) {
            $remain[$prefix . $key] = $value ? trim($value) : $value;
            unset($remain[$key]);
        }

        return $remain;
    }

    /**
     * Получить массив остатков
     *
     * @return array
     */
    public function getAllRemainsArray()
    {
        $remains = $this->getMapper()->fetchAllRemainsArray();
        foreach ($remains as &$remain) $remain = $this->_prepareToGetRemainValues($remain);
        /* во избежание кофликтов имен */
        unset($remain);

        return $remains;
    }

    /**
     * Подготовить значения остатков для выгрузки
     * 
     * @param array $remain
     * @return array
     */
    private function _prepareToGetRemainValues(array $remain)
    {
        if ($remain['size']) $remain['size'] = number_format($remain['size'], 2, ',', ' ');
        if ($remain['weight']) $remain['weight'] = number_format($remain['weight'], 2, ',', ' ');
        $remain['price'] = number_format($remain['price'], 2, ',', ' ');
        $remain['in_stock'] = (int) $remain['in_stock'] ? 'true' : 'false';

        return $remain;
    }

    /**
     * Получить paginator остатков по артикулу
     *
     * @param $sku
     * @param $rowsPerPage
     * @param $page
     * @return mixed
     */
    public function paginatorRemainsBySku($sku, $rowsPerPage, $page)
    {
        $select = $this->getMapper()->fetchComplex(array('remain_sku = ?' => $sku), false);

        return $this->getMapper()->paginator($select, $rowsPerPage, $page);
    }

    /**
     * Получить значения фильтра по остаткам и категии
     *
     * @param string $column
     * @param array  $sku
     * @return array
     */
    public function getFilterValues($column, $sku)
    {
        return $this->getMapper()->fetchFilterValues($column, $sku);
    }

    /**
     * Get remains by seo_id
     *
     * @param $sku
     *
     * @return Model_Collection_Interface
     */
    public function getRemainsBySeoId($sku)
    {
        return $this->getMapper()->fetchRemainsBySku($sku);
    }

}
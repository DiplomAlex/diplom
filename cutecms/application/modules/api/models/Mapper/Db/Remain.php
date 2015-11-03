<?php

class Api_Model_Mapper_Db_Remain extends Model_Mapper_Db_Abstract
{

    protected $_defaultInjections = array(
        'Model_Object_Interface'     => 'Api_Model_Object_Remain',
        'Model_Db_Table_Interface'   => 'Api_Model_Db_Table_Remain',
        'Model_Collection_Interface' => 'Api_Model_Collection_Remain'
    );

    /**
     * Получить массив остатков
     *
     * @return array
     */
    public function fetchAllRemainsArray()
    {
        return $this->getTable()->select()->from(
            $this->getTable(), array(
                'sku'             => 'remain_sku',
                'code'            => 'remain_code',
                'material'        => 'remain_material',
                'probe'           => 'remain_probe',
                'size'            => 'remain_size',
                'characteristics' => 'remain_characteristics',
                'weight'          => 'remain_weight',
                'price'           => 'remain_price',
                'in_stock'        => 'remain_in_stock'
            )
        )->query()->fetchAll();
    }

    /**
     * Очистка наличия остатков
     */
    public function clearRemains()
    {
        $this->getTable()->update(array('remain_in_stock' => '0'), 1);
    }

    /**
     * Fetch remains by sku
     *
     * @param $sku
     *
     * @return Model_Collection_Interface
     */
    public function fetchRemainsBySku($sku)
    {
        if ($sku != null) {
            $select = $this->getTable()->select()
                ->where('remain_sku = ?', $sku)
                ->order('remain_in_stock DESC')
                ->order('remain_price DESC');

            return $this->makeComplexCollection($select->query()->fetchAll());
        } else {
            return $this->makeComplexCollection(array());
        }
    }

    /**
     * Получить значения фильтра по остаткам и категии
     *
     * @param string $column
     * @param array  $sku
     * @return array
     */
    public function fetchFilterValues($column, $sku)
    {
        $result = array();

        if (!empty($sku)) {
            $data = $this->fetchComplex(array('remain_sku IN (?)' => $sku, 'remain_' . $column . '<> ?' => ''), false)
                ->order('remain_' . $column . ' ASC')->query()->fetchAll();

            foreach ($data as $row) {
                $result[$row['remain_' . $column]] = $row['remain_' . $column];
            }
        }

        return $result;
    }
}
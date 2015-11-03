<?php

class Catalog_Model_Mapper_Db_Plugin_Filter_Item extends Model_Mapper_Db_Plugin_Filter_Abstract
{

    protected static $_attrCode = null;

    public function setAttribute($code)
    {
        self::$_attrCode = $code;
    }

    protected function _filter_name(Zend_Db_Table_Select $select, $filterName, $value)
    {
        $select->where('item_desc_name LIKE ?', '%' . $value . '%');

        return $select;
    }

    protected function _filter_sku(Zend_Db_Table_Select $select, $filterName, $value)
    {
        $select->where('item_sku LIKE ?', '%' . $value . '%');

        return $select;
    }

    protected function _filter_seo_id(Zend_Db_Table_Select $select, $filterName, $value)
    {
        $select->where('item_seo_id LIKE ?', '%' . $value . '%');

        return $select;
    }

    protected function _filter_is_new(Zend_Db_Table_Select $select, $filterName, $value)
    {
        if ($value) {
            $select->where('item_is_new = ?', $value);
        }

        return $select;
    }

    protected function _filter_is_popular(Zend_Db_Table_Select $select, $filterName, $value)
    {
        if ($value) {
            $select->where('item_is_popular = ?', $value);
        }

        return $select;
    }

    protected function _filter_main_page_slider(Zend_Db_Table_Select $select, $filterName, $value)
    {
        if ($value) {
            $select->where('item_main_page_slider = ?', $value);
        }

        return $select;
    }

    protected function _filter_main_left_baner(Zend_Db_Table_Select $select, $filterName, $value)
    {
        if ($value) {
            $select->where('item_main_left_baner = ?', $value);
        }

        return $select;
    }

    protected function _filter_attribute(Zend_Db_Table_Select $select, $filterName, $value)
    {
        $code = self::$_attrCode;
        if (empty($code)) {
            throw new Model_Mapper_Db_Plugin_Exception('For filter_attribute $service->setFilterParams() should be called first in controller');
        }

        /*<collection>
            .........
            <object class="Catalog_Model_Object_Attribute">
                ..........
                <code><![CDATA[size]]></code>
                ..........
                <value_variant><![CDATA[XXL]]></value_variant>
                ..........
            </object>
            .........
          </collection>
         */

        $select->where('EXTRACTVALUE(item_attributes_xml, '
            . '\'//collection/object[@class="Catalog_Model_Object_Attribute"]'
            . '/code[text()="' . $code . '"]'
            . '/../current_value/text()\') LIKE ?', /* '%'. */ $value/* .'%' */);

        return $select;
    }

    /**
     * Фильтр по цене из остатков
     *
     * @param Zend_Db_Table_Select $select
     * @param                      $filterName
     * @param                      $value
     * @param array                $params
     * @return Zend_Db_Table_Select
     */
    protected function _filter_price(Zend_Db_Table_Select $select, $filterName, $value, array $params = NULL)
    {
        if (array_key_exists('filter_price_min', $params) and ($minPrice = floatval($params['filter_price_min']))) {
            $select->where('remain_price >= ?', $minPrice);
        }
        if (array_key_exists('filter_price_max', $params) and ($maxPrice = floatval($params['filter_price_max']))) {
            $select->where('remain_price <= ?', $maxPrice);
        }

        return $select;
    }

    protected function _filter_manufacturer(Zend_Db_Table_Select $select, $filterName, $value, array $params = NULL)
    {
        $select->where('manufacturer_desc_name LIKE ?', '%' . $value . '%');

        return $select;
    }

    /**
     * bundle products in filters example :

      protected $_extenderCode = NULL;

      public function setExtenderCode($code)
      {
      $this->_extenderCode = $code;
      return $this;
      }

      protected function _getExtenderCode()
      {
      if ($this->_extenderCode === NULL) {
      $this->_throwException('code of bundle named "extender" should be set in init() method of service (corresponding to this mapper)');
      }
      return $this->_extenderCode;
      }

      protected function _filter_automate(Zend_Db_Table_Select $select, $filterName, $value)
      {
      $lang = $this->getMapper()->getPlugin('Description')->getCurrentLanguage();
      $select->joinLeft(array('subitemref'=>'item_bundle_subitem_ref'), 'item_id = bs_ref_subitem_id', array())
      ->joinLeft(array('autodesc'=>'item_description'), 'autodesc.item_desc_item_id = bs_ref_item_id AND autodesc.item_desc_language_id = '.$lang->id, array())
      ->where('autodesc.item_desc_name LIKE ?', '%'.$value.'%')
      ;
      return $select;
      }

      protected function _filter_extender(Zend_Db_Table_Select $select, $filterName, $value)
      {
      $lang = $this->getMapper()->getPlugin('Description')->getCurrentLanguage();
      $select->distinct(TRUE)
      ->joinLeft(array('bundle'=>'item_bundle'), 'bundle_item_id = item_id AND bundle_code = \''.$this->_getExtenderCode().'\'', array())
      ->joinLeft(array('subitemref'=>'item_bundle_subitem_ref'), 'bundle_id = bs_ref_bundle_id', array())
      ->joinLeft(array('subitemdesc'=>'item_description'), 'subitemdesc.item_desc_item_id = bs_ref_subitem_id AND subitemdesc.item_desc_language_id = '.$lang->id, array())
      ->where('subitemdesc.item_desc_name LIKE ?', '%'.$value.'%')
      ;
      return $select;
      }

     */

    protected function _filter_active(Zend_Db_Table_Select $select, $filterName, $value, array $params = null)
    {
        $keyFild = 'filter_attr_';
        $service = Model_Service::factory('catalog/item-search');

        $columns = $service->getColumnList();
        $table = $service->getMapper()->getTable();
        $prefix = $table->getColumnPrefix() . $table->getPrefixSeparator();

        foreach ($params as $key => $value) {
            $value = trim($value);
            $find = strripos($key, $keyFild);
            if ($find !== false and !empty($value)) {
                $id = (int) substr($key, -(strlen($key) - strlen($keyFild)));
                if (is_numeric($id) and in_array($prefix . $id, $columns)  ) {
                    $select->where($prefix . $id . ' = ?', $value);
                }
            }
        }

        return $select;
    }

    /**
     * Фильтр по материалу из остатков
     *
     * @param Zend_Db_Table_Select $select
     * @param                      $filterName
     * @param                      $value
     * @return Zend_Db_Table_Select
     */
    protected function _filter_material(Zend_Db_Table_Select $select, $filterName, $value)
    {
        $select->where('remain_material = ?', $value);

        return $select;
    }

    /**
     * Фильтр по пробе из остатков
     *
     * @param Zend_Db_Table_Select $select
     * @param                      $filterName
     * @param                      $value
     * @return Zend_Db_Table_Select
     */
    protected function _filter_probe(Zend_Db_Table_Select $select, $filterName, $value)
    {
        $select->where('remain_probe = ?', $value);

        return $select;
    }

    /**
     * Фильтр по размеру из остатков
     *
     * @param Zend_Db_Table_Select $select
     * @param                      $filterName
     * @param                      $value
     * @return Zend_Db_Table_Select
     */
    protected function _filter_size(Zend_Db_Table_Select $select, $filterName, $value, array $params = NULL)
    {
        $select->where('remain_size = ?', $value);

        return $select;
    }

}
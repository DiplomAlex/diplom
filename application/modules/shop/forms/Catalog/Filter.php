<?php

class Shop_Form_Catalog_Filter extends App_Form
{

    const MIN_PRICE = 0;
    const MAX_PRICE = 300000;

    protected $_attributes = null;
    protected $_sku = null;

    public function __construct($options = null)
    {
        if (isset($options['attributes']) and $options['attributes'] instanceof Catalog_Model_Collection_Attribute) {
            $this->_attributes = $options['attributes'];
        }

        parent::__construct();
    }

    public function init()
    {
        $this->addElement('hidden', 'filter_active', array('value' => 1));
        $this->addElement('hidden', 'filter_price_change', array('value' => 0));

        $this->addElement('select', 'filter_material', array(
            'label' => $this->getTranslator()->_('Материал'),
            'attribs' => array(
                'class' => 'custom_select',
            ),
        ));
        $this->filter_material->setMultiOptions($this->_prepareListOfRemains('material'));

        $this->addElement('select', 'filter_probe', array(
            'label' => $this->getTranslator()->_('Проба'),
            'attribs' => array(
                'class' => 'custom_select',
            ),
        ));
        $this->filter_probe->setMultiOptions($this->_prepareListOfRemains('probe'));

        $listOfSize = $this->_prepareListOfRemains('size');
        $filters = 2;

        if (!empty($listOfSize)) {
            $this->addElement('select', 'filter_size', array(
                'label'   => $this->getTranslator()->_('Размер'),
                'attribs' => array(
                    'class' => 'custom_select',
                ),
            ));
            $this->filter_size->setMultiOptions($listOfSize);
            $filters = 1;
        }

        $i = 0;
        if ($this->_attributes !== null and $this->_attributes->count()) {

            foreach ($this->_attributes as $attribute) {
                if ($attribute->status and $i < $filters) {
                    $this->addElement('select', 'filter_attr_' . $attribute->id, array(
                        'label' => $this->getTranslator()->_($attribute->name),
                        'attribs' => array(
                            'class' => 'custom_select',
                        ),
                    ));
                    $this->{'filter_attr_' . $attribute->id}->setMultiOptions($this->_prepareList($attribute->variants));
                }
                $i++;
            }
        }

        $this->addElement('text', 'filter_price_min', array(
            'attribs' => array(
                'id' => 'minCost',
            ),
            'value'   => self::MIN_PRICE
        ));

        $this->addElement('text', 'filter_price_max', array(
            'attribs' => array(
                'id' => 'maxCost',
            ),
            'value'   => self::MAX_PRICE
        ));
    }

    /**
     * Подготовить список значений фильтра атрибутов
     *
     * @param Catalog_Model_Collection_AttributeVariant $attributeVariants
     * @return array
     */
    protected function _prepareList(Catalog_Model_Collection_AttributeVariant $attributeVariants)
    {
        $list = array();
        foreach ($attributeVariants as $attributeVariant) {
            $list[] = $this->getTranslator()->_($attributeVariant->value);
        }

        return $list;
    }

    /**
     * Подготовить список значений фильтра остатка
     *
     * @param string $name
     * @return array
     */
    protected function _prepareListOfRemains($name)
    {
        $seoId =  Zend_Controller_Front::getInstance()->getRequest()->getParam('seo_id');

        if ($seoId && !$this->_sku) {
            $categoryService = Model_Service::factory('catalog/category');
            try {
                $category = $categoryService->getComplexBySeoId($seoId);
                $categoryChild = $categoryService->getAllActiveByParent($category->seo_id);
                $sku = array();

                $categoryIds[] = $category->id;
                foreach ($categoryChild as $value) {
                    $categoryIds[] = $value->id;
                }
                foreach (Model_Service::factory('catalog/item')->getSkuByCategory($categoryIds) as $rowChild) {
                    $sku[] = $rowChild['item_sku'];
                }

                $this->_sku = $sku;
            } catch (Exception $e) {
                $this->_sku = array();
            }
        }

        return Model_Service::factory('api/remain')->getFilterValues($name, $this->_sku);
    }

}
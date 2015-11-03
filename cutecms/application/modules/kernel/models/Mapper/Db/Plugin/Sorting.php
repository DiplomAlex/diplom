<?php

class Model_Mapper_Db_Plugin_Sorting extends Model_Mapper_Db_Plugin_Abstract
{


    protected $_hasTable = FALSE;

    protected $_positions = array(
        'first', 'prev', 'next', 'last',
    );
    
    protected $_sortModes = NULL;


    public function __construct(array $sortModes = NULL)
    {
        /*App_Debug::dump($sortModes, 'in plugin');*/
        if ($sortModes !== NULL) {
            $this->setSortingModes($sortModes);
        }
    }
    
    public function setSortingModes(array $modes)
    {
        $this->_sortModes = $modes;
        return $this;
    }
    
    public function getSortingModes()
    {
        return $this->_sortModes;
    }
    
    /**
     * get order and directions of all modes
     * @return array as array (modeName=>direciton, ...)
     */
    public function getSortingModesOrder()
    {
        $order = array();
        foreach ($this->_sortModes as $modeName=>$modeData) {
            if (is_array($modeData)) {
                $direction = array_shift($modeData);
            }
            else {
                $direction = 'ASC';
            }
            $order[$modeName] = $direction;
        }
        return $order;
    }
    
    /**
     * change order of modes and direction in each mode
     * @param array $modes as array(modeName => direciton, ...)
     * @return $this
     */
    public function setSortingModesOrder(array $modes)
    {
        $newModes = array();
        foreach ($modes as $modeName=>$value) {
            $modeDirection = $value;
            $modeData = $this->_sortModes[$modeName];
            if (is_array($modeData)) {
                if ($modeDirection !== NULL) {
                    foreach ($modeData as $field=>$dir) {
                        break;
                    }
                    $modeData[$field] = $modeDirection;
                }
            }
            else {
                if ($modeDirection === NULL) {
                    $modeDirection = 'ASC';
                }
                $modeData = array($modeData => $modeDirection);
            }
            $newModes[$modeName] = $modeData;
        }
        $this->_sortModes = $newModes;
        return $this;
    }
    
    public function setCurrentSortingMode($modeName, $direction = NULL)
    {
        if ($direction === NULL) {
            $direction = 'ASC';
        }
        $mode = $this->_sortModes[$modeName];
        if (is_array($mode)) {
            foreach ($mode as $key=>$val) {
                $mode[$key] = $direction;
            }
        }
        else {
            if ( ! empty($mode)) {
                $mode = array($mode=>$direction);
            }
        }
        unset($this->_sortModes[$modeName]);
        if ( ! empty($mode)) {
            $newModes = array($modeName=>$mode);
            if ( ! empty($this->_sortModes)) {
                $newModes = array_merge($newModes, $this->_sortModes);                
            }
            $this->_sortModes = $newModes;
        }
        return $this;
    }
    
    public function getCurrentSortingMode()
    {
        $modeName = NULL;
        foreach ($this->_sortModes as $modeName=>$modeData) {
            break;
        }
        return $modeName;
    }
    
    public function getSortingDirection($modeName)
    {
        $modeData = $this->_sortModes[$modeName];
        if (is_array($modeData)) {
            $dir = array_shift($modeData);
        }
        else {
            $dir = 'ASC';
        }
        return $dir;
    }

    /**
     * add sorting
     * @param Zend_Db_Select
     * @return Zend_Db_Select
     */
    public function onFetchComplex(Zend_Db_Select $select)
    {
        
        if (($sortModes = $this->getSortingModes()) AND ( ! empty($sortModes))) {
            foreach ($sortModes as $modeName=>$modeData) {
                if (is_array($modeData)) {
                    foreach ($modeData as $ordField=>$ordDir) {
                        $select->order($ordField.' '.$ordDir);
                    }
                }
                else {
                    $select->order($modeData.' ASC');
                }
            }
        } 
        else {
            $table = $this->getMapper()->getTable();
            $prefix = $table->getColumnPrefix().$table->getPrefixSeparator();
            $select -> order($prefix.'sort ASC');
        }
        return $select;

    }


    /**
     * @param Model_Object_Interface
     * @param array
     * @param bool
     *
     */
    public function onAfterSave(Model_Object_Interface $obj,array $values, $isNew = FALSE)
    {
        if ($isNew === TRUE) {
            $this->changeSorting($obj, 'first');
        }
    }

    public function onBeforeDelete(Model_Object_Interface $obj)
    {
        if ($obj->sort != NULL) {
            $fieldSort = $this->getMapper()->getTable()->getColumnPrefix() .'_sort';
            $fieldId = $this->getMapper()->getTable()->getColumnPrefix() .'_id';
            $this->getMapper()->getTable()->update(
                                        array($fieldSort => new Zend_Db_Expr($fieldSort.'-1')),
                                        array(
                                            $fieldSort.' > ?' => $obj->sort,
                                        )
                                    );
        }
    }



    /**
     * change position of object in list (sort field)
     * @param Model_Object_Interface
     * @param string (first|last|prev|next)
     */
    public function changeSorting(Model_Object_Interface $obj, $position)
    {
        if ( ! in_array($position, $this->getPositions())) {
            throw new Model_Mapper_Db_Plugin_Exception('wrong position ("'.$position.'") to change sorting in '.__CLASS__);
        }
        $fieldSort = $this->getMapper()->getTable()->getColumnPrefix() .'_sort';
        $fieldId = $this->getMapper()->getTable()->getColumnPrefix() .'_id';
        switch ($position) {
            case 'first':
                if (($obj->sort > 0) OR ( ! isset($obj->sort)) OR ($obj->sort == NULL)) {

                    if (( ! isset($obj->sort)) OR ($obj->sort == NULL)) {
                        $obj->sort = 0;
                        $this->getMapper()->getTable()->update(
                                                    array($fieldSort => new Zend_Db_Expr($fieldSort.'+1')),
                                                    array('1=1')
                                                );
                    }
                    else {
                        $this->getMapper()->getTable()->update(
                                                    array($fieldSort => new Zend_Db_Expr($fieldSort.'+1')),
                                                    array(
                                                        $fieldSort.' < ?' => $obj->sort,
                                                    )
                                                );
                    }
                    $this->getMapper()->getTable()->update(
                                                    array($fieldSort => new Zend_Db_Expr('0')),
                                                    array(
                                                        $fieldId.' = ?' => $obj->id,
                                                    )
                                                );
                }
                break;
            case 'prev':
                if ($obj->sort > 0) {
                    $this->getMapper()->getTable()->update(
                                                array($fieldSort => new Zend_Db_Expr($fieldSort.'+1')),
                                                array(
                                                    $fieldSort.' = ?' => $obj->sort-1,
                                                )
                                            );
                    $this->getMapper()->getTable()->update(
                                                array($fieldSort => $obj->sort-1),
                                                array(
                                                    $fieldId.' = ?' => $obj->id,
                                                )
                                            );
                }

                break;
            case 'next':
                $rowMax = current($this->getMapper()->getTable()->select()
                                 ->from($this->getMapper()->getTable()->info('name'), array('mx'=>'max('.$fieldSort.')'))
                                 ->query()->fetchAll());
                $max = $rowMax['mx'];
                if ($obj->sort < $max) {
                    $this->getMapper()->getTable()->update(
                                                array($fieldSort => new Zend_Db_Expr($fieldSort.'-1')),
                                                array(
                                                    $fieldSort.' = ?' => $obj->sort+1,
                                                )
                                            );
                    $this->getMapper()->getTable()->update(
                                                array($fieldSort => $obj->sort+1),
                                                array(
                                                    $fieldId.' = ?' => $obj->id,
                                                )
                                            );
                }
                break;
            case 'last':
                $rowMax = current($this->getMapper()->getTable()->select()
                                 ->from($this->getMapper()->getTable()->info('name'), array('mx'=>'max('.$fieldSort.')'))
                                 ->query()->fetchAll());
                $max = $rowMax['mx'];
                if ($obj->sort < $max) {
                    $this->getMapper()->getTable()->update(
                                                array($fieldSort => new Zend_Db_Expr($fieldSort.'-1')),
                                                array(
                                                    $fieldSort.' > ?' => $obj->sort,
                                                )
                                            );
                    $this->getMapper()->getTable()->update(
                                                array($fieldSort => $max),
                                                array(
                                                    $fieldId.' = ?' => $obj->id,
                                                )
                                            );
                }
                break;
        }
        return $this;
    }


    /**
     * @return array
     */
    public function getPositions()
    {
        return $this->_positions;
    }

}
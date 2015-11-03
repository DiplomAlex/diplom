<?php

require_once 'App/Model/Collection/Interface.php';
require_once 'App/Model/Collection/Exception.php';
require_once 'App/Model/Object/Interface.php';

class Model_Collection_Abstract implements Model_Collection_Interface
{

    protected $_container = array();

    protected $_pointer = NULL;

    /**
     * @param array $data - init collection data
     */
    public function __construct(array $data = NULL)
    {
        if ($data !== NULL) {
            $this->addArray($data);
        }
    }


    /** ArrayAccess implementation start  */

    public function offsetSet($offset, $value) {
        $this->set($offset, $value);
    }


    public function offsetExists($offset) {
        return isset($this->_container[$offset]);
    }


    public function offsetUnset($offset) {
        $this->remove($offset);
    }


    public function offsetGet($offset) {
        return $this->get($offset);
    }

    /** ArrayAccess implementation end  */


    /**
     * appends object to the end of collection
     *
     * @param Model_Object_Interface $value
     * @return Model_Collection_Abstract $this
     */
    public function add(Model_Object_Interface $object)
    {
        $this->_container [] = $object;
        return $this;
    }

    /**
     * appends array of objects
     * @param array of Model_Object_Interface
     * @return Model_Collection_Abstract $this
     */
    public function addArray(array $objects)
    {
        foreach ($objects as $object) {
            $this->add($object);
        }
        return $this;
    }

    /**
     * sets object to offset
     *
     * @param int $offset
     * @param Model_Object_Interface $offset
     * @return Model_Collection_Abstract $this
     */
    public function set($offset, Model_Object_Interface $object)
    {
        $this->_container[$offset] = $object;
        return $this;
    }

    /**
     * removes object from collection
     *
     * @param int $offset
     * @return Model_Collection_Abstract $this
     */
    public function remove($offset)
    {
        array_splice($this->_container, $offset, 1);
        return $this;
    }

    /**
     * gets object by offset
     *
     * @param int $offset
     * @return Model_Object_Interface
     */
    public function get($offset)
    {
        if (isset($this->_container[$offset])) {
            $object = $this->_container[$offset];
        }
        else {
            throw new Model_Collection_Exception('no offset '.$offset.' in collection ('.get_class($this).')');
        }
        return $object;
    }

    /**
     * return as array
     *
     * @return array of Model_Object_Interface
     */
    public function toArray()
    {
        $result = array();
        foreach ($this->_container as $key=>$obj) {
            $result []= $obj->toArray();
        }
        return $result;
    }

    /**
     * magic function to convert to array
     */
    public function __toArray()
    {
        return $this->toArray();
    }

    /**
     * counts objects in collection
     *
     * @return int
     */
    public function count()
    {
        return count($this->_container);
    }

    /**
     * checks if collection is empty
     */
    public function isEmpty()
    {
        return ! (bool) $this->count();
    }


    /**
     * returns current object in collection
     * @return Model_Object_Interface
     */
    public function current()
    {
        if ($this->isEmpty()) {
            $this->_throwException('method current() cannot be applied to empty collection');
        }
        $this->_pointer = (int) $this->_pointer;
        return $this->_container[$this->_pointer];
    }

    /**
     * returns next object in collection
     * @return Model_Object_Interface
     */
    public function next()
    {
        $this->_pointer = (int) $this->_pointer;
         ++ $this->_pointer;
        if ($this->valid()) {
            return $this->_container[$this->_pointer];
        }
        else {
            return FALSE;
        }
    }

    /**
     * returns prev object in collection
     * @return Model_Object_Interface
     */
    public function prev()
    {
        $this->_pointer = (int) $this->_pointer;
        -- $this->_pointer;
        if ($this->valid()) {
            return $this->_container[$this->_pointer];
        }
        else {
            return FALSE;
        }
    }

    /**
     * returns last object in collection
     * @return Model_Object_Interface
     */
    public function last()
    {
        $this->_pointer = count($this->_container) - 1;
        return $this->_container[$this->_pointer];
    }

    /**
     * returns first object in collection
     * @return Model_Object_Interface
     */
    public function rewind()
    {
        $this->_pointer = 0;
        return @$this->_container[$this->_pointer];
    }

    /**
     * returns key of current element
     * @return int
     */
    public function key()
    {
        return $this->_pointer;
    }

    /**
     * part of Iterator interface
     * Checks if current position is valid
     * @return bool
     */
    public function valid()
    {
        return $this->offsetExists($this->key());
    }

    /**
     * reverse objects order in collection
     * @return $this
     */
    public function reverse()
    {
        $this->_container = array_reverse($this->_container);
        return $this;
    }

    protected function _throwException($message)
    {
        throw new Model_Collection_Exception(get_class($this).' says: '.$message);
    }

    public function findByElement($elName, $elValue, $findOne = FALSE, $findIndex = FALSE)
    {
        $found = array();
        $i = 0;
        foreach ($this as $obj) {
            if ($obj->{$elName} == $elValue) {
                if ($findOne === TRUE) {
                    if ($findIndex) {
                        return $i;
                    }
                    else {
                        return $obj;
                    }
                }
                else {
                    if ($findIndex) {
                        $found []= $i;
                    }
                    else {
                        $found []= $obj;
                    }
                }
            }
            ++ $i;
        }
        if (empty($found)) return FALSE;
        return $found;
    }

    /**
     * @TODO should be carefully tested!!!! (looks like contains error)
     * removes ONLY the 1st element found
     */
    public function removeByElement($elName, $elValue)
    {
        foreach ($this->_container as $key=>$obj) {
            if ($obj->{$elName} == $elValue) {
                $this->remove($key);
                break;
            }
        }
        return $this;
    }
    
    public function clean()
    {
        $this->_container = array();
        $this->_pointer = NULL;
        return $this;
    }


    public function __call($method, array $values)
    {
        if (substr($method, 0, 6) == 'findBy') {
            $elName = lcfirst(substr($method, 6));
            $result = $this->findByElement($elName, $values[0]);
        }
        else if (substr($method, 0, 9) == 'findOneBy') {
            $elName = lcfirst(substr($method, 9));
            $result = $this->findByElement($elName, $values[0], TRUE);
        }
        else if (substr($method, 0, 11) == 'findIndexBy') {
            $elName = lcfirst(substr($method, 11));
            $result = $this->findByElement($elName, $values[0], FALSE, TRUE);
        }
        else if (substr($method, 0, 14) == 'findOneIndexBy') {
            $elName = lcfirst(substr($method, 14));
            $result = $this->findByElement($elName, $values[0], TRUE, TRUE);
        }        
        else if (substr($method, 0, 8) == 'removeBy') {
            $elName = lcfirst(substr($method, 8));
            $result = $this->removeByElement($elName, $values[0]);
        }
        else {
            $this->_throwException('method not found: '.$method);
        }
        return $result;
    }
    
    /**
     * change sorting order of elements
     * @param int $index - current index
     * @param $position
     */
    public function changeSorting($index, $position)
    {        
        $count = $this->count();
        $data = $this->_container;        
        $curr = $data[$index];
        if (($position == 'first') AND ($index > 0)) {
            unset($data[$index]);
            array_unshift($data, $curr);
            $this->_container = array_values($data);
        }
        else if (($position == 'prev') AND ($index > 0)) {
            $data[$index] = $data[$index-1];
            $data[$index-1] = $curr;
            $this->_container = $data;
        }
        else if (($position == 'next') AND ($index < $count-1)) {
            $data[$index] = $data[$index+1];
            $data[$index+1] = $curr;
            $this->_container = $data;
        }
        else if (($position == 'last') AND ($index < $count-1)) {
            unset($data[$index]);
            array_push($data, $curr);
            $this->_container = array_values($data);
        }        
        return $this;
    }


}
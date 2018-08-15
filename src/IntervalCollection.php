<?php

class IntervalCollection implements ArrayAccess
{
    private $collection = [];

    public function toArray()
    {
        return $this->collection;
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->collection[] = $value;
        } else {
            $this->collection[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->collection[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->collection[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->collection[$offset]) ? $this->collection[$offset] : null;
    }

}
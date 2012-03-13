<?php

namespace Cloudcontrol\Entity;

use Cloudcontrol\Exception\BadMethodCallException;

class AbstractEntity implements \ArrayAccess
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException('This entity is read-only.');
    }

    public function offsetUnset($offset)
    {
        throw new BadMethodCallException('This entity is read-only.');
    }
}
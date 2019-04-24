<?php

namespace Solarium\Core\Query;

/**
 * Document base functionality, used by readonly and readwrite documents.
 */
abstract class AbstractDocument implements DocumentInterface, \IteratorAggregate, \Countable, \ArrayAccess
{
    /**
     * All fields in this document.
     *
     * @var array
     */
    protected $fields;

    abstract public function __set($name, $value): DocumentInterface;

    /**
     * Get field value by name.
     *
     * Magic access method for accessing fields as properties of this document
     * object, by field name.
     *
     * @param string $name
     *
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->fields[$name] ?? null;
    }

    /**
     * Check if field is set by name.
     *
     * Magic method for checking if fields are set as properties of this document
     * object, by field name. Also used by empty().
     *
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name): bool
    {
        return isset($this->fields[$name]);
    }

    /**
     * Get all fields.
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->fields);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->fields);
    }

    /**
     * ArrayAccess implementation.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }

    /**
     * ArrayAccess implementation.
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return null !== $this->__get($offset);
    }

    /**
     * ArrayAccess implementation.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->__set($offset, null);
    }

    /**
     * ArrayAccess implementation.
     *
     * @param mixed $offset
     *
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }
}

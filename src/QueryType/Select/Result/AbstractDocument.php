<?php

namespace Solarium\QueryType\Select\Result;

/**
 * Document base functionality, used by readonly and readwrite documents.
 */
abstract class AbstractDocument implements \IteratorAggregate, \Countable, \ArrayAccess
{
    /**
     * All fields in this document.
     *
     * @var array
     */
    protected $fields;

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
    public function __get(string $name)
    {
        if (!isset($this->fields[$name])) {
            return null;
        }

        return $this->fields[$name];
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
    public function __isset(string $name): bool
    {
        return isset($this->fields[$name]);
    }

    /**
     * Set field value.
     *
     * @param string      $name
     * @param string|null $value
     *
     * @return self
     */
    public function __set(string $name, string $value): self
    {
        $this->fields[$name] = $value;
        return $this;
    }

    /**
     * Unset field value.
     *
     * Magic method for removing fields by unsetting object properties
     *
     * @param string $name
     *
     * @return self
     */
    public function __unset(string $name): self
    {
        unset($this->fields[$name]);

        return $this;
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

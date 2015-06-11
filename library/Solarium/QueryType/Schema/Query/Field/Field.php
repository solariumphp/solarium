<?php

namespace Solarium\QueryType\Schema\Query\Field;


use Solarium\Core\ArrayableInterface;
use Solarium\Exception\RuntimeException;

class Field implements FieldInterface, \ArrayAccess {

    protected $name;
    protected $type;
    protected $default;
    protected $required;
    protected $indexed;
    protected $stored;
    protected $multiValued;

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = array()) {
        foreach ($attributes AS $key => $value)
            $this[$key] = $value;
    }

    /**
     * @param string|null $default
     * @return $this - Provides Fluent Interface
     */
    public function setDefault($default) {
        $this->default = $default;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDefault() {
        return $this->default;
    }

    /**
     * @param boolean|null $indexed
     * @return $this - Provides Fluent Interface
     */
    public function setIndexed($indexed) {
        $this->indexed = is_null($indexed) ? null : (bool) $indexed;
        return $this;
    }

    /**
     * @return boolean|null
     */
    public function isIndexed() {
        return $this->indexed;
    }

    /**
     * @param boolean|null $multiValued
     * @return $this - Provides Fluent Interface
     */
    public function setMultiValued($multiValued) {
        $this->multiValued = is_null($multiValued) ? null : (bool) $multiValued;
        return $this;
    }

    /**
     * @return boolean|null
     */
    public function isMultiValued() {
        return $this->multiValued;
    }

    /**
     * @param string $name
     * @return $this - Provides Fluent Interface
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param boolean $required
     * @return $this - Provides Fluent Interface
     */
    public function setRequired($required) {
        $this->required = is_null($required) ? null : (bool) $required;
        return $this;
    }

    /**
     * @return boolean|null
     */
    public function isRequired() {
        return $this->required;
    }

    /**
     * @param boolean|null $stored
     * @return $this - Provides Fluent Interface
     */
    public function setStored($stored) {
        $this->stored = is_null($stored) ? null : (bool) $stored;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isStored() {
        return $this->stored;
    }

    /**
     * @param string $type
     * @return $this - Provides Fluent Interface
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function castAsArray() {
        $attributes = array('name',
                            'type',
                            'default',
                            'required',
                            'indexed',
                            'stored',
                            'multiValued',
        );

        $output = array();

        foreach ($attributes AS $attribute) {
            if (!is_null($this->{$attribute})) {
                $output[$attribute] = $this->{$attribute};
            }
        }

        if (!array_key_exists('name', $output))
            throw new RuntimeException("The 'name' attribute is not defined.");

        if (!array_key_exists('type', $output))
            throw new RuntimeException(sprintf("The 'type' attribute is not defined for the field %s.", $this->getName()));

        return $output;
    }

    /**
     * @return string
     */
    public function __toString() {
        return (string) $this->getName();
    }

    /**
     * @see \ArrayAccess::offsetExists
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return method_exists($this, 'get' . $offset) || method_exists($this, 'is' . $offset) || (stripos($offset, 'is') === 0 && method_exists($this, $offset));
    }

    /**
     * @see \ArrayAccess::offsetGet
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset) {
        if (method_exists($this, 'get' . $offset))
            return call_user_func([$this, 'get' . $offset]);

        elseif (method_exists($this, 'is' . $offset))
            return call_user_func([$this, 'is' . $offset]);

        elseif (stripos($offset, 'is') === 0 && method_exists($this, $offset))
            return call_user_func([$this, $offset]);

        else
            return null;
    }

    /**
     * @see \ArrayAccess::offsetSet
     * @param mixed $offset
     * @param mixed $value
     * @return $this
     */
    public function offsetSet($offset, $value) {
        return method_exists($this, 'set' . $offset) ? call_user_func([$this, 'set' . $offset], $value) : $this;
    }

    /**
     * @see \ArrayAccess::offsetUnset
     * @param mixed $offset
     * @return $this
     */
    public function offsetUnset($offset) {
        return $this->offsetSet($offset, null);
    }

}
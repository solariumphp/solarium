<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 *
 * @copyright Copyright 2011 Bas de Nooijer <solarium@raspberry.nl>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */
namespace Solarium\QueryType\Schema\Query\Field;

use Solarium\Exception\RuntimeException;

/**
 * Class Field
 * @author Beno!t POLASZEK
 */
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
    public function __construct(array $attributes = array())
    {
        foreach ($attributes AS $key => $value) {
            $this[$key] = $value;
        }
    }

    /**
     * @param string|null $default
     * @return $this - Provides Fluent Interface
     */
    public function setDefault($default)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param boolean|null $indexed
     * @return $this - Provides Fluent Interface
     */
    public function setIndexed($indexed)
    {
        $this->indexed = (!is_null($indexed)) ? (bool) $indexed : null;

        return $this;
    }

    /**
     * @return boolean|null
     */
    public function isIndexed()
    {
        return $this->indexed;
    }

    /**
     * @param boolean|null $multiValued
     * @return $this - Provides Fluent Interface
     */
    public function setMultiValued($multiValued)
    {
        $this->multiValued = (!is_null($multiValued)) ? (bool) $multiValued : null;
        return $this;
    }

    /**
     * @return boolean|null
     */
    public function isMultiValued()
    {
        return $this->multiValued;
    }

    /**
     * @param string $name
     * @return $this - Provides Fluent Interface
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param boolean $required
     * @return $this - Provides Fluent Interface
     */
    public function setRequired($required)
    {
        $this->required = (!is_null($required)) ? (bool) $required : null;

        return $this;
    }

    /**
     * @return boolean|null
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param boolean|null $stored
     * @return $this - Provides Fluent Interface
     */
    public function setStored($stored)
    {
        $this->stored = (!is_null($stored)) ? (bool) $stored : null;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isStored()
    {
        return $this->stored;
    }

    /**
     * @param string $type
     * @return $this - Provides Fluent Interface
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function castAsArray()
    {
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

        if (!array_key_exists('name', $output)) {
            throw new RuntimeException("The 'name' attribute is not defined.");
        }

        if (!array_key_exists('type', $output)) {
            throw new RuntimeException(sprintf("The 'type' attribute is not defined for the field %s.", $this->getName()));
        }

        return $output;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getName();
    }

    /**
     * @see \ArrayAccess::offsetExists
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return method_exists($this, 'get' . $offset)
            || method_exists($this, 'is' . $offset)
            || (stripos($offset, 'is') === 0 && method_exists($this, $offset));
    }

    /**
     * @see \ArrayAccess::offsetGet
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        if (method_exists($this, 'get' . $offset)) {
            return call_user_func([$this, 'get' . $offset]);
        } elseif (method_exists($this, 'is' . $offset)) {
            return call_user_func([$this, 'is' . $offset]);
        } elseif (stripos($offset, 'is') === 0 && method_exists($this, $offset)) {
            return call_user_func([$this, $offset]);
        }

        return null;
    }

    /**
     * @see \ArrayAccess::offsetSet
     * @param mixed $offset
     * @param mixed $value
     * @return $this
     */
    public function offsetSet($offset, $value)
    {
        return (method_exists($this, 'set' . $offset))
            ? call_user_func([$this, 'set' . $offset], $value)
            : $this;
    }

    /**
     * @see \ArrayAccess::offsetUnset
     * @param mixed $offset
     * @return $this
     */
    public function offsetUnset($offset)
    {
        return $this->offsetSet($offset, null);
    }
}

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
namespace Solarium\QueryType\Schema\Query\FieldType\Analyzer\Filter;

/**
 * Class Filter
 * @author Beno!t POLASZEK
 */
class Filter implements FilterInterface, \ArrayAccess
{
    protected $class = '';
    protected $attributes = array();

    public function __construct($class = null, $attributes = array())
    {
        $this->class = $class;
        $this->setAttributes($attributes);
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     * @return $this - Provides Fluent Interface
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @return $this - Provides Fluent Interface
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = array();
        $this->addAttributes($attributes);

        return $this;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function addAttributes(array $attributes)
    {
        foreach ($attributes AS $name => $value) {
            $this->addAttribute($name, $value);
        }

        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function addAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function castAsArray()
    {
        return array_merge(array('class' => $this->getClass()), $this->getAttributes());
    }

    /**
     * Whether a offset exists
     * @param mixed $offset An offset to check for.
     * @return boolean true on success or false on failure.
     *                      The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->attributes);
    }

    /**
     * Offset to retrieve
     * @param mixed $offset The offset to retrieve.
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->attributes[$offset];
    }

    /**
     * Offset to set
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value  The value to set.
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    /**
     * Offset to unset
     * @param mixed $offset The offset to unset.
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }
}

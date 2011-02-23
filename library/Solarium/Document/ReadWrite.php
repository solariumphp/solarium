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
 * @licence http://github.com/basdenooijer/solarium/raw/master/COPYING
 *
 * @package Solarium
 * @subpackage Document
 */

/**
 * Updateable Solr document
 */
class Solarium_Document_ReadWrite extends Solarium_Document_ReadOnly
{

    /**
     * Document boost value
     *
     * @var float
     */
    protected $_boost = null;

    /**
     * Field boosts
     * 
     * @var array
     */
    protected $_fieldBoosts;

    /**
     * Constructor.
     *
     * @param array $fields
     */
    public function __construct($fields = array(), $boosts = array())
    {
        $this->_fields = $fields;
        $this->_fieldBoosts = $boosts;
    }

    /**
     * Add a field value. If a field already has a value it will be converted
     * to a multivalue field.
     *
     * @param string $key
     * @param mixed $value
     * @param float $boost
     * @return Solarium_Document_ReadWrite Provides fluent interface
     */
    public function addField($key, $value, $boost = null)
    {
        if (!isset($this->_fields[$key])) {
            $this->setField($key, $value, $boost);
        } else {
            // convert single value to array if needed
            if (!is_array($this->_fields[$key])) {
                $this->_fields[$key] = array($this->_fields[$key]);
            }

            $this->_fields[$key][] = $value;
            $this->setFieldBoost($key, $boost);
        }

        return $this;
    }

    /**
     * Set a field value. If a field already has a value it will be overwritten.
     *
     * @param string $key
     * @param mixed $value
     * @param float $boost
     * @return Solarium_Document_ReadWrite Provides fluent interface
     */
    public function setField($key, $value, $boost = null)
    {
        $this->_fields[$key] = $value;
        $this->setFieldBoost($key, $boost);

        return $this;
    }

    /**
     * Remove a field from this document
     *
     * @param string $key
     * @return Solarium_Document_ReadWrite Provides fluent interface
     */
    public function removeField($key)
    {
        if (isset($this->_fields[$key])) {
            unset($this->_fields[$key]);
        }

        if (isset($this->_fieldBoosts[$key])) {
            unset($this->_fieldBoosts[$key]);
        }

        return $this;
    }

    /**
     * Get the boost value for a single document field
     *
     * @param string $key
     * @return float
     */
    public function getFieldBoost($key)
    {
        if (isset($this->_fieldBoosts[$key])) {
            return $this->_fieldBoosts[$key];
        } else {
            return null;
        }
    }

    /**
     * Set the boost value for a single field
     *
     * @param string $key
     * @param float $boost
     * @return Solarium_Document_ReadWrite Provides fluent interface
     */
    public function setFieldBoost($key, $boost)
    {
        $this->_fieldBoosts[$key] = $boost;
        return $this;
    }

    /**
     * Set the boost value for this document
     *
     * @param float $boost
     * @return Solarium_Document_ReadWrite Provides fluent interface
     */
    public function setBoost($boost)
    {
        $this->_boost = $boost;
        return $this;
    }

    /**
     * Get the boost value for this document
     *
     * @return float
     */
    public function getBoost()
    {
        return $this->_boost;
    }

    /**
     * Magic method for setting fields as properties of this document
     * object, by field name.
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->setField($name, $value);
    }

}
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
 *
 * @package Solarium
 * @subpackage Document
 */

/**
 * Read/Write Solr document
 *
 * This document type is used for update queries. It has all of the features of
 * the readonly document and it also allows for updating or adding fields and
 * boosts.
 *
 * While it is possible to use this document type for a select, alter it and use
 * it in an update query (effectively the 'edit' that Solr doesn't have) this
 * is not recommended. Most Solr indexes have fields that are indexed and not
 * stored. You will loose that data because it is impossible to retrieve it from
 * Solr. Always update from the original data source.
 *
 * @package Solarium
 * @subpackage Document
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
     * Using fieldname as the key and the boost as the value
     * 
     * @var array
     */
    protected $_fieldBoosts;

    /**
     * Constructor
     *
     * @param array $fields
     * @param array $boosts
     */
    public function __construct($fields = array(), $boosts = array())
    {
        $this->_fields = $fields;
        $this->_fieldBoosts = $boosts;
    }

    /**
     * Add a field value
     *
     * If a field already has a value it will be converted
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
     * Set a field value
     *
     * If a field already has a value it will be overwritten. You cannot use
     * this method for a multivalue field.
     * If you supply NULL as the value the field will be removed
     *
     * @param string $key
     * @param mixed $value
     * @param float $boost
     * @return Solarium_Document_ReadWrite Provides fluent interface
     */
    public function setField($key, $value, $boost = null)
    {
        if ($value === null) {
            $this->removeField($key);
        } else {
            $this->_fields[$key] = $value;
            $this->setFieldBoost($key, $boost);
        }

        return $this;
    }

    /**
     * Remove a field
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
     * Get the boost value for a field
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
     * Set the boost value for a field
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
     * Set the document boost value
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
     * Get the document boost value
     *
     * @return float
     */
    public function getBoost()
    {
        return $this->_boost;
    }

    /**
     * Clear all fields
     *
     * @return Solarium_Document_ReadWrite Provides fluent interface
     **/
    public function clear()
    {
        $this->_fields = array();
        $this->_fieldBoosts = array();
        
        return $this;
    }

    /**
     * Set field value
     *
     * Magic method for setting fields as properties of this document
     * object, by field name.
     *
     * If you supply NULL as the value the field will be removed
     * If you supply an array a multivalue field will be created.
     * In all cases any existing (multi)value will be overwritten.
     *
     * @param string $name
     * @param string|null $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->setField($name, $value);
    }

    /**
     * Unset field value
     *
     * Magic method for removing fields by unsetting object properties
     *
     * @param string $name
     * @return void
     */
    public function __unset($name)
    {
        $this->removeField($name);
    }

}
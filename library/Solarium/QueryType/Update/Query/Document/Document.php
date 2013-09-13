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
namespace Solarium\QueryType\Update\Query\Document;

use Solarium\QueryType\Select\Result\AbstractDocument;
use Solarium\Exception\RuntimeException;

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
 * Atomic updates are also support, using the field modifiers
 */
class Document extends AbstractDocument implements DocumentInterface
{
    /**
     * Directive to set a value using atomic updates
     *
     * @var string
     */
    const MODIFIER_SET = 'set';

    /**
     * Directive to increment an integer value using atomic updates
     *
     * @var string
     */
    const MODIFIER_INC = 'inc';

    /**
     * Directive to append a value (e.g. multivalued fields) using atomic updates
     *
     * @var string
     */
    const MODIFIER_ADD = 'add';

    /**
     * This value has the same effect as not setting a version
     *
     * @var int
     */
    const VERSION_DONT_CARE = 0;

    /**
     * This value requires an existing document with the same key, but no specific version
     *
     * @var int
     */
    const VERSION_MUST_EXIST = 1;

    /**
     * This value requires that no document with the same key exists (so no automatic overwrite like default)
     *
     * @var int
     */
    const VERSION_MUST_NOT_EXIST = -1;

    /**
     * Document boost value
     *
     * @var float
     */
    protected $boost = null;

    /**
     * Allows us to determine what kind of atomic update we want to set
     *
     * @var array
     */
    protected $modifiers = array();

    /**
     * This field needs to be explicitly set to observe the rules of atomic updates
     *
     * @var string
     */
    protected $key;

    /**
     * Field boosts
     *
     * Using fieldname as the key and the boost as the value
     *
     * @var array
     */
    protected $fieldBoosts;

    /**
     * Version value
     *
     * Can be used for updating using Solr's optimistic concurrency control
     *
     * @var int
     */
    protected $version;

    /**
     * Constructor
     *
     * @param array $fields
     * @param array $boosts
     * @param array $modifiers
     */
    public function __construct(array $fields = array(), array $boosts = array(), array $modifiers = array())
    {
        $this->fields = $fields;
        $this->fieldBoosts = $boosts;
        $this->modifiers = $modifiers;
    }

    /**
     * Add a field value
     *
     * If a field already has a value it will be converted
     * to a multivalue field.
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  float  $boost
     * @param  string $modifier
     * @return self   Provides fluent interface
     */
    public function addField($key, $value, $boost = null, $modifier = null)
    {
        if (!isset($this->fields[$key])) {
            $this->setField($key, $value, $boost, $modifier);
        } else {
            // convert single value to array if needed
            if (!is_array($this->fields[$key])) {
                $this->fields[$key] = array($this->fields[$key]);
            }

            $this->fields[$key][] = $value;
            $this->setFieldBoost($key, $boost);
            if ($modifier !== null) {
                $this->setFieldModifier($key, $modifier);
            }
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
     * @param  string $key
     * @param  mixed  $value
     * @param  float  $boost
     * @param  string $modifier
     * @return self   Provides fluent interface
     */
    public function setField($key, $value, $boost = null, $modifier = null)
    {
        if ($value === null && $modifier == null) {
            $this->removeField($key);
        } else {
            $this->fields[$key] = $value;
            $this->setFieldBoost($key, $boost);
            if ($modifier !== null) {
                $this->setFieldModifier($key, $modifier);
            }
        }

        return $this;
    }

    /**
     * Remove a field
     *
     * @param  string $key
     * @return self   Provides fluent interface
     */
    public function removeField($key)
    {
        if (isset($this->fields[$key])) {
            unset($this->fields[$key]);
        }

        if (isset($this->fieldBoosts[$key])) {
            unset($this->fieldBoosts[$key]);
        }

        return $this;
    }

    /**
     * Get the boost value for a field
     *
     * @param  string $key
     * @return float
     */
    public function getFieldBoost($key)
    {
        if (isset($this->fieldBoosts[$key])) {
            return $this->fieldBoosts[$key];
        } else {
            return null;
        }
    }

    /**
     * Set the boost value for a field
     *
     * @param  string $key
     * @param  float  $boost
     * @return self   Provides fluent interface
     */
    public function setFieldBoost($key, $boost)
    {
        $this->fieldBoosts[$key] = $boost;

        return $this;
    }

    /**
     * Get boost values for all fields
     *
     * @return array
     */
    public function getFieldBoosts()
    {
        return $this->fieldBoosts;
    }

    /**
     * Set the document boost value
     *
     * @param  float $boost
     * @return self  Provides fluent interface
     */
    public function setBoost($boost)
    {
        $this->boost = $boost;

        return $this;
    }

    /**
     * Get the document boost value
     *
     * @return float
     */
    public function getBoost()
    {
        return $this->boost;
    }

    /**
     * Clear all fields
     *
     * @return self Provides fluent interface
     **/
    public function clear()
    {
        $this->fields = array();
        $this->fieldBoosts = array();
        $this->modifiers = array();

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
     * @param  string      $name
     * @param  string|null $value
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
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        $this->removeField($name);
    }

    /**
     * Sets the uniquely identifying key for use in atomic updating
     *
     * You can set an existing field as key by supplying that field name as key, or add a new field by also supplying a
     * value.
     *
     * @param  string $key
     * @param  mixed  $value
     * @return self   Provides fluent interface
     */
    public function setKey($key, $value = null)
    {
        $this->key = $key;
        if ($value !== null) {
            $this->addField($key, $value);
        }
        return $this;
    }

    /**
     * Sets the modifier type for the provided field
     *
     * @param string $key
     * @param string $modifier
     * @throws RuntimeException
     * @return self
     */
    public function setFieldModifier($key, $modifier = null)
    {
        if (!in_array($modifier, array(self::MODIFIER_ADD, self::MODIFIER_INC, self::MODIFIER_SET))) {
            throw new RuntimeException('Attempt to set an atomic update modifier that is not supported');
        }
        $this->modifiers[$key] = $modifier;
        return $this;
    }

    /**
     * Returns the appropriate modifier for atomic updates.
     *
     * @param string $key
     * @return null|string
     */
    public function getFieldModifier($key)
    {
        return isset($this->modifiers[$key]) ? $this->modifiers[$key] : null;
    }

    /**
     * Get fields
     *
     * Adds validation for atomicUpdates
     *
     * @throws RuntimeException
     * @return array
     */
    public function getFields()
    {
        if (count($this->modifiers) > 0 && ($this->key == null || !isset($this->fields[$this->key]))) {
            throw new RuntimeException(
                'A document that uses modifiers (atomic updates) must have a key defined before it is used'
            );
        }

        return parent::getFields();
    }

    /**
     * Set version
     *
     * @param int $version
     * @return self
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * Get version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }
}

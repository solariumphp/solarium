<?php

namespace Solarium\QueryType\Update\Query\Document;

use Solarium\Core\Query\Helper;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Select\Result\AbstractDocument;

/**
 * Read/Write Solr document.
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
     * Directive to set or replace the field value(s) with the specified value(s), or remove the values if 'null' or
     * empty list is specified as the new value. May be specified as a single value, or as a list for multiValued
     * fields.
     *
     * @var string
     */
    const MODIFIER_SET = 'set';

    /**
     * Directive to increment a numeric value by a specific amount. Must be specified as a single numeric value.
     *
     * @var string
     */
    const MODIFIER_INC = 'inc';

    /**
     * Directive to add the specified values to a multiValued field. May be specified as a single value, or as a list.
     *
     * @var string
     */
    const MODIFIER_ADD = 'add';

    /**
     * Directive to add the specified values to a multiValued field, only if not already present. May be specified as a
     * single value, or as a list.
     *
     * @var string
     */
    const MODIFIER_ADD_DISTINCT = 'add-distinct';

    /**
     * Directive to remove (all occurrences of) the specified values from a multiValued field. May be specified as a
     * single value, or as a list.
     *
     * @var string
     */
    const MODIFIER_REMOVE = 'remove';

    /**
     * Directive to remove all occurrences of the specified regex from a multiValued field. May be specified as a single
     * value, or as a list.
     *
     * @var string
     */
    const MODIFIER_REMOVEREGEX = 'removeregex';

    /**
     * This value has the same effect as not setting a version.
     *
     * @var int
     */
    const VERSION_DONT_CARE = 0;

    /**
     * This value requires an existing document with the same key, but no specific version.
     *
     * @var int
     */
    const VERSION_MUST_EXIST = 1;

    /**
     * This value requires that no document with the same key exists (so no automatic overwrite like default).
     *
     * @var int
     */
    const VERSION_MUST_NOT_EXIST = -1;

    /**
     * Document boost value.
     *
     * @var float
     */
    protected $boost = null;

    /**
     * Allows us to determine what kind of atomic update we want to set.
     *
     * @var array
     */
    protected $modifiers = [];

    /**
     * This field needs to be explicitly set to observe the rules of atomic updates.
     *
     * @var string
     */
    protected $key;

    /**
     * Field boosts.
     *
     * Using fieldname as the key and the boost as the value
     *
     * @var array
     */
    protected $fieldBoosts;

    /**
     * Version value.
     *
     * Can be used for updating using Solr's optimistic concurrency control
     *
     * @var int
     */
    protected $version;

    /**
     * Helper instance.
     *
     * @var Helper
     */
    protected $helper;

    protected $filterControlCharacters = true;

    /**
     * Constructor.
     *
     * @param array $fields
     * @param array $boosts
     * @param array $modifiers
     */
    public function __construct(array $fields = [], array $boosts = [], array $modifiers = [])
    {
        $this->fields = $fields;
        $this->fieldBoosts = $boosts;
        $this->modifiers = $modifiers;
    }

    /**
     * Set field value.
     *
     * Magic method for setting fields as properties of this document
     * object, by field name.
     *
     * If you supply NULL as the value the field will be removed
     * If you supply an array a multivalue field will be created.
     * In all cases any existing (multi)value will be overwritten.
     *
     * @param string      $name
     * @param string|null $value
     */
    public function __set($name, $value)
    {
        $this->setField($name, $value);
    }

    /**
     * Unset field value.
     *
     * Magic method for removing fields by unsetting object properties
     *
     * @param string $name
     */
    public function __unset($name)
    {
        $this->removeField($name);
    }

    /**
     * Add a field value.
     *
     * If a field already has a value it will be converted
     * to a multivalue field.
     *
     * @param string $key
     * @param mixed  $value
     * @param float  $boost
     * @param string $modifier
     *
     * @return self Provides fluent interface
     */
    public function addField($key, $value, $boost = null, $modifier = null)
    {
        if (!isset($this->fields[$key])) {
            $this->setField($key, $value, $boost, $modifier);
        } else {
            // convert single value to array if needed
            if (!is_array($this->fields[$key])) {
                $this->fields[$key] = [$this->fields[$key]];
            }

            if ($this->filterControlCharacters && is_string($value)) {
                $value = $this->getHelper()->filterControlCharacters($value);
            }

            $this->fields[$key][] = $value;
            $this->setFieldBoost($key, $boost);
            if (null !== $modifier) {
                $this->setFieldModifier($key, $modifier);
            }
        }

        return $this;
    }

    /**
     * Set a field value.
     *
     * If a field already has a value it will be overwritten. You cannot use
     * this method for a multivalue field.
     * If you supply NULL as the value the field will be removed
     *
     * @param string $key
     * @param mixed  $value
     * @param float  $boost
     * @param string $modifier
     *
     * @return self Provides fluent interface
     */
    public function setField($key, $value, $boost = null, $modifier = null)
    {
        if (null === $value && null === $modifier) {
            $this->removeField($key);
        } else {
            if ($this->filterControlCharacters && is_string($value)) {
                $value = $this->getHelper()->filterControlCharacters($value);
            }

            $this->fields[$key] = $value;
            $this->setFieldBoost($key, $boost);
            if (null !== $modifier) {
                $this->setFieldModifier($key, $modifier);
            }
        }

        return $this;
    }

    /**
     * Remove a field.
     *
     * @param string $key
     *
     * @return self Provides fluent interface
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
     * Get the boost value for a field.
     *
     * @param string $key
     *
     * @return float
     */
    public function getFieldBoost($key)
    {
        if (isset($this->fieldBoosts[$key])) {
            return $this->fieldBoosts[$key];
        }
    }

    /**
     * Set the boost value for a field.
     *
     * @param string $key
     * @param float  $boost
     *
     * @return self Provides fluent interface
     */
    public function setFieldBoost($key, $boost)
    {
        $this->fieldBoosts[$key] = $boost;

        return $this;
    }

    /**
     * Get boost values for all fields.
     *
     * @return array
     */
    public function getFieldBoosts()
    {
        return $this->fieldBoosts;
    }

    /**
     * Set the document boost value.
     *
     * @param float $boost
     *
     * @return self Provides fluent interface
     */
    public function setBoost($boost)
    {
        $this->boost = $boost;

        return $this;
    }

    /**
     * Get the document boost value.
     *
     * @return float
     */
    public function getBoost()
    {
        return $this->boost;
    }

    /**
     * Clear all fields.
     *
     * @return self Provides fluent interface
     **/
    public function clear()
    {
        $this->fields = [];
        $this->fieldBoosts = [];
        $this->modifiers = [];

        return $this;
    }

    /**
     * Sets the uniquely identifying key for use in atomic updating.
     *
     * You can set an existing field as key by supplying that field name as key, or add a new field by also supplying a
     * value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return self Provides fluent interface
     */
    public function setKey($key, $value = null)
    {
        $this->key = $key;
        if (null !== $value) {
            $this->addField($key, $value);
        }

        return $this;
    }

    /**
     * Sets the modifier type for the provided field.
     *
     * @param string $key
     * @param string $modifier
     *
     * @throws RuntimeException
     *
     * @return self
     */
    public function setFieldModifier($key, $modifier = null)
    {
        if (!in_array($modifier, [self::MODIFIER_ADD, self::MODIFIER_REMOVE, self::MODIFIER_INC, self::MODIFIER_SET], true)) {
            throw new RuntimeException('Attempt to set an atomic update modifier that is not supported');
        }
        $this->modifiers[$key] = $modifier;

        return $this;
    }

    /**
     * Returns the appropriate modifier for atomic updates.
     *
     * @param string $key
     *
     * @return null|string
     */
    public function getFieldModifier($key)
    {
        return isset($this->modifiers[$key]) ? $this->modifiers[$key] : null;
    }

    /**
     * Get fields.
     *
     * Adds validation for atomicUpdates
     *
     * @throws RuntimeException
     *
     * @return array
     */
    public function getFields()
    {
        if (count($this->modifiers) > 0 && (null === $this->key || !isset($this->fields[$this->key]))) {
            throw new RuntimeException(
                'A document that uses modifiers (atomic updates) must have a key defined before it is used'
            );
        }

        return parent::getFields();
    }

    /**
     * Set version.
     *
     * @param int $version
     *
     * @return self
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version.
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Get a helper instance.
     *
     * Uses lazy loading: the helper is instantiated on first use
     *
     * @return Helper
     */
    public function getHelper()
    {
        if (null === $this->helper) {
            $this->helper = new Helper($this);
        }

        return $this->helper;
    }

    /**
     * Whether values should be filtered for control characters automatically.
     *
     * @param bool $filterControlCharacters
     */
    public function setFilterControlCharacters($filterControlCharacters)
    {
        $this->filterControlCharacters = $filterControlCharacters;
    }

    /**
     * Returns whether values should be filtered automatically or control characters.
     *
     * @return bool
     */
    public function getFilterControlCharacters()
    {
        return $this->filterControlCharacters;
    }
}

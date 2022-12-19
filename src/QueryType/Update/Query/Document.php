<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Update\Query;

use Solarium\Core\Query\AbstractDocument;
use Solarium\Core\Query\Helper;
use Solarium\Exception\RuntimeException;

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
 * stored. You will lose that data because it is impossible to retrieve it from
 * Solr. Always update from the original data source.
 *
 * Atomic updates are also supported, using the field modifiers.
 */
class Document extends AbstractDocument
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
     * Directive to increment a numeric value by a specific amount. Must be specified as a single numeric value.
     *
     * @var string
     */
    const MODIFIER_INC = 'inc';

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
     * Null menas no boost which is something different than a boost by '0.0'.
     *
     * @var float|null
     */
    protected $boost;

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

    /**
     * Constructor.
     *
     * @param array $fields
     * @param array $boosts
     * @param array $modifiers
     */
    public function __construct(array $fields = [], array $boosts = [], array $modifiers = [])
    {
        $this->setFields($fields, $boosts, $modifiers);
    }

    /**
     * Set field value.
     *
     * Magic method for setting fields as properties of this document
     * object, by field name.
     *
     * If you supply NULL as the value the field will be removed
     * If you supply a numerically indexed array of values a multivalue field will be created.
     * In all cases any existing (multi)value or child document(s) will be overwritten.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value): void
    {
        $this->setField($name, $value);
    }

    /**
     * Unset field value.
     *
     * Magic method for removing fields by un-setting object properties.
     *
     * @param string $name
     */
    public function __unset($name): void
    {
        $this->removeField($name);
    }

    /**
     * Add a field value.
     *
     * If a field already has a value it will be converted
     * to a multivalue field.
     *
     * If the value is a nested child document, the field will
     * always be converted to a multivalue field.
     *
     * @param string      $key
     * @param mixed       $value
     * @param float|null  $boost
     * @param string|null $modifier
     *
     * @return self Provides fluent interface
     */
    public function addField(string $key, $value, ?float $boost = null, ?string $modifier = null): self
    {
        if (!isset($this->fields[$key])) {
            // convert nested child document to array
            if (\is_array($value) && !is_numeric(array_key_first($value))) {
                $value = [$value];
            }

            $this->setField($key, $value, $boost, $modifier);
        } else {
            // convert single value or child document to array if needed
            if (!\is_array($this->fields[$key]) || !is_numeric(array_key_first($this->fields[$key]))) {
                $this->fields[$key] = [$this->fields[$key]];
            }

            $this->fields[$key][] = $value;

            if (null !== $boost) {
                $this->setFieldBoost($key, $boost);
            }

            if (null !== $modifier) {
                $this->setFieldModifier($key, $modifier);
            }
        }

        return $this;
    }

    /**
     * Set a field value.
     *
     * If you supply NULL as the value and no modifier the field will be removed
     * If you supply a numerically indexed array of values a multivalue field will be created.
     * In all cases any existing (multi)value or child document(s) will be overwritten.
     *
     * @param string      $key
     * @param mixed       $value
     * @param float|null  $boost
     * @param string|null $modifier
     *
     * @return self Provides fluent interface
     */
    public function setField(string $key, $value, ?float $boost = null, ?string $modifier = null): self
    {
        if (null === $value && null === $modifier) {
            $this->removeField($key);
        } else {
            $this->fields[$key] = $value;

            if (null !== $boost) {
                $this->setFieldBoost($key, $boost);
            }

            if (null !== $modifier) {
                $this->setFieldModifier($key, $modifier);
            }
        }

        return $this;
    }

    /**
     * Sets all field values.
     *
     * All previously set fields will be overwritten, even if they're not
     * included in the new field list.
     *
     * @param array $fields
     * @param array $boosts
     * @param array $modifiers
     *
     * @return self Provides fluent interface
     */
    public function setFields(array $fields = [], array $boosts = [], array $modifiers = []): self
    {
        $this->clear();

        foreach ($fields as $key => $value) {
            $this->setField($key, $value, $boosts[$key] ?? null, $modifiers[$key] ?? null);
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
    public function removeField(string $key): self
    {
        if (isset($this->fields[$key])) {
            unset($this->fields[$key]);
        }

        if (isset($this->fieldBoosts[$key])) {
            unset($this->fieldBoosts[$key]);
        }

        if (isset($this->modifiers[$key])) {
            unset($this->modifiers[$key]);
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
    public function getFieldBoost(string $key): ?float
    {
        return $this->fieldBoosts[$key] ?? null;
    }

    /**
     * Set the boost value for a field.
     *
     * @param string $key
     * @param float  $boost
     *
     * @return self Provides fluent interface
     */
    public function setFieldBoost(string $key, float $boost): self
    {
        $this->fieldBoosts[$key] = $boost;

        return $this;
    }

    /**
     * Get boost values for all fields.
     *
     * @return array
     */
    public function getFieldBoosts(): array
    {
        return $this->fieldBoosts;
    }

    /**
     * Set the document boost value.
     *
     * @param float $boost
     *
     * @return self Provides fluent interface
     *
     * @deprecated No longer supported since Solr 7
     */
    public function setBoost(float $boost): self
    {
        $this->boost = $boost;

        return $this;
    }

    /**
     * Get the document boost value.
     *
     * @return float|null
     *
     * @deprecated No longer supported since Solr 7
     */
    public function getBoost(): ?float
    {
        return $this->boost;
    }

    /**
     * Clear all fields.
     *
     * @return self Provides fluent interface
     **/
    public function clear(): self
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
    public function setKey(string $key, $value = null): self
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
    public function setFieldModifier(string $key, string $modifier = null): self
    {
        if (!\in_array($modifier, [self::MODIFIER_SET, self::MODIFIER_ADD, self::MODIFIER_ADD_DISTINCT, self::MODIFIER_REMOVE, self::MODIFIER_REMOVEREGEX, self::MODIFIER_INC], true)) {
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
     * @return string|null
     */
    public function getFieldModifier(string $key): ?string
    {
        return $this->modifiers[$key] ?? null;
    }

    /**
     * Get fields.
     *
     * Adds validation for atomic updates.
     *
     * @throws RuntimeException
     *
     * @return array
     */
    public function getFields(): array
    {
        if ((null === $this->key || !isset($this->fields[$this->key])) && \count($this->modifiers) > 0) {
            throw new RuntimeException('A document that uses modifiers (atomic updates) must have a key defined before it is used');
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
    public function setVersion(int $version): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version.
     *
     * @return int|null
     */
    public function getVersion(): ?int
    {
        return $this->version;
    }

    #[\ReturnTypeWillChange]
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $fields = $this->getFields();

        foreach ($fields as $key => &$value) {
            if ($value instanceof \DateTimeInterface) {
                $value = $this->getHelper()->formatDate($value);
            } elseif (\is_array($value) && is_numeric(array_key_first($value))) {
                // ensure consecutive indices so it doesn't serialize to an object
                $value = array_values($value);

                foreach ($value as &$multivalue) {
                    if ($multivalue instanceof \DateTimeInterface) {
                        $multivalue = $this->getHelper()->formatDate($multivalue);
                    }
                }
            }

            if (isset($this->modifiers[$key])) {
                $value = [$this->modifiers[$key] => $value];
            }
        }

        if (null !== $this->version) {
            $fields['_version_'] = $this->version;
        }

        return $fields;
    }

    /**
     * Get a helper instance.
     *
     * Uses lazy loading: the helper is instantiated on first use.
     *
     * @return Helper
     */
    protected function getHelper(): Helper
    {
        if (null === $this->helper) {
            $this->helper = new Helper();
        }

        return $this->helper;
    }
}

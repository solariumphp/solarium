<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Stats;

use Solarium\Component\AbstractComponent;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Component\RequestBuilder\Stats as RequestBuilder;
use Solarium\Component\ResponseParser\ComponentParserInterface;
use Solarium\Component\ResponseParser\Stats as ResponseParser;
use Solarium\Exception\InvalidArgumentException;

/**
 * Stats component.
 *
 * @see https://solr.apache.org/guide/the-stats-component.html
 */
class Stats extends AbstractComponent
{
    use FacetsTrait;

    /**
     * Fields.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Get component type.
     *
     * @return string
     */
    public function getType(): string
    {
        return ComponentAwareQueryInterface::COMPONENT_STATS;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder(): ComponentRequestBuilderInterface
    {
        return new RequestBuilder();
    }

    /**
     * Get a response parser for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser(): ?ComponentParserInterface
    {
        return new ResponseParser();
    }

    /**
     * Create a field instance.
     *
     * If you supply a string as the first arguments ($options) it will be used as the key for the field
     * and it will be added to this query component.
     * If you supply an options array/object that contains a key the field will also be added to the component.
     *
     * When no key is supplied the field cannot be added, in that case you will need to add it manually
     * after setting the key, by using the addField method.
     *
     * @param mixed $options
     *
     * @return Field
     */
    public function createField($options = null): Field
    {
        if (\is_string($options)) {
            $fq = new Field();
            $fq->setKey($options);
        } else {
            $fq = new Field($options);
        }

        if (null !== $fq->getKey()) {
            $this->addField($fq);
        }

        return $fq;
    }

    /**
     * Add a field.
     *
     * Supports a field instance or a config array, in that case a new
     * field instance wil be created based on the options.
     *
     * @param Field|array $field
     *
     * @throws InvalidArgumentException
     *
     * @return self Provides fluent interface
     */
    public function addField($field): self
    {
        if (\is_array($field)) {
            $field = new Field($field);
        }

        $key = $field->getKey();

        if (null === $key || 0 === \strlen($key)) {
            throw new InvalidArgumentException('A field must have a key value');
        }

        // Double add calls for the same field are ignored, but non-unique keys cause an exception.
        if (\array_key_exists($key, $this->fields) && $this->fields[$key] !== $field) {
            throw new InvalidArgumentException('A field must have a unique key value');
        }

        $this->fields[$key] = $field;

        return $this;
    }

    /**
     * Add multiple fields.
     *
     * @param array $fields
     *
     * @return self Provides fluent interface
     */
    public function addFields(array $fields): self
    {
        foreach ($fields as $key => $field) {
            // in case of a config array: add key to config
            if (\is_array($field) && !isset($field['key'])) {
                $field['key'] = $key;
            }

            $this->addField($field);
        }

        return $this;
    }

    /**
     * Get a field.
     *
     * @param string $key
     *
     * @return Field|null
     */
    public function getField(string $key): ?Field
    {
        return $this->fields[$key] ?? null;
    }

    /**
     * Get all fields.
     *
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Remove a single field.
     *
     * You can remove a field by passing its key, or by passing the field instance
     *
     * @param string|Field $field
     *
     * @return self Provides fluent interface
     */
    public function removeField($field): self
    {
        if (\is_object($field)) {
            $field = $field->getKey();
        }

        if ($field && isset($this->fields[$field])) {
            unset($this->fields[$field]);
        }

        return $this;
    }

    /**
     * Remove all fields.
     *
     * @return self Provides fluent interface
     */
    public function clearFields(): self
    {
        $this->fields = [];

        return $this;
    }

    /**
     * Set multiple fields.
     *
     * This overwrites any existing fields
     *
     * @param array $fields
     *
     * @return self Provides fluent interface
     */
    public function setFields(array $fields): self
    {
        $this->clearFields();
        $this->addFields($fields);

        return $this;
    }

    /**
     * Initialize options.
     *
     * Several options need some extra checks or setup work, for these options
     * the setters are called.
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'field':
                    $this->setFields($value);
                    break;
                case 'facet':
                    $this->setFacets($value);
                    break;
            }
        }
    }
}

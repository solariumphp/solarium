<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Highlighting;

use Solarium\Component\AbstractComponent;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\QueryInterface;
use Solarium\Component\QueryTrait;
use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Component\RequestBuilder\Highlighting as RequestBuilder;
use Solarium\Component\ResponseParser\ComponentParserInterface;
use Solarium\Component\ResponseParser\Highlighting as ResponseParser;
use Solarium\Exception\InvalidArgumentException;

/**
 * Highlighting component.
 *
 * @see https://solr.apache.org/guide/highlighting.html
 */
class Highlighting extends AbstractComponent implements HighlightingInterface, QueryInterface
{
    use HighlightingTrait;
    use QueryTrait;

    /**
     * Array of fields for highlighting.
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
        return ComponentAwareQueryInterface::COMPONENT_HIGHLIGHTING;
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
     * Get a field options object.
     *
     * @param string $name
     * @param bool   $autocreate
     *
     * @return Field|null
     */
    public function getField($name, $autocreate = true): ?Field
    {
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        }

        if ($autocreate) {
            $this->addField($name);

            return $this->fields[$name];
        }

        return null;
    }

    /**
     * Add a field for highlighting.
     *
     * @param string|array|Field $field
     *
     * @throws InvalidArgumentException
     *
     * @return self Provides fluent interface
     */
    public function addField($field): self
    {
        // autocreate object for string input
        if (\is_string($field)) {
            $field = new Field(['name' => $field]);
        } elseif (\is_array($field)) {
            $field = new Field($field);
        }

        // validate field
        if (null === $field->getName()) {
            throw new InvalidArgumentException('To add a highlighting field it needs to have at least a "name" setting');
        }

        $this->fields[$field->getName()] = $field;

        return $this;
    }

    /**
     * Add multiple fields for highlighting.
     *
     * @param string|array $fields can be an array of object instances or a string with comma
     *                             separated fieldnames
     *
     * @return self Provides fluent interface
     */
    public function addFields($fields): self
    {
        if (\is_string($fields)) {
            $fields = explode(',', $fields);
            $fields = array_map('trim', $fields);
        }

        foreach ($fields as $key => $field) {
            // in case of a config array without key: add key to config
            if (\is_array($field) && !isset($field['name'])) {
                $field['name'] = $key;
            }

            $this->addField($field);
        }

        return $this;
    }

    /**
     * Remove a highlighting field.
     *
     * @param string $field
     *
     * @return self Provides fluent interface
     */
    public function removeField(string $field): self
    {
        if (isset($this->fields[$field])) {
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
     * Get the list of fields.
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Set multiple fields.
     *
     * This overwrites any existing fields
     *
     * @param string|array $fields can be an array of object instances or a string with comma
     *                             separated fieldnames
     *
     * @return self Provides fluent interface
     */
    public function setFields($fields): self
    {
        $this->clearFields();
        $this->addFields($fields);

        return $this;
    }

    /**
     * Set the query parser to use for the highlight query.
     *
     * @param string $parser
     *
     * @return self Provides fluent interface
     */
    public function setQueryParser(string $parser): self
    {
        $this->setOption('queryparser', $parser);

        return $this;
    }

    /**
     * Get the query parser to use for the highlight query.
     *
     * @return string|null
     */
    public function getQueryParser(): ?string
    {
        return $this->getOption('queryparser');
    }

    /**
     * Set requireFieldMatch option.
     *
     * @param bool $require
     *
     * @return self Provides fluent interface
     */
    public function setRequireFieldMatch(bool $require): self
    {
        $this->setOption('requirefieldmatch', $require);

        return $this;
    }

    /**
     * Get requireFieldMatch option.
     *
     * @return bool|null
     */
    public function getRequireFieldMatch(): ?bool
    {
        return $this->getOption('requirefieldmatch');
    }

    /**
     * Set queryFieldPattern option.
     *
     * @param string|array $queryFieldPattern array or string with comma separated fieldnames
     *
     * @return self Provides fluent interface
     */
    public function setQueryFieldPattern($queryFieldPattern): self
    {
        if (\is_string($queryFieldPattern)) {
            $queryFieldPattern = explode(',', $queryFieldPattern);
            $queryFieldPattern = array_map('trim', $queryFieldPattern);
        }

        $this->setOption('queryfieldpattern', $queryFieldPattern);

        return $this;
    }

    /**
     * Get queryFieldPattern option.
     *
     * @return array|null
     */
    public function getQueryFieldPattern(): ?array
    {
        return $this->getOption('queryfieldpattern');
    }

    /**
     * Initialize options.
     *
     * {@internal Options that set a list of fields need additional setup work
     *            because they can be an array or a comma separated string.}
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'field':
                    $this->addFields($value);
                    break;
                case 'queryfieldpattern':
                    $this->setQueryFieldPattern($value);
                    break;
            }
        }
    }
}

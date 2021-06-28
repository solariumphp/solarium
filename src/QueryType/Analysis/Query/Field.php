<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Analysis\Query;

use Solarium\Core\Client\Client;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\QueryType\Analysis\RequestBuilder\Field as RequestBuilder;
use Solarium\QueryType\Analysis\ResponseParser\Field as ResponseParser;

/**
 * Analysis document query.
 */
class Field extends AbstractQuery
{
    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'handler' => 'analysis/field',
        'resultclass' => 'Solarium\QueryType\Analysis\Result\Field',
        'omitheader' => true,
    ];

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType(): string
    {
        return Client::QUERY_ANALYSIS_FIELD;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder(): RequestBuilderInterface
    {
        return new RequestBuilder();
    }

    /**
     * Get a response parser for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser(): ResponseParserInterface
    {
        return new ResponseParser();
    }

    /**
     * Set the field value option.
     *
     * The text that will be analyzed. The analysis will mimic the index-time analysis.
     *
     * @param string $value
     *
     * @return self Provides fluent interface
     */
    public function setFieldValue(string $value): self
    {
        $this->setOption('fieldvalue', $value);

        return $this;
    }

    /**
     * Get the field value option.
     *
     * @return string|null
     */
    public function getFieldValue(): ?string
    {
        return $this->getOption('fieldvalue');
    }

    /**
     * Set the field type option.
     *
     * When present, the text will be analyzed based on the specified type
     *
     * @param string $type
     *
     * @return self Provides fluent interface
     */
    public function setFieldType(string $type): self
    {
        $this->setOption('fieldtype', $type);

        return $this;
    }

    /**
     * Get the fieldtype option.
     *
     * @return string|null
     */
    public function getFieldType(): ?string
    {
        return $this->getOption('fieldtype');
    }

    /**
     * Set the field name option.
     *
     * When present, the text will be analyzed based on the type of this field name
     *
     * @param string $name
     *
     * @return self Provides fluent interface
     */
    public function setFieldName(string $name): self
    {
        $this->setOption('fieldname', $name);

        return $this;
    }

    /**
     * Get the fieldname option.
     *
     * @return string|null
     */
    public function getFieldName(): ?string
    {
        return $this->getOption('fieldname');
    }
}

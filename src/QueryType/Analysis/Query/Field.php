<?php

namespace Solarium\QueryType\Analysis\Query;

use Solarium\Core\Client\Client;
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
    public function getType()
    {
        return Client::QUERY_ANALYSIS_FIELD;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder()
    {
        return new RequestBuilder();
    }

    /**
     * Get a response parser for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser()
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
    public function setFieldValue($value)
    {
        return $this->setOption('fieldvalue', $value);
    }

    /**
     * Get the field value option.
     *
     * @return string
     */
    public function getFieldValue()
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
    public function setFieldType($type)
    {
        return $this->setOption('fieldtype', $type);
    }

    /**
     * Get the fieldtype option.
     *
     * @return string
     */
    public function getFieldType()
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
    public function setFieldName($name)
    {
        return $this->setOption('fieldname', $name);
    }

    /**
     * Get the fieldname option.
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->getOption('fieldname');
    }
}

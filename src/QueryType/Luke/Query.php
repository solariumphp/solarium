<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Luke;

use Solarium\Core\Client\Client;
use Solarium\Core\Query\AbstractQuery as BaseQuery;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\QueryType\Luke\ResponseParser\Doc as DocResponseParser;
use Solarium\QueryType\Luke\ResponseParser\Index as IndexResponseParser;
use Solarium\QueryType\Luke\ResponseParser\Fields as FieldsResponseParser;
use Solarium\QueryType\Luke\ResponseParser\Schema as SchemaResponseParser;
use Solarium\QueryType\Luke\Result\Result;
use Solarium\QueryType\Select\Result\Document;

/**
 * Luke query.
 *
 * Luke queries offer programmatic access to the information provided on the
 * Schema Browser page of the Admin UI.
 *
 * {@see https://solr.apache.org/guide/luke-request-handler.html}
 */
class Query extends BaseQuery
{
    /**
     * Return details about indexed fields plus the index details.
     */
    const SHOW_ALL = 'all';

    /**
     * Return details about a specific document plus the index details.
     *
     * Works in conjuction with a 'docId' or 'id' paramater.
     */
    const SHOW_DOC = 'doc';

    /**
     * Return the high level details about the index.
     */
    const SHOW_INDEX = 'index';

    /**
     * Return details about the schema plus the index details.
     */
    const SHOW_SCHEMA = 'schema';

    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'resultclass' => Result::class,
        'documentclass' => Document::class,
        'handler' => 'admin/luke',
        'omitheader' => true,
    ];

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType(): string
    {
        return Client::QUERY_LUKE;
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
     * @return ResponseParserInterface
     */
    public function getResponseParser(): ResponseParserInterface
    {
        switch ($this->getShow()) {
            case self::SHOW_SCHEMA:
                $parser = new SchemaResponseParser();
                break;
            case self::SHOW_DOC:
            case null:
                if (null !== $this->getId() || null !== $this->getDocId()) {
                    $parser = new DocResponseParser();
                    break;
                }
                // without 'id' or 'docId', SHOW_DOC or no 'show' at all behaves like SHOW_ALL
                // no break
            case self::SHOW_ALL:
                $parser = new FieldsResponseParser();
                break;
            case self::SHOW_INDEX:
            default:
                $parser = new IndexResponseParser();
        }

        return $parser;
    }

    /**
     * Set a custom document class.
     *
     * This class should implement the document interface.
     *
     * @param string $value classname
     *
     * @return self Provides fluent interface
     */
    public function setDocumentClass(string $value): self
    {
        $this->setOption('documentclass', $value);

        return $this;
    }

    /**
     * Get the current documentclass option.
     *
     * The value is a classname, not an instance.
     *
     * @return string|null
     */
    public function getDocumentClass(): ?string
    {
        return $this->getOption('documentclass');
    }

    /**
     * Set the data about the index to include in the response.
     *
     * Use one of the SHOW_* constants as value.
     *
     * {@see SHOW_ALL} returns all fields plus the index details. This is also the
     * default behaviour if no 'show' and no 'id' or 'docId' is set.
     *
     * {@see SHOW_INDEX} only returns high level details about the index. This data
     * is also included in all of the other responses.
     *
     * {@see SHOW_SCHEMA} returns details about the schema plus the index details.
     *
     * {@see SHOW_DOC} returns details about a specific document plus the index details.
     * It works in conjunction with {@see setId()} or {@see setDocId()}. This is
     * also the default behaviour if 'show' isn't set and an 'id' or 'docId' is set.
     *
     * @param string $show
     *
     * @return self Provides fluent interface
     */
    public function setShow(string $show): self
    {
        $this->setOption('show', $show);

        return $this;
    }

    /**
     * Get the data about the index to include in the response.
     *
     * @return string|null
     */
    public function getShow(): ?string
    {
        return $this->getOption('show');
    }

    /**
     * Set the id of a document to get using the uniqueKey field specified in schema.xml.
     *
     * @see setDocId() To set a Lucene documentID instead.
     *
     * @param mixed $id
     *
     * @return self Provides fluent interface
     */
    public function setId($id): self
    {
        $this->setOption('id', $id);

        return $this;
    }

    /**
     * Get the id of the document to get.
     *
     * @return mixed|null
     */
    public function getId()
    {
        return $this->getOption('id');
    }

    /**
     * Set the Lucene documentID of a document to get.
     *
     * @see setId() To set the uniqueKey id of a document instead.
     *
     * @param int $docId
     *
     * @return self Provides fluent interface
     */
    public function setDocId(int $docId): self
    {
        $this->setOption('docId', $docId);

        return $this;
    }

    /**
     * Get the Lucene documentID of the document to get.
     *
     * @return int|null
     */
    public function getDocId(): ?int
    {
        return $this->getOption('docId');
    }

    /**
     * Limit the returned values to a set of fields.
     *
     * Separate multiple fields with commas if you use string input.
     *
     * @param string|array $fields
     *
     * @return self Provides fluent interface
     */
    public function setFields($fields): self
    {
        if (\is_string($fields)) {
            $fields = explode(',', $fields);
            $fields = array_map('trim', $fields);
        }

        $this->setOption('fields', $fields);

        return $this;
    }

    /**
     * Get the set of fields to limit the returned values to.
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->getOption('fields') ?? [];
    }

    /**
     * Set the number of top terms for each field.
     *
     * @param int $numTerms
     *
     * @return self Provides fluent interface
     */
    public function setNumTerms(int $numTerms): self
    {
        $this->setOption('numTerms', $numTerms);

        return $this;
    }

    /**
     * Get the number of top terms for each field.
     *
     * @return int|null
     */
    public function getNumTerms(): ?int
    {
        return $this->getOption('numTerms');
    }

    /**
     * Set whether index-flags for each field should be returned.
     *
     * @param bool $includeIndexFieldFlags
     *
     * @return self Provides fluent interface
     */
    public function setIncludeIndexFieldFlags(bool $includeIndexFieldFlags): self
    {
        $this->setOption('includeIndexFieldFlags', $includeIndexFieldFlags);

        return $this;
    }

    /**
     * Get whether index-flags for each field should be returned.
     *
     * @return bool|null
     */
    public function getIncludeIndexFieldFlags(): ?bool
    {
        return $this->getOption('includeIndexFieldFlags');
    }

    /**
     * Initialize options.
     */
    protected function init()
    {
        foreach ($this->options as $name => $value) {
            switch ($name) {
                case 'fields':
                    $this->setFields($value);
                    break;
            }
        }
    }
}

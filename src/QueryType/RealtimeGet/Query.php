<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\RealtimeGet;

use Solarium\Core\Client\Client;
use Solarium\Core\Query\AbstractQuery as BaseQuery;
use Solarium\Core\Query\RequestBuilderInterface;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\QueryType\Select\ResponseParser;
use Solarium\QueryType\Select\Result\Document;

/**
 * Get query.
 *
 * Realtime Get query for one or multiple document IDs
 *
 * @see https://solr.apache.org/guide/realtime-get.html
 */
class Query extends BaseQuery
{
    /**
     * Default options.
     *
     * @var array
     */
    protected $options = [
        'resultclass' => Result::class,
        'documentclass' => Document::class,
        'handler' => 'get',
        'omitheader' => true,
    ];

    /**
     * Document IDs.
     *
     * @var array
     */
    protected $ids = [];

    /**
     * Get type for this query.
     *
     * @return string
     */
    public function getType(): string
    {
        return Client::QUERY_REALTIME_GET;
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
     * The ping query has no response parser so we return a null value.
     *
     * @return ResponseParser
     */
    public function getResponseParser(): ResponseParserInterface
    {
        return new ResponseParser();
    }

    /**
     * Add an id.
     *
     * @param string $id
     *
     * @return self Provides fluent interface
     */
    public function addId(string $id): self
    {
        $this->ids[$id] = true;

        return $this;
    }

    /**
     * Add multiple ids.
     *
     * @param string|array $ids can be an array or string with comma separated ids
     *
     * @return self Provides fluent interface
     */
    public function addIds($ids): self
    {
        if (\is_string($ids)) {
            $ids = explode(',', $ids);
            $ids = array_map('trim', $ids);
        }

        foreach ($ids as $id) {
            $this->addId($id);
        }

        return $this;
    }

    /**
     * Remove an id.
     *
     * @param string $id
     *
     * @return self Provides fluent interface
     */
    public function removeId(string $id): self
    {
        if (isset($this->ids[$id])) {
            unset($this->ids[$id]);
        }

        return $this;
    }

    /**
     * Remove all IDs.
     *
     * @return self Provides fluent interface
     */
    public function clearIds(): self
    {
        $this->ids = [];

        return $this;
    }

    /**
     * Get the list of ids.
     *
     * @return array
     */
    public function getIds(): array
    {
        return array_keys($this->ids);
    }

    /**
     * Set multiple ids.
     *
     * This overwrites any existing ids
     *
     * @param array $ids
     *
     * @return self Provides fluent interface
     */
    public function setIds(array $ids): self
    {
        $this->clearIds();
        $this->addIds($ids);

        return $this;
    }

    /**
     * Set a custom document class.
     *
     * This class should implement the document interface
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
     * The value is a classname, not an instance
     *
     * @return string
     */
    public function getDocumentClass(): string
    {
        return $this->getOption('documentclass');
    }

    /**
     * No components for this querytype.
     *
     * @return array
     */
    public function getComponents(): array
    {
        return [];
    }
}

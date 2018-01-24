<?php

namespace Solarium\QueryType\Analysis\Result;

use Solarium\Core\Query\Result\QueryType as BaseResult;

/**
 * Analysis document query result.
 */
class Document extends BaseResult implements \IteratorAggregate, \Countable
{
    /**
     * Document instances array.
     *
     * @var ResultList[]
     */
    protected $items;

    /**
     * Status code returned by Solr.
     *
     * @var int
     */
    protected $status;

    /**
     * Solr index queryTime.
     *
     * This doesn't include things like the HTTP responsetime. Purely the Solr
     * query execution time.
     *
     * @var int
     */
    protected $queryTime;

    /**
     * Get Solr status code.
     *
     * This is not the HTTP status code! The normal value for success is 0.
     *
     * @return int
     */
    public function getStatus()
    {
        $this->parseResponse();

        return $this->status;
    }

    /**
     * Get Solr query time.
     *
     * This doesn't include things like the HTTP responsetime. Purely the Solr
     * query execution time.
     *
     * @return int
     */
    public function getQueryTime()
    {
        $this->parseResponse();

        return $this->queryTime;
    }

    /**
     * Get all documents.
     *
     * @return ResultList[]
     */
    public function getDocuments()
    {
        $this->parseResponse();

        return $this->items;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        $this->parseResponse();

        return new \ArrayIterator($this->items);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count()
    {
        $this->parseResponse();

        return count($this->items);
    }

    /**
     * Get a document by uniquekey value.
     *
     * @param string $key
     *
     * @return ResultList|null
     */
    public function getDocument($key)
    {
        $this->parseResponse();

        if (isset($this->items[$key])) {
            return $this->items[$key];
        }
    }
}

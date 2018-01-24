<?php

namespace Solarium\QueryType\Suggester\Result;

use Solarium\Core\Query\Result\QueryType as BaseResult;

/**
 * Suggester query result.
 */
class Result extends BaseResult implements \IteratorAggregate, \Countable
{
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
     * Suggester results.
     *
     * @var array
     */
    protected $results;

    /**
     * Suggester flat results.
     *
     * @var array
     */
    protected $all;

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
     * Get all results.
     *
     * @return array
     */
    public function getResults()
    {
        $this->parseResponse();

        return $this->results;
    }

    /**
     * Get flat results.
     *
     * @return array
     */
    public function getAll()
    {
        $this->parseResponse();

        return $this->all;
    }

    /**
     * Get results for a specific dictionary.
     *
     * @param string $dictionary
     *
     * @return Dictionary|null
     */
    public function getDictionary($dictionary)
    {
        $this->parseResponse();

        if (isset($this->results[$dictionary])) {
            return $this->results[$dictionary];
        }

        return null;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        $this->parseResponse();

        return new \ArrayIterator($this->results);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count()
    {
        $this->parseResponse();

        return count($this->results);
    }
}

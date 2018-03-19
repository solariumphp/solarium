<?php

namespace Solarium\QueryType\Stream;

use Solarium\Core\Query\Result\QueryType as BaseResult;
use Solarium\QueryType\Select\Result\DocumentInterface;

/**
 * Stream query result.
 */
class Result extends BaseResult implements \IteratorAggregate, \Countable
{
    /**
     * Solr numFound.
     *
     * This is NOT the number of document fetched from Solr!
     *
     * @var int
     */
    protected $numfound;

    /**
     * Document instances array.
     *
     * @var array
     */
    protected $documents;

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
     * get Solr numFound.
     *
     * Returns the total number of documents found by Solr (this is NOT the
     * number of document fetched from Solr!)
     *
     * @return int
     */
    public function getNumFound()
    {
        $this->parseResponse();

        return $this->numfound;
    }

    /**
     * Get Solr status code.
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * Get all documents.
     *
     * @return DocumentInterface[]
     */
    public function getDocuments()
    {
        $this->parseResponse();

        return $this->documents;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        $this->parseResponse();

        return new \ArrayIterator($this->documents);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count()
    {
        $this->parseResponse();

        return count($this->documents);
    }

    /**
     * Get Solr response body.
     *
     * @return string A JSON encoded string
     */
    public function getBody()
    {
        return $this->response->getBody();
    }

    /**
     * Get Solr response body in JSON format.
     *
     * More expressive convenience method that just call getBody().
     *
     * @return string JSON
     */
    public function getJson()
    {
        return $this->getBody();
    }

    /**
     * Get Solr response data.
     *
     * Includes a lazy loading mechanism: JSON body data is decoded on first use and then saved for reuse.
     *
     * @return array
     */
    public function getData()
    {
        if (null === $this->data) {
            $this->data = json_decode($this->response->getBody(), true);
        }

        return $this->data;
    }
}

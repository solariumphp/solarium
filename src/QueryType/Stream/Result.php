<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Stream;

use Solarium\Core\Query\DocumentInterface;
use Solarium\Core\Query\Result\QueryType as BaseResult;

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
     * @throws \Solarium\Exception\UnexpectedValueException
     *
     * @return int
     */
    public function getStatus(): int
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
     * @throws \Solarium\Exception\UnexpectedValueException
     *
     * @return int
     */
    public function getQueryTime(): int
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
     * @throws \Solarium\Exception\UnexpectedValueException
     *
     * @return int
     */
    public function getNumFound(): int
    {
        $this->parseResponse();

        return $this->numfound;
    }

    /**
     * Get Solr status code.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    /**
     * Get all documents.
     *
     * @throws \Solarium\Exception\UnexpectedValueException
     *
     * @return DocumentInterface[]
     */
    public function getDocuments(): array
    {
        $this->parseResponse();

        return $this->documents;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @throws \Solarium\Exception\UnexpectedValueException
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        $this->parseResponse();

        return new \ArrayIterator($this->documents);
    }

    /**
     * Countable implementation.
     *
     * @throws \Solarium\Exception\UnexpectedValueException
     *
     * @return int
     */
    public function count(): int
    {
        $this->parseResponse();

        return \count($this->documents);
    }

    /**
     * Get Solr response body.
     *
     * @return string A JSON encoded string
     */
    public function getBody(): string
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
    public function getJson(): string
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
    public function getData(): array
    {
        if (null === $this->data) {
            $this->data = json_decode($this->response->getBody(), true);
        }

        return $this->data;
    }
}

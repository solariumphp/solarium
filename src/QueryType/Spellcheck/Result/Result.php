<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Spellcheck\Result;

use Solarium\Core\Query\Result\QueryType as BaseResult;

/**
 * Spellcheck query result.
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
     * Collation result.
     *
     * Only available when collate is enabled in the suggester query
     *
     * @var string
     */
    protected $collation;

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
     * Get all results.
     *
     * @throws \Solarium\Exception\UnexpectedValueException
     *
     * @return array
     */
    public function getResults(): array
    {
        $this->parseResponse();

        return $this->results;
    }

    /**
     * Get flat results.
     *
     * @throws \Solarium\Exception\UnexpectedValueException
     *
     * @return array
     */
    public function getAll(): array
    {
        $this->parseResponse();

        return $this->all;
    }

    /**
     * Get results for a specific term.
     *
     * @param string $term
     *
     * @throws \Solarium\Exception\UnexpectedValueException
     *
     * @return array
     */
    public function getTerm(string $term): ?Term
    {
        $this->parseResponse();

        return $this->results[$term] ?? null;
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

        return new \ArrayIterator($this->results);
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

        return \count($this->results);
    }

    /**
     * Get collation.
     *
     * @throws \Solarium\Exception\UnexpectedValueException
     *
     * @return string|null
     */
    public function getCollation(): ?string
    {
        $this->parseResponse();

        return $this->collation;
    }
}

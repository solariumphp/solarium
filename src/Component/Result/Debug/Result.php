<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Debug;

use Solarium\Component\Result\ComponentResultInterface;

/**
 * Select component debug result.
 */
class Result implements ComponentResultInterface, \IteratorAggregate, \Countable
{
    /**
     * QueryString.
     */
    protected string $queryString;

    /**
     * ParsedQuery.
     */
    protected string $parsedQuery;

    /**
     * QueryParser.
     */
    protected string $queryParser;

    /**
     * OtherQuery.
     */
    protected string $otherQuery;

    /**
     * Explain instance.
     */
    protected DocumentSet $explain;

    /**
     * ExplainOther instance.
     */
    protected ?DocumentSet $explainOther;

    /**
     * Timing instance.
     */
    protected ?Timing $timing;

    /**
     * Constructor.
     *
     * @param string           $queryString
     * @param string           $parsedQuery
     * @param string           $queryParser
     * @param string           $otherQuery
     * @param DocumentSet      $explain
     * @param DocumentSet|null $explainOther
     * @param Timing|null      $timing
     */
    public function __construct(string $queryString, string $parsedQuery, string $queryParser, string $otherQuery, DocumentSet $explain, ?DocumentSet $explainOther, ?Timing $timing)
    {
        $this->queryString = $queryString;
        $this->parsedQuery = $parsedQuery;
        $this->queryParser = $queryParser;
        $this->otherQuery = $otherQuery;
        $this->explain = $explain;
        $this->explainOther = $explainOther;
        $this->timing = $timing;
    }

    /**
     * Get input querystring.
     *
     * @return string
     */
    public function getQueryString(): string
    {
        return $this->queryString;
    }

    /**
     * Get the result of the queryparser.
     *
     * @return string
     */
    public function getParsedQuery(): string
    {
        return $this->parsedQuery;
    }

    /**
     * Get the used queryparser.
     *
     * @return string
     */
    public function getQueryParser(): string
    {
        return $this->queryParser;
    }

    /**
     * Get other query (only available if set in query).
     *
     * @return string
     */
    public function getOtherQuery(): string
    {
        return $this->otherQuery;
    }

    /**
     * Get explain document set.
     *
     * @return DocumentSet
     */
    public function getExplain(): DocumentSet
    {
        return $this->explain;
    }

    /**
     * Get explain other document set (only available if otherquery was set in query).
     *
     * @return DocumentSet|null
     */
    public function getExplainOther(): ?DocumentSet
    {
        return $this->explainOther;
    }

    /**
     * Get timing object.
     *
     * @return Timing|null
     */
    public function getTiming(): ?Timing
    {
        return $this->timing;
    }

    /**
     * IteratorAggregate implementation.
     *
     * Iterates the explain results
     *
     * @return DocumentSet
     */
    public function getIterator(): DocumentSet
    {
        return $this->explain;
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->explain);
    }
}

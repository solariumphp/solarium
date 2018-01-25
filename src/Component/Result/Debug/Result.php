<?php

namespace Solarium\Component\Result\Debug;

/**
 * Select component debug result.
 */
class Result implements \IteratorAggregate, \Countable
{
    /**
     * QueryString.
     *
     * @var string
     */
    protected $queryString;

    /**
     * ParsedQuery.
     *
     * @var string
     */
    protected $parsedQuery;

    /**
     * QueryParser.
     *
     * @var string
     */
    protected $queryParser;

    /**
     * OtherQuery.
     *
     * @var string
     */
    protected $otherQuery;

    /**
     * Explain instance.
     *
     * @var DocumentSet
     */
    protected $explain;

    /**
     * ExplainOther instance.
     *
     * @var DocumentSet
     */
    protected $explainOther;

    /**
     * Timing instance.
     *
     * @var Timing
     */
    protected $timing;

    /**
     * Constructor.
     *
     * @param string      $queryString
     * @param string      $parsedQuery
     * @param string      $queryParser
     * @param string      $otherQuery
     * @param DocumentSet $explain
     * @param DocumentSet $explainOther
     * @param Timing      $timing
     */
    public function __construct($queryString, $parsedQuery, $queryParser, $otherQuery, $explain, $explainOther, $timing)
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
    public function getQueryString()
    {
        return $this->queryString;
    }

    /**
     * Get the result of the queryparser.
     *
     * @return string
     */
    public function getParsedQuery()
    {
        return $this->parsedQuery;
    }

    /**
     * Get the used queryparser.
     *
     * @return string
     */
    public function getQueryParser()
    {
        return $this->queryParser;
    }

    /**
     * Get other query (only available if set in query).
     *
     * @return string
     */
    public function getOtherQuery()
    {
        return $this->otherQuery;
    }

    /**
     * Get explain document set.
     *
     * @return DocumentSet
     */
    public function getExplain()
    {
        return $this->explain;
    }

    /**
     * Get explain other document set (only available if otherquery was set in query).
     *
     * @return DocumentSet
     */
    public function getExplainOther()
    {
        return $this->explainOther;
    }

    /**
     * Get timing object.
     *
     * @return Timing
     */
    public function getTiming()
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
    public function getIterator()
    {
        return $this->explain;
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count()
    {
        return count($this->explain);
    }
}

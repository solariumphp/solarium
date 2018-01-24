<?php

namespace Solarium\Component\Result\Grouping;

use Solarium\QueryType\Select\Query\Query;

/**
 * Select component grouping query group result.
 *
 * @since 2.1.0
 */
class QueryGroup implements \IteratorAggregate, \Countable
{
    /**
     * Match count.
     *
     * @var int
     */
    protected $matches;

    /**
     * NumFound value.
     *
     * @var int
     */
    protected $numFound;

    /**
     * Start offset.
     *
     * @var int
     */
    protected $start;

    /**
     * Maximum score in group.
     *
     * @var float
     */
    protected $maximumScore;

    /**
     * Group documents array.
     *
     * @var array
     */
    protected $documents;

    /**
     * @var Query
     */
    protected $query;

    /**
     * Constructor.
     *
     * @param int   $matches
     * @param int   $numFound
     * @param int   $start
     * @param float $maximumScore
     * @param array $documents
     * @param Query $query
     */
    public function __construct($matches, $numFound, $start, $maximumScore, $documents, $query = null)
    {
        $this->matches = $matches;
        $this->numFound = $numFound;
        $this->start = $start;
        $this->maximumScore = $maximumScore;
        $this->documents = $documents;
        $this->query = $query;
    }

    /**
     * Get matches value.
     *
     * @return int
     */
    public function getMatches()
    {
        return $this->matches;
    }

    /**
     * Get numFound value.
     *
     * @return int
     */
    public function getNumFound()
    {
        return $this->numFound;
    }

    /**
     * Get start value.
     *
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Get maximumScore value.
     *
     * @return int
     */
    public function getMaximumScore()
    {
        return $this->maximumScore;
    }

    /**
     * Get all documents.
     *
     * @return array
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getDocuments());
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count()
    {
        return count($this->getDocuments());
    }
}

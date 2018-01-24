<?php

namespace Solarium\Component\Result\Grouping;

use Solarium\QueryType\Select\Query\Query;

/**
 * Select component grouping field value group result.
 *
 * @since 2.1.0
 */
class ValueGroup implements \IteratorAggregate, \Countable
{
    /**
     * Field value.
     *
     * @var string
     */
    protected $value;

    /**
     * NumFound.
     *
     * @var int
     */
    protected $numFound;

    /**
     * Start position.
     *
     * @var int
     */
    protected $start;

    /**
     * Documents in this group.
     *
     * @var array
     */
    protected $documents;

    /**
     * Maximum score in group.
     *
     * @var float
     */
    protected $maximumScore;

    /**
     * @var Query
     */
    protected $query;

    /**
     * Constructor.
     *
     * @param string $value
     * @param int    $numFound
     * @param int    $start
     * @param array  $documents
     * @param float  $maxScore
     * @param Query  $query
     */
    public function __construct($value, $numFound, $start, $documents, $maxScore = null, $query = null)
    {
        $this->value = $value;
        $this->numFound = $numFound;
        $this->start = $start;
        $this->documents = $documents;
        $this->maximumScore = $maxScore;
        $this->query = $query;
    }

    /**
     * Get value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get numFound.
     *
     * @return int
     */
    public function getNumFound()
    {
        return $this->numFound;
    }

    /**
     * Get start.
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

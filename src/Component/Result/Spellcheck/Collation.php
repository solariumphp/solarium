<?php

namespace Solarium\Component\Result\Spellcheck;

/**
 * Select component spellcheck collation result.
 */
class Collation implements \IteratorAggregate, \Countable
{
    /**
     * Query.
     *
     * @var string
     */
    protected $query;

    /**
     * Hit count.
     *
     * @var int
     */
    protected $hits;

    /**
     * Corrections.
     *
     * @var array
     */
    protected $corrections;

    /**
     * Constructor.
     *
     * @param string   $query
     * @param int|null $hits
     * @param array    $corrections
     */
    public function __construct($query, $hits, $corrections)
    {
        $this->query = $query;
        $this->hits = $hits;
        $this->corrections = $corrections;
    }

    /**
     * Get query string.
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Get hit count.
     *
     * Only available if ExtendedResults was enabled in your query
     *
     * @return int|null
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * Get all corrrections.
     *
     * Only available if ExtendedResults was enabled in your query
     *
     * @return array
     */
    public function getCorrections()
    {
        return $this->corrections;
    }

    /**
     * IteratorAggregate implementation.
     *
     * Only available if ExtendedResults was enabled in your query
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->corrections);
    }

    /**
     * Countable implementation.
     *
     * Only available if ExtendedResults was enabled in your query
     *
     * @return int
     */
    public function count()
    {
        return count($this->corrections);
    }
}

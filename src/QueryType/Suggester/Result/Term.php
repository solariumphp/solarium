<?php

namespace Solarium\QueryType\Suggester\Result;

/**
 * Suggester query term result.
 */
class Term implements \IteratorAggregate, \Countable
{
    /**
     * NumFound.
     *
     * @var int
     */
    protected $numFound;

    /**
     * Suggestions.
     *
     * @var array
     */
    protected $suggestions;

    /**
     * Constructor.
     *
     * @param int   $numFound
     * @param array $suggestions
     */
    public function __construct($numFound, $suggestions)
    {
        $this->numFound = $numFound;
        $this->suggestions = $suggestions;
    }

    /**
     * Get NumFound.
     *
     * @return int
     */
    public function getNumFound()
    {
        return $this->numFound;
    }

    /**
     * Get suggestions.
     *
     * @return array
     */
    public function getSuggestions()
    {
        return $this->suggestions;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->suggestions);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count()
    {
        return count($this->suggestions);
    }
}

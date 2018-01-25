<?php

namespace Solarium\QueryType\Suggester\Result;

/**
 * Suggester query dictionary result.
 */
class Dictionary implements \IteratorAggregate, \Countable
{
    /**
     * Suggestions.
     *
     * @var Term[]
     */
    protected $terms;

    /**
     * Constructor.
     *
     * @param Term[] $terms
     */
    public function __construct(array $terms)
    {
        $this->terms = $terms;
    }

    /**
     * Get Terms.
     *
     * @return Term[]
     */
    public function getTerms()
    {
        return $this->terms;
    }

    /**
     * Get results for a specific term.
     *
     * @param string $term
     *
     * @return Term|null
     */
    public function getTerm($term)
    {
        if (isset($this->terms[$term])) {
            return $this->terms[$term];
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
        return new \ArrayIterator($this->terms);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count()
    {
        return count($this->terms);
    }
}

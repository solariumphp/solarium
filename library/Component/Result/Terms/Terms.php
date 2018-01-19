<?php

namespace Solarium\Component\Result\Terms;

/**
 * Terms.
 */
class Terms implements \IteratorAggregate, \Countable
{

    /**
     * Terms.
     *
     * @var array
     */
    protected $terms;

    /**
     * Constructor.
     *
     * @param array $terms
     */
    public function __construct($terms)
    {
        $this->terms = $terms;
    }

    /**
     * Get suggestions.
     *
     * @return array
     */
    public function getTerms()
    {
        return $this->terms;
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

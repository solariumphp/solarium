<?php

namespace Solarium\Component\Result\Terms;

/**
 * Terms component result.
 */
class Field implements \IteratorAggregate, \Countable
{
    /**
     * Terms.
     *
     * @var Terms[]
     */
    protected $terms;

    /**
     * Constructor.
     *
     * @param Terms[] $terms
     */
    public function __construct(array $terms)
    {
        $this->terms = $terms;
    }

    /**
     * Get Terms.
     *
     * @return Terms[]
     */
    public function getAllTerms()
    {
        return $this->terms;
    }

    /**
     * Get results for a specific term.
     *
     * @param string $term
     *
     * @return Terms|null
     */
    public function getTerms($field)
    {
        if (isset($this->terms[$field])) {
            return $this->terms[$field];
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

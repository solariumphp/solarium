<?php

namespace Solarium\Component\Result\Highlighting;

/**
 * Select component highlighting result.
 */
class Highlighting implements \IteratorAggregate, \Countable
{
    /**
     * Result array.
     *
     * @var array
     */
    protected $results;

    /**
     * Constructor.
     *
     * @param array $results
     */
    public function __construct($results)
    {
        $this->results = $results;
    }

    /**
     * Get a result by key.
     *
     * @param mixed $key
     *
     * @return Result|null
     */
    public function getResult($key)
    {
        if (isset($this->results[$key])) {
            return $this->results[$key];
        }
    }

    /**
     * Get all results.
     *
     * @return Result[]
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->results);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count()
    {
        return count($this->results);
    }
}

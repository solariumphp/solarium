<?php

namespace Solarium\Component\Result\Suggester;

use Solarium\QueryType\Suggester\Result\Dictionary;

/**
 * Component suggester result.
 */
class Result implements \IteratorAggregate, \Countable
{
    /**
     * Suggester results.
     *
     * @var array
     */
    protected $results;

    /**
     * Suggester flat results.
     *
     * @var array
     */
    protected $all;

    /**
     * Constructor.
     *
     * @param array $results
     * @param array $all
     */
    public function __construct($results, $all)
    {
        $this->results = $results;
        $this->all = $all;
    }

    /**
     * Get all results.
     *
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Get flat results.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->all;
    }

    /**
     * Get results for a specific dictionary.
     *
     * @param string $dictionary
     *
     * @return Dictionary|null
     */
    public function getDictionary($dictionary)
    {
        if (isset($this->results[$dictionary])) {
            return $this->results[$dictionary];
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

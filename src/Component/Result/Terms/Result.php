<?php

namespace Solarium\Component\Result\Terms;

/**
 * Component terms result.
 */
class Result implements \IteratorAggregate, \Countable
{
    /**
     * Terms results.
     *
     * @var array
     */
    protected $results;

    /**
     * Terms flat results.
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
     * @param string $field
     *
     * @return Field|null
     */
    public function getField($field)
    {
        if (isset($this->results[$field])) {
            return $this->results[$field];
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

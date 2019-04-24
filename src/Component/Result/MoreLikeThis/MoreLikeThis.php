<?php

namespace Solarium\Component\Result\MoreLikeThis;

/**
 * Select component morelikethis result.
 */
class MoreLikeThis implements \IteratorAggregate, \Countable
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
    public function __construct(array $results)
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
    public function getResult($key): ?Result
    {
        return $this->results[$key] ?? null;
    }

    /**
     * Get all results.
     *
     * @return Result[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->results);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->results);
    }
}

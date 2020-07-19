<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Stats;

/**
 * Select component stats result.
 */
class Stats implements \IteratorAggregate, \Countable
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
     * @param string                                  $key
     * @param \Solarium\Component\Result\Stats\Result $result
     *
     * @return $this
     */
    public function setResult(string $key, Result $result): self
    {
        $this->results[$key] = $result;

        return $this;
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
        return \count($this->results);
    }
}

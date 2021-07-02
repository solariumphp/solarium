<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

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
    public function __construct(array $results, array $all)
    {
        $this->results = $results;
        $this->all = $all;
    }

    /**
     * Get all results.
     *
     * @return array
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * Get flat results.
     *
     * @return array
     */
    public function getAll(): array
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
    public function getField($field): ?Field
    {
        return $this->results[$field] ?? null;
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

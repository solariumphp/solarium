<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

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
     * @param string $dictionary
     *
     * @return Dictionary|null
     */
    public function getDictionary(string $dictionary): ?Dictionary
    {
        return $this->results[$dictionary] ?? null;
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

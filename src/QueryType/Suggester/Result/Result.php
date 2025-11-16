<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Suggester\Result;

use Solarium\Core\Query\Result\QueryType as BaseResult;
use Solarium\Exception\UnexpectedValueException;

/**
 * Suggester query result.
 */
class Result extends BaseResult implements \IteratorAggregate, \Countable
{
    /**
     * Suggester results.
     */
    protected array $results;

    /**
     * Suggester flat results.
     */
    protected array $all;

    /**
     * Get all results.
     *
     * @throws UnexpectedValueException
     *
     * @return array
     */
    public function getResults(): array
    {
        $this->parseResponse();

        return $this->results;
    }

    /**
     * Get flat results.
     *
     * @throws UnexpectedValueException
     *
     * @return array
     */
    public function getAll(): array
    {
        $this->parseResponse();

        return $this->all;
    }

    /**
     * Get results for a specific dictionary.
     *
     * @param string $dictionary
     *
     * @throws UnexpectedValueException
     *
     * @return Dictionary|null
     */
    public function getDictionary(string $dictionary): ?Dictionary
    {
        $this->parseResponse();

        return $this->results[$dictionary] ?? null;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @throws UnexpectedValueException
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        $this->parseResponse();

        return new \ArrayIterator($this->results);
    }

    /**
     * Countable implementation.
     *
     * @throws UnexpectedValueException
     *
     * @return int
     */
    public function count(): int
    {
        $this->parseResponse();

        return \count($this->results);
    }
}

<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Terms;

use Solarium\Core\Query\Result\QueryType as BaseResult;

/**
 * Terms query result.
 */
class Result extends BaseResult implements \IteratorAggregate, \Countable
{
    /**
     * Term results.
     *
     * @var array
     */
    protected $results;

    /**
     * Get all term results.
     *
     * @throws \Solarium\Exception\UnexpectedValueException
     *
     * @return array
     */
    public function getResults(): array
    {
        $this->parseResponse();

        return $this->results;
    }

    /**
     * Get term results for a specific field.
     *
     * @param string $field
     *
     * @throws \Solarium\Exception\UnexpectedValueException
     *
     * @return array
     */
    public function getTerms(string $field): array
    {
        $this->parseResponse();

        if (isset($this->results[$field])) {
            return $this->results[$field];
        }

        return [];
    }

    /**
     * IteratorAggregate implementation.
     *
     * @throws \Solarium\Exception\UnexpectedValueException
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
     * @throws \Solarium\Exception\UnexpectedValueException
     *
     * @return int
     */
    public function count(): int
    {
        $this->parseResponse();

        return \count($this->results);
    }
}

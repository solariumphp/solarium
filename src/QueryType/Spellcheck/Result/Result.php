<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Spellcheck\Result;

use Solarium\Core\Query\Result\QueryType as BaseResult;
use Solarium\Exception\UnexpectedValueException;

/**
 * Spellcheck query result.
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
     * Collation result.
     *
     * Only available when collate is enabled in the suggester query
     */
    protected ?string $collation;

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
     * Get results for a specific term.
     *
     * @param string $term
     *
     * @throws UnexpectedValueException
     *
     * @return Term|null
     */
    public function getTerm(string $term): ?Term
    {
        $this->parseResponse();

        return $this->results[$term] ?? null;
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

    /**
     * Get collation.
     *
     * @throws UnexpectedValueException
     *
     * @return string|null
     */
    public function getCollation(): ?string
    {
        $this->parseResponse();

        return $this->collation;
    }
}

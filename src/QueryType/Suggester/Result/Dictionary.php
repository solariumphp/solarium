<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Suggester\Result;

/**
 * Suggester query dictionary result.
 */
class Dictionary implements \IteratorAggregate, \Countable
{
    /**
     * Suggestions.
     *
     * @var Term[]
     */
    protected $terms;

    /**
     * Constructor.
     *
     * @param Term[] $terms
     */
    public function __construct(array $terms)
    {
        $this->terms = $terms;
    }

    /**
     * Get Terms.
     *
     * @return Term[]
     */
    public function getTerms(): array
    {
        return $this->terms;
    }

    /**
     * Get results for a specific term.
     *
     * @param string $term
     *
     * @return Term|null
     */
    public function getTerm(string $term): ?Term
    {
        return $this->terms[$term] ?? null;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->terms);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->terms);
    }
}

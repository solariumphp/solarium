<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Suggester\Result;

/**
 * Suggester query term result.
 */
class Term implements \IteratorAggregate, \Countable
{
    /**
     * NumFound.
     *
     * @var int
     */
    protected $numFound;

    /**
     * Suggestions.
     *
     * @var array
     */
    protected $suggestions;

    /**
     * Constructor.
     *
     * @param int   $numFound
     * @param array $suggestions
     */
    public function __construct(int $numFound, array $suggestions)
    {
        $this->numFound = $numFound;
        $this->suggestions = $suggestions;
    }

    /**
     * Get NumFound.
     *
     * @return int
     */
    public function getNumFound(): int
    {
        return $this->numFound;
    }

    /**
     * Get suggestions.
     *
     * @return array
     */
    public function getSuggestions(): array
    {
        return $this->suggestions;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->suggestions);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->suggestions);
    }
}

<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Spellcheck\Result;

/**
 * Spellcheck query term result.
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
     * StartOffset.
     *
     * @var int
     */
    protected $startOffset;

    /**
     * EndOffset.
     *
     * @var int
     */
    protected $endOffset;

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
     * @param int   $startOffset
     * @param int   $endOffset
     * @param array $suggestions
     */
    public function __construct(int $numFound, int $startOffset, int $endOffset, array $suggestions)
    {
        $this->numFound = $numFound;
        $this->startOffset = $startOffset;
        $this->endOffset = $endOffset;
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
     * Get StartOffset.
     *
     * @return int
     */
    public function getStartOffset(): int
    {
        return $this->startOffset;
    }

    /**
     * Get EndOffset.
     *
     * @return int
     */
    public function getEndOffset(): int
    {
        return $this->endOffset;
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

<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Spellcheck;

/**
 * Select component spellcheck collation result.
 */
class Collation implements \IteratorAggregate, \Countable
{
    /**
     * Query.
     *
     * @var string
     */
    protected $query;

    /**
     * Hit count.
     *
     * @var int
     */
    protected $hits;

    /**
     * Corrections.
     *
     * @var array
     */
    protected $corrections;

    /**
     * Constructor.
     *
     * @param string $query
     * @param int    $hits
     * @param array  $corrections
     */
    public function __construct(string $query, ?int $hits, array $corrections)
    {
        $this->query = $query;
        $this->hits = $hits;
        $this->corrections = $corrections;
    }

    /**
     * Get query string.
     *
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Get hit count.
     *
     * Only available if ExtendedResults was enabled in your query
     *
     * @return int|null
     */
    public function getHits(): ?int
    {
        return $this->hits;
    }

    /**
     * Get all corrrections.
     *
     * Only available if ExtendedResults was enabled in your query
     *
     * @return array
     */
    public function getCorrections(): ?array
    {
        return $this->corrections;
    }

    /**
     * IteratorAggregate implementation.
     *
     * Only available if ExtendedResults was enabled in your query
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->corrections);
    }

    /**
     * Countable implementation.
     *
     * Only available if ExtendedResults was enabled in your query
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->corrections);
    }
}

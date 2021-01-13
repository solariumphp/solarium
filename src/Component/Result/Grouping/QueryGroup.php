<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Grouping;

use Solarium\Core\Query\AbstractQuery;

/**
 * Select component grouping query group result.
 *
 * @since 2.1.0
 */
class QueryGroup implements \IteratorAggregate, \Countable
{
    /**
     * Match count.
     *
     * @var int
     */
    protected $matches;

    /**
     * NumFound value.
     *
     * @var int
     */
    protected $numFound;

    /**
     * Start offset.
     *
     * @var int|null
     */
    protected $start;

    /**
     * Maximum score in group.
     *
     * @var float|null
     */
    protected $maximumScore;

    /**
     * Group documents array.
     *
     * @var array
     */
    protected $documents;

    /**
     * @var AbstractQuery
     */
    protected $query;

    /**
     * Constructor.
     *
     * @param int|null      $matches
     * @param int|null      $numFound
     * @param int|null      $start
     * @param float|null    $maximumScore
     * @param array         $documents
     * @param AbstractQuery $query
     */
    public function __construct(?int $matches, ?int $numFound, ?int $start, ?float $maximumScore, array $documents, ?AbstractQuery $query = null)
    {
        $this->matches = $matches;
        $this->numFound = $numFound;
        $this->start = $start;
        $this->maximumScore = $maximumScore;
        $this->documents = $documents;
        $this->query = $query;
    }

    /**
     * Get matches value.
     *
     * @return int
     */
    public function getMatches(): int
    {
        return $this->matches;
    }

    /**
     * Get numFound value.
     *
     * @return int
     */
    public function getNumFound(): int
    {
        return $this->numFound;
    }

    /**
     * Get start value.
     *
     * @return int|null
     */
    public function getStart(): ?int
    {
        return $this->start;
    }

    /**
     * Get maximumScore value.
     *
     * @return float|null
     */
    public function getMaximumScore(): ?float
    {
        return $this->maximumScore;
    }

    /**
     * Get all documents.
     *
     * @return array
     */
    public function getDocuments(): array
    {
        return $this->documents;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->getDocuments());
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->getDocuments());
    }
}

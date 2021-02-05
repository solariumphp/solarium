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
 * Select component grouping field value group result.
 *
 * @since 2.1.0
 */
class ValueGroup implements \IteratorAggregate, \Countable
{
    /**
     * Field value.
     *
     * @var string
     */
    protected $value;

    /**
     * NumFound.
     *
     * @var int
     */
    protected $numFound;

    /**
     * Start position.
     *
     * @var int|null
     */
    protected $start;

    /**
     * Documents in this group.
     *
     * @var array
     */
    protected $documents;

    /**
     * Maximum score in group.
     *
     * @var float|null
     */
    protected $maximumScore;

    /**
     * @var AbstractQuery
     */
    protected $query;

    /**
     * Constructor.
     *
     * @param string|null   $value
     * @param int|null      $numFound
     * @param int|null      $start
     * @param array         $documents
     * @param float|null    $maxScore
     * @param AbstractQuery $query
     */
    public function __construct(?string $value, ?int $numFound, ?int $start, array $documents, ?float $maxScore = null, ?AbstractQuery $query = null)
    {
        $this->value = $value;
        $this->numFound = $numFound;
        $this->start = $start;
        $this->documents = $documents;
        $this->maximumScore = $maxScore;
        $this->query = $query;
    }

    /**
     * Get value.
     *
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * Get numFound.
     *
     * @return int|null
     */
    public function getNumFound(): ?int
    {
        return $this->numFound;
    }

    /**
     * Get start.
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

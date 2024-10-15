<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\MoreLikeThis;

use Solarium\Core\Query\DocumentInterface;

/**
 * Select component morelikethis result item.
 */
class Result implements \IteratorAggregate, \Countable
{
    /**
     * Document instances array.
     *
     * @var array
     */
    protected $documents;

    /**
     * Solr numFound.
     *
     * This is NOT the number of MLT documents fetched from Solr!
     *
     * @var int
     */
    protected $numFound;

    /**
     * Maximum score in this MLT set.
     *
     * @var float
     */
    protected $maximumScore;

    /**
     * Constructor.
     *
     * @param int        $numFound
     * @param float|null $maxScore
     * @param array      $documents
     */
    public function __construct(int $numFound, ?float $maxScore = null, array $documents = [])
    {
        $this->numFound = $numFound;
        $this->maximumScore = $maxScore;
        $this->documents = $documents;
    }

    /**
     * Get Solr numFound.
     *
     * Returns the number of MLT documents found by Solr (this is NOT the
     * number of documents fetched from Solr!)
     *
     * @return int
     */
    public function getNumFound(): int
    {
        return $this->numFound;
    }

    /**
     * Get maximum score in the MLT document set.
     *
     * @return float
     */
    public function getMaximumScore(): ?float
    {
        return $this->maximumScore;
    }

    /**
     * Get all documents.
     *
     * @return DocumentInterface[]
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
        return new \ArrayIterator($this->documents);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->documents);
    }
}

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
     * MLT interesting terms.
     *
     * Only available if mlt.interestingTerms wasn't 'none'.
     *
     * @var array|null
     */
    protected $interestingTerms;

    /**
     * Constructor.
     *
     * @param int    $numFound
     * @param float  $maxScore
     * @param array  $documents
     * @param ?array $interestingTerms
     */
    public function __construct(int $numFound, float $maxScore = null, array $documents = [], ?array $interestingTerms = null)
    {
        $this->numFound = $numFound;
        $this->maximumScore = $maxScore;
        $this->documents = $documents;
        $this->interestingTerms = $interestingTerms;
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
     * Get MLT interesting terms.
     *
     * This will show what "interesting" terms are used for the MoreLikeThis
     * query. These are the top tf/idf terms.
     *
     * If mlt.interestingTerms was 'list', a flat list is returned.
     *
     * If mlt.interestingTerms was 'details',
     * this shows you the term and boost used for each term. Unless
     * mlt.boost was true all terms will have boost=1.0.
     *
     * If mlt.interestingTerms was 'none', the terms aren't available
     * and null is returned.
     *
     * @return array
     */
    public function getInterestingTerms(): ?array
    {
        return $this->interestingTerms;
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

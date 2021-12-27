<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\MoreLikeThis;

use Solarium\Exception\UnexpectedValueException;

/**
 * Select component morelikethis result.
 */
class MoreLikeThis implements \IteratorAggregate, \Countable
{
    /**
     * Result array.
     *
     * @var array
     */
    protected $results;

    /**
     * Interesting terms.
     *
     * Only available if mlt.interestingTerms wasn't 'none'.
     *
     * @var array|null
     */
    protected $interestingTerms;

    /**
     * Constructor.
     *
     * @param array      $results
     * @param array|null $interestingTerms
     */
    public function __construct(array $results, ?array $interestingTerms)
    {
        $this->results = $results;
        $this->interestingTerms = $interestingTerms;
    }

    /**
     * Get a result by key.
     *
     * @param mixed $key
     *
     * @return Result|null
     */
    public function getResult($key): ?Result
    {
        return $this->results[$key] ?? null;
    }

    /**
     * Get all results.
     *
     * @return Result[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * Get a list of interesting terms by document key.
     *
     * This will show what "interesting" terms are used for the MoreLikeThis
     * query. These are the top TF/IDF terms.
     *
     * If mlt.interestingTerms was 'list', a flat list is returned.
     *
     * If mlt.interestingTerms was 'details',
     * this shows you the term and boost used for each term. Unless
     * mlt.boost was true all terms will have boost=1.0.
     *
     * If mlt.interestingTerms was 'none', the terms aren't available
     * and an exception is thrown.
     *
     * @param mixed $key
     *
     * @throws UnexpectedValueException
     *
     * @return array|null
     */
    public function getInterestingTerm($key): ?array
    {
        if (null === $this->interestingTerms) {
            throw new UnexpectedValueException('interestingterms is none');
        }

        return $this->interestingTerms[$key] ?? null;
    }

    /**
     * Get interesting terms for all documents.
     *
     * If mlt.interestingTerms was 'none', the terms aren't available
     * and an exception is thrown.
     *
     * @see getInterestingTerm() for a description of how the terms will be returned for each document key
     *
     * @throws UnexpectedValueException
     *
     * @return array
     */
    public function getInterestingTerms(): array
    {
        if (null === $this->interestingTerms) {
            throw new UnexpectedValueException('interestingterms is none');
        }

        return $this->interestingTerms;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->results);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->results);
    }
}

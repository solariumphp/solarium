<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Facet;

/**
 * Select field facet result.
 *
 * A field facet will usually return a dataset of multiple rows, in each row a
 * value and its count. You can access the values as an array using
 * {@link getValues()} or iterate this object.
 */
class Buckets implements FacetResultInterface, \IteratorAggregate, \Countable
{
    /**
     * Value array.
     *
     * @var Bucket[]
     */
    protected $buckets;

    /**
     * numBuckets.
     *
     * @var int|null
     */
    protected $numBuckets;

    /**
     * Constructor.
     *
     * @param Bucket[] $buckets
     * @param int|null $numBuckets
     */
    public function __construct(array $buckets, ?int $numBuckets = null)
    {
        $this->buckets = $buckets;
        $this->numBuckets = $numBuckets;
    }

    /**
     * Get all values.
     *
     * @return Bucket[]
     */
    public function getBuckets(): array
    {
        return $this->buckets;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->buckets);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count(): int
    {
        return \count($this->buckets);
    }

    /**
     * Get total bucket count for JSON facet
     * requires 'numBuckets':true in request.
     *
     * @return int|null
     */
    public function getNumBuckets(): ?int
    {
        return $this->numBuckets;
    }
}

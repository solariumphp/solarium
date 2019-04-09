<?php

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
     * Constructor.
     *
     * @param Bucket[] $buckets
     */
    public function __construct(array $buckets)
    {
        $this->buckets = $buckets;
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
        return count($this->buckets);
    }
}

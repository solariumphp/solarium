<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Result\Facet;

use Solarium\Component\Facet\FacetInterface;
use Solarium\Component\Result\FacetSet;

/**
 * Select field facet result.
 *
 * A field facet will usually return a dataset of multiple rows, in each row a
 * value and its count. You can access the values as an array using
 * {@link getValues()} or iterate this object.
 */
class Bucket implements \IteratorAggregate, \Countable
{
    /**
     * Value.
     *
     * @var string
     */
    protected $value;

    /**
     * Count.
     *
     * @var int
     */
    protected $count;

    /**
     * Facet set.
     *
     * @var FacetSet
     */
    protected $facetSet;

    /**
     * Bucket constructor.
     *
     * @param string   $value
     * @param int      $count
     * @param FacetSet $facets
     */
    public function __construct(string $value, int $count, FacetSet $facets)
    {
        $this->value = $value;
        $this->count = $count;
        $this->facetSet = $facets;
    }

    /**
     * Get the value.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Get the count.
     *
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Get nested facets.
     *
     * @return FacetInterface[]
     */
    public function getFacets(): array
    {
        return $this->facetSet->getFacets();
    }

    /**
     * Get nested facet set.
     *
     * @return FacetSet
     */
    public function getFacetSet(): FacetSet
    {
        return $this->facetSet;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return $this->facetSet->getIterator();
    }

    /**
     * Countable implementation.
     *
     * @return int the amount of nested facets
     */
    public function count(): int
    {
        return \count($this->facetSet->getFacets());
    }
}

<?php

namespace Solarium\Component\Result;

use Solarium\Component\Result\Facet\FacetResultInterface;

/**
 * Select component facetset result.
 */
class FacetSet implements \IteratorAggregate, \Countable
{
    /**
     * Facet array.
     *
     * @var FacetResultInterface[]
     */
    protected $facets;

    /**
     * Constructor.
     *
     * @param FacetResultInterface[] $facets
     */
    public function __construct(array $facets)
    {
        $this->facets = $facets;
    }

    /**
     * Get a facet by key.
     *
     * @param mixed $key
     *
     * @return FacetResultInterface|null
     */
    public function getFacet($key): ?FacetResultInterface
    {
        return $this->facets[$key] ?? null;
    }

    /**
     * Get all facet results.
     *
     * @return FacetResultInterface[]
     */
    public function getFacets(): array
    {
        return $this->facets;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->facets);
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count()
    {
        return count($this->facets);
    }
}

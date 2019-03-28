<?php

namespace Solarium\Component\Result;

use Solarium\Component\Facet\FacetInterface;

/**
 * Select component facetset result.
 */
class FacetSet implements \IteratorAggregate, \Countable
{
    /**
     * Facet array.
     *
     * @var array
     */
    protected $facets;

    /**
     * Constructor.
     *
     * @param array $facets
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
     * @return FacetInterface|null
     */
    public function getFacet($key): ?FacetInterface
    {
        if (isset($this->facets[$key])) {
            return $this->facets[$key];
        }
        return null;
    }

    /**
     * Get all facet results.
     *
     * @return array
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

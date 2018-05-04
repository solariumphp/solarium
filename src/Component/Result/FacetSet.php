<?php

namespace Solarium\Component\Result;

use Solarium\Component\Result\Facet\Field;

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
    public function __construct($facets)
    {
        $this->facets = $facets;
    }

    /**
     * Get a facet by key.
     *
     * @param mixed $key
     *
     * @return Field
     */
    public function getFacet($key)
    {
        if (isset($this->facets[$key])) {
            return $this->facets[$key];
        }
    }

    /**
     * Get all results.
     *
     * @return Field[]
     */
    public function getFacets()
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

<?php

namespace Solarium\Component\Result\Facet;

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
    protected $facets;

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
        $this->facets = $facets;
    }

    /**
     * Get the value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get the count.
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Get nested facets.
     *
     * @return FacetSet
     */
    public function getFacets()
    {
        return $this->facetset;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return $this->facetset->getIterator();
    }

    /**
     * Countable implementation.
     *
     * @return int the amount of nested facets
     */
    public function count()
    {
        return count($this->facetset);
    }
}

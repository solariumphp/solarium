<?php

namespace Solarium\Component;

use Solarium\Component\Facet\FacetInterface;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\OutOfBoundsException;

/**
 * FacetSet interface.
 */
interface FacetSetInterface
{
    /**
     * Add a facet.
     *
     * @param FacetInterface|array $facet
     *
     * @throws InvalidArgumentException
     *
     * @return \Solarium\Component\FacetSet
     */
    public function addFacet($facet);

    /**
     * Add multiple facets.
     *
     * @param array $facets
     *
     * @return \Solarium\Component\FacetSet
     */
    public function addFacets(array $facets);

    /**
     * Get a facet.
     *
     * @param string $key
     *
     * @return FacetInterface
     */
    public function getFacet($key);

    /**
     * Get all facets.
     *
     * @return FacetInterface[]
     */
    public function getFacets();

    /**
     * Remove a single facet.
     *
     * You can remove a facet by passing its key or the facet instance
     *
     * @param string|FacetInterface $facet
     *
     * @return \Solarium\Component\FacetSet
     */
    public function removeFacet($facet);

    /**
     * Remove all facets.
     *
     * @return \Solarium\Component\FacetSet
     */
    public function clearFacets();

    /**
     * Set multiple facets.
     *
     * This overwrites any existing facets
     *
     * @param FacetInterface[] $facets
     */
    public function setFacets($facets);

    /**
     * Create a facet instance.
     *
     * If you supply a string as the first arguments ($options) it will be used as the key for the facet
     * and it will be added to this query.
     * If you supply an options array/object that contains a key the facet will also be added to the query.
     *
     * When no key is supplied the facet cannot be added, in that case you will need to add it manually
     * after setting the key, by using the addFacet method.
     *
     *
     * @param string            $type
     * @param array|object|null $options
     * @param bool              $add
     *
     * @throws OutOfBoundsException
     *
     * @return FacetInterface
     */
    public function createFacet(string $type, $options = null, bool $add = true);
}

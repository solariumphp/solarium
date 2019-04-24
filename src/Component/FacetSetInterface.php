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
     * Facet type field.
     */
    const FACET_FIELD = 'field';

    /**
     * Facet type query.
     */
    const FACET_QUERY = 'query';

    /**
     * Facet type multiquery.
     */
    const FACET_MULTIQUERY = 'multiquery';

    /**
     * Facet type range.
     */
    const FACET_RANGE = 'range';

    /**
     * Facet type pivot.
     */
    const FACET_PIVOT = 'pivot';

    /**
     * Facet type interval.
     */
    const FACET_INTERVAL = 'interval';

    /**
     * Facet type field.
     */
    const JSON_FACET_TERMS = 'json_terms';

    /**
     * Facet type query.
     */
    const JSON_FACET_QUERY = 'json_query';

    /**
     * Facet type range.
     */
    const JSON_FACET_RANGE = 'json_range';

    /**
     * Facet type range.
     */
    const JSON_FACET_AGGREGATION = 'json_aggregation';

    /**
     * Add a facet.
     *
     * @param FacetInterface|array $facet
     *
     * @throws InvalidArgumentException
     *
     * @return \Solarium\Component\FacetSet
     */
    public function addFacet($facet): self;

    /**
     * Add multiple facets.
     *
     * @param array $facets
     *
     * @return \Solarium\Component\FacetSet
     */
    public function addFacets(array $facets): self;

    /**
     * Get a facet.
     *
     * @param string $key
     *
     * @return FacetInterface|null
     */
    public function getFacet(string $key): ?FacetInterface;

    /**
     * Get all facets.
     *
     * @return FacetInterface[]
     */
    public function getFacets(): array;

    /**
     * Remove a single facet.
     *
     * You can remove a facet by passing its key or the facet instance
     *
     * @param string|FacetInterface $facet
     *
     * @return \Solarium\Component\FacetSet
     */
    public function removeFacet($facet): self;

    /**
     * Remove all facets.
     *
     * @return \Solarium\Component\FacetSet
     */
    public function clearFacets(): self;

    /**
     * Set multiple facets.
     *
     * This overwrites any existing facets
     *
     * @param FacetInterface[] $facets
     *
     * @return \Solarium\Component\FacetSet
     */
    public function setFacets(array $facets): self;

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
    public function createFacet(string $type, $options = null, bool $add = true): FacetInterface;
}

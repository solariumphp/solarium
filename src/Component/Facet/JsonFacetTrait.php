<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSetInterface;
use Solarium\Component\FacetSetTrait;
use Solarium\Core\Query\Helper;
use Solarium\Exception\InvalidArgumentException;

/**
 * JSON facets.
 *
 * @see https://solr.apache.org/guide/json-facet-api.html
 */
trait JsonFacetTrait
{
    use FacetSetTrait {
        addFacet as facetSetAddFacet;
        removeFacet as facetSetRemoveFacet;
        clearFacets as facetSetClearFacets;
    }

    /**
     * Facet type mapping.
     */
    protected array $facetTypes = [
        FacetSetInterface::JSON_FACET_TERMS => 'Solarium\Component\Facet\JsonTerms',
        FacetSetInterface::JSON_FACET_QUERY => 'Solarium\Component\Facet\JsonQuery',
        FacetSetInterface::JSON_FACET_RANGE => 'Solarium\Component\Facet\JsonRange',
        FacetSetInterface::JSON_FACET_AGGREGATION => 'Solarium\Component\Facet\JsonAggregation',
    ];

    /**
     * Get the domain filter.
     *
     * @return array|string|null
     */
    public function getDomainFilter(): array|string|null
    {
        $domain = $this->getOption('domain');

        return $domain['filter'] ?? null;
    }

    /**
     * Set the domain filter query string.
     *
     * This overwrites the current value.
     *
     * @param string     $query
     * @param array|null $bind  Bind values for placeholders in the query string
     */
    public function setDomainFilterQuery(string $query, ?array $bind = null): static
    {
        if (null !== $bind) {
            $helper = new Helper();
            $query = $helper->assemble($query, $bind);
        }

        $filter = $this->getDomainFilter();
        if (!$filter || \is_string($filter)) {
            $this->setOption('domain', ['filter' => $query]);

            return $this;
        }

        foreach ($filter as &$paramOrQuery) {
            if (\is_string($paramOrQuery)) {
                $paramOrQuery = $query;

                $this->setOption('domain', ['filter' => $filter]);

                return $this;
            }
        }
        unset($paramOrQuery);

        /* @noinspection UnsupportedStringOffsetOperationsInspection */
        $filter[] = $query;

        $this->setOption('domain', ['filter' => $filter]);

        return $this;
    }

    /**
     * Adds a domain filter parameter.
     *
     * @param string $param
     */
    public function addDomainFilterParameter(string $param): static
    {
        $filter = $this->getDomainFilter();
        if (!$filter) {
            $this->setOption('domain', ['filter' => ['param' => $param]]);

            return $this;
        }

        if (\is_string($filter) || 1 === \count($filter)) {
            $this->setOption('domain', ['filter' => [$filter, ['param' => $param]]]);

            return $this;
        }

        foreach ($filter as &$paramOrQuery) {
            if (\is_array($paramOrQuery) && $paramOrQuery['param'] === $param) {
                return $this;
            }
        }
        unset($paramOrQuery);

        /* @noinspection UnsupportedStringOffsetOperationsInspection */
        $filter[] = ['param' => $param];

        $this->setOption('domain', ['filter' => $filter]);

        return $this;
    }

    /**
     * Serializes nested facets as option "facet" and returns that array structure.
     *
     * @return array|string
     */
    public function serialize()
    {
        // Strip 'json_' prefix.
        $this->setOption('type', substr($this->getType(), 5));

        $facets = [];
        foreach ($this->getFacets() as $key => $facet) {
            $facets[$key] = $facet->serialize();
        }

        if ($facets) {
            $this->setOption('facet', $facets);
        } elseif (isset($this->options['facet'])) {
            unset($this->options['facet']);
        }

        $options = $this->getOptions();
        unset($options['key']);

        return $options;
    }

    /**
     * Add a facet.
     *
     * @param FacetInterface|array $facet
     *
     * @throws InvalidArgumentException
     */
    public function addFacet(FacetInterface|array $facet): static
    {
        if ($facet instanceof JsonFacetInterface) {
            $this->facetSetAddFacet($facet);
            $this->serialize();

            return $this;
        }

        throw new InvalidArgumentException('Only JSON facets can be nested.');
    }

    /**
     * Get a facet.
     *
     * @param string $key
     *
     * @return JsonFacetInterface|null
     */
    public function getFacet(string $key): ?JsonFacetInterface
    {
        return $this->facets[$key] ?? null;
    }

    /**
     * Get all facets.
     *
     * @return JsonFacetInterface[]
     */
    public function getFacets(): array
    {
        return $this->facets;
    }

    /**
     * Remove a single facet.
     *
     * You can remove a facet by passing its key or the facet instance
     *
     * @param string|FacetInterface $facet
     */
    public function removeFacet(string|FacetInterface $facet): static
    {
        $this->facetSetRemoveFacet($facet);
        $this->serialize();

        return $this;
    }

    /**
     * Remove all facets.
     */
    public function clearFacets(): static
    {
        $this->facetSetClearFacets();
        $this->serialize();

        return $this;
    }
}

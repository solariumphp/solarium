<?php

namespace Solarium\Component\Facet;

use Solarium\Component\FacetSetInterface;
use Solarium\Component\FacetSetTrait;
use Solarium\Core\Query\Helper;
use Solarium\Exception\InvalidArgumentException;

/**
 * Json facets.
 *
 * @see https://lucene.apache.org/solr/guide/7_3/json-facet-api.html
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
     *
     * @var array
     */
    protected $facetTypes = [
        FacetSetInterface::JSON_FACET_TERMS => 'Solarium\Component\Facet\JsonTerms',
        FacetSetInterface::JSON_FACET_QUERY => 'Solarium\Component\Facet\JsonQuery',
        FacetSetInterface::JSON_FACET_RANGE => 'Solarium\Component\Facet\JsonRange',
        FacetSetInterface::JSON_FACET_AGGREGATION => 'Solarium\Component\Facet\JsonAggregation',
    ];

    /**
     * Set the domain filter query string.
     *
     * This overwrites the current value.
     *
     * @param string $query
     * @param array  $bind  Bind values for placeholders in the query string
     *
     * @return self Provides fluent interface
     */
    public function setDomainFilterQuery($query, $bind = null)
    {
        if (null !== $bind) {
            $helper = new Helper();
            $query = $helper->assemble($query, $bind);
        }

        return $this->setOption('domain', ['filter' => $query]);
    }

    /**
     * Get the domain filter query string.
     *
     * @return string
     */
    public function getDomainFilterQuery()
    {
        return $this->getOption('domain')['filter'];
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
     *
     * @param FacetInterface|array $facet
     *
     * @throws InvalidArgumentException
     *
     * @return self Provides fluent interface
     */
    public function addFacet($facet)
    {
        if ($facet instanceof JsonFacetInterface) {
            $this->facetSetAddFacet($facet);
            $this->serialize();

            return $this;
        } else {
            throw new InvalidArgumentException('Only JSON facets can be nested.');
        }
    }

    /**
     * Remove a single facet.
     *
     * You can remove a facet by passing its key or the facet instance
     *
     * @param string|FacetInterface $facet
     *
     * @return self Provides fluent interface
     */
    public function removeFacet($facet)
    {
        $this->facetSetRemoveFacet($facet);
        $this->serialize();

        return $this;
    }

    /**
     * Remove all facets.
     *
     * @return self Provides fluent interface
     */
    public function clearFacets()
    {
        $this->facetSetClearFacets();
        $this->serialize();

        return $this;
    }
}

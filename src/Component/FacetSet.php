<?php

namespace Solarium\Component;

use Solarium\Component\Facet\FacetInterface;
use Solarium\Component\RequestBuilder\FacetSet as RequestBuilder;
use Solarium\Component\ResponseParser\FacetSet as ResponseParser;

/**
 * FacetSet component.
 */
class FacetSet extends AbstractComponent implements FacetSetInterface
{
    use FacetSetTrait;

    /**
     * Facet type mapping.
     *
     * @var array
     */
    protected $facetTypes = [
        FacetSetInterface::FACET_FIELD => 'Solarium\Component\Facet\Field',
        FacetSetInterface::FACET_QUERY => 'Solarium\Component\Facet\Query',
        FacetSetInterface::FACET_MULTIQUERY => 'Solarium\Component\Facet\MultiQuery',
        FacetSetInterface::FACET_RANGE => 'Solarium\Component\Facet\Range',
        FacetSetInterface::FACET_PIVOT => 'Solarium\Component\Facet\Pivot',
        FacetSetInterface::FACET_INTERVAL => 'Solarium\Component\Facet\Interval',
        FacetSetInterface::JSON_FACET_AGGREGATION => 'Solarium\Component\Facet\JsonAggregation',
        FacetSetInterface::JSON_FACET_TERMS => 'Solarium\Component\Facet\JsonTerms',
        FacetSetInterface::JSON_FACET_QUERY => 'Solarium\Component\Facet\JsonQuery',
        FacetSetInterface::JSON_FACET_RANGE => 'Solarium\Component\Facet\JsonRange',
    ];

    /**
     * Facets.
     *
     * @var FacetInterface[]
     */
    protected $facets = [];

    /**
     * Get component type.
     *
     * @return string
     */
    public function getType()
    {
        return ComponentAwareQueryInterface::COMPONENT_FACETSET;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder()
    {
        return new RequestBuilder();
    }

    /**
     * Get a response parser for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser()
    {
        return new ResponseParser();
    }

    /**
     * Allow extraction of facets without having to define
     * them on the query.
     *
     * @param bool $extract
     *
     * @return self Provides fluent interface
     */
    public function setExtractFromResponse($extract)
    {
        return $this->setOption('extractfromresponse', $extract);
    }

    /**
     * Get the extractfromresponse option value.
     *
     * @return bool
     */
    public function getExtractFromResponse()
    {
        return $this->getOption('extractfromresponse');
    }

    /**
     * Limit the terms for faceting by a prefix.
     *
     * This is a global value for all facets in this facetset
     *
     * @param string $prefix
     *
     * @return self Provides fluent interface
     */
    public function setPrefix($prefix)
    {
        return $this->setOption('prefix', $prefix);
    }

    /**
     * Get the facet prefix.
     *
     * This is a global value for all facets in this facetset
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->getOption('prefix');
    }

    /**
     * Limit the terms for faceting by a string they must contain. Since Solr 5.1.
     *
     * This is a global value for all facets in this facetset
     *
     * @param string $contains
     *
     * @return self Provides fluent interface
     */
    public function setContains($contains)
    {
        return $this->setOption('contains', $contains);
    }

    /**
     * Get the facet contains.
     *
     * This is a global value for all facets in this facetset
     *
     * @return string
     */
    public function getContains()
    {
        return $this->getOption('contains');
    }

    /**
     * Case sensitivity of matching string that facet terms must contain. Since Solr 5.1.
     *
     * This is a global value for all facets in this facetset
     *
     * @param bool $containsIgnoreCase
     *
     * @return self Provides fluent interface
     */
    public function setContainsIgnoreCase($containsIgnoreCase)
    {
        return $this->setOption('containsignorecase', $containsIgnoreCase);
    }

    /**
     * Get the case sensitivity of facet contains.
     *
     * This is a global value for all facets in this facetset
     *
     * @return bool
     */
    public function getContainsIgnoreCase()
    {
        return $this->getOption('containsignorecase');
    }

    /**
     * Set the facet sort order.
     *
     * Use one of the SORT_* constants as the value
     *
     * This is a global value for all facets in this facetset
     *
     * @param string $sort
     *
     * @return self Provides fluent interface
     */
    public function setSort($sort)
    {
        return $this->setOption('sort', $sort);
    }

    /**
     * Get the facet sort order.
     *
     * This is a global value for all facets in this facetset
     *
     * @return string
     */
    public function getSort()
    {
        return $this->getOption('sort');
    }

    /**
     * Set the facet limit.
     *
     *  This is a global value for all facets in this facetset
     *
     * @param int $limit
     *
     * @return self Provides fluent interface
     */
    public function setLimit($limit)
    {
        return $this->setOption('limit', $limit);
    }

    /**
     * Get the facet limit.
     *
     * This is a global value for all facets in this facetset
     *
     * @return string
     */
    public function getLimit()
    {
        return $this->getOption('limit');
    }

    /**
     * Set the facet mincount.
     *
     * This is a global value for all facets in this facetset
     *
     * @param int $minCount
     *
     * @return self Provides fluent interface
     */
    public function setMinCount($minCount)
    {
        return $this->setOption('mincount', $minCount);
    }

    /**
     * Get the facet mincount.
     *
     * This is a global value for all facets in this facetset
     *
     * @return int
     */
    public function getMinCount()
    {
        return $this->getOption('mincount');
    }

    /**
     * Set the missing count option.
     *
     * This is a global value for all facets in this facetset
     *
     * @param bool $missing
     *
     * @return self Provides fluent interface
     */
    public function setMissing($missing)
    {
        return $this->setOption('missing', $missing);
    }

    /**
     * Get the facet missing option.
     *
     * This is a global value for all facets in this facetset
     *
     * @return bool
     */
    public function getMissing()
    {
        return $this->getOption('missing');
    }

    /**
     * Get a facet field instance.
     *
     * @param mixed $options
     * @param bool  $add
     *
     * @return \Solarium\Component\Facet\Field
     */
    public function createFacetField($options = null, $add = true)
    {
        return $this->createFacet(FacetSetInterface::FACET_FIELD, $options, $add);
    }

    /**
     * Get a facet query instance.
     *
     * @param mixed $options
     * @param bool  $add
     *
     * @return \Solarium\Component\Facet\Query
     */
    public function createFacetQuery($options = null, $add = true)
    {
        return $this->createFacet(FacetSetInterface::FACET_QUERY, $options, $add);
    }

    /**
     * Get a facet multiquery instance.
     *
     * @param mixed $options
     * @param bool  $add
     *
     * @return \Solarium\Component\Facet\MultiQuery
     */
    public function createFacetMultiQuery($options = null, $add = true)
    {
        return $this->createFacet(FacetSetInterface::FACET_MULTIQUERY, $options, $add);
    }

    /**
     * Get a facet range instance.
     *
     * @param mixed $options
     * @param bool  $add
     *
     * @return \Solarium\Component\Facet\Range
     */
    public function createFacetRange($options = null, $add = true)
    {
        return $this->createFacet(FacetSetInterface::FACET_RANGE, $options, $add);
    }

    /**
     * Get a facet pivot instance.
     *
     * @param mixed $options
     * @param bool  $add
     *
     * @return \Solarium\Component\Facet\Pivot
     */
    public function createFacetPivot($options = null, $add = true)
    {
        return $this->createFacet(FacetSetInterface::FACET_PIVOT, $options, $add);
    }

    /**
     * Get a facet interval instance.
     *
     * @param mixed $options
     * @param bool  $add
     *
     * @return \Solarium\Component\Facet\Interval
     */
    public function createFacetInterval($options = null, $add = true)
    {
        return $this->createFacet(FacetSetInterface::FACET_INTERVAL, $options, $add);
    }

    /**
     * Get a json facet aggregation instance.
     *
     * @param mixed $options
     * @param bool  $add
     *
     * @return \Solarium\Component\Facet\JsonAggregation
     */
    public function createJsonFacetAggregation($options = null, $add = true)
    {
        return $this->createFacet(FacetSetInterface::JSON_FACET_AGGREGATION, $options, $add);
    }

    /**
     * Get a json facet terms instance.
     *
     * @param mixed $options
     * @param bool  $add
     *
     * @return \Solarium\Component\Facet\JsonTerms
     */
    public function createJsonFacetTerms($options = null, $add = true)
    {
        return $this->createFacet(FacetSetInterface::JSON_FACET_TERMS, $options, $add);
    }

    /**
     * Get a json facet query instance.
     *
     * @param mixed $options
     * @param bool  $add
     *
     * @return \Solarium\Component\Facet\JsonQuery
     */
    public function createJsonFacetQuery($options = null, $add = true)
    {
        return $this->createFacet(FacetSetInterface::JSON_FACET_QUERY, $options, $add);
    }

    /**
     * Get a json facet range instance.
     *
     * @param mixed $options
     * @param bool  $add
     *
     * @return \Solarium\Component\Facet\JsonRange
     */
    public function createJsonFacetRange($options = null, $add = true)
    {
        return $this->createFacet(FacetSetInterface::JSON_FACET_RANGE, $options, $add);
    }
}

<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component;

use Solarium\Component\Facet\FacetInterface;
use Solarium\Component\Facet\Field;
use Solarium\Component\Facet\FieldValueParametersInterface;
use Solarium\Component\Facet\FieldValueParametersTrait;
use Solarium\Component\Facet\Interval;
use Solarium\Component\Facet\JsonAggregation;
use Solarium\Component\Facet\JsonQuery;
use Solarium\Component\Facet\JsonRange;
use Solarium\Component\Facet\JsonTerms;
use Solarium\Component\Facet\MultiQuery;
use Solarium\Component\Facet\Pivot;
use Solarium\Component\Facet\PivotMinCountTrait;
use Solarium\Component\Facet\Query;
use Solarium\Component\Facet\Range;
use Solarium\Component\RequestBuilder\ComponentRequestBuilderInterface;
use Solarium\Component\RequestBuilder\FacetSet as RequestBuilder;
use Solarium\Component\ResponseParser\ComponentParserInterface;
use Solarium\Component\ResponseParser\FacetSet as ResponseParser;

/**
 * FacetSet component.
 */
class FacetSet extends AbstractComponent implements FacetSetInterface, FieldValueParametersInterface
{
    use FacetSetTrait;
    use FieldValueParametersTrait;
    use PivotMinCountTrait;

    /**
     * Facet type mapping.
     *
     * @var array
     */
    protected $facetTypes = [
        FacetSetInterface::FACET_FIELD => Field::class,
        FacetSetInterface::FACET_QUERY => Query::class,
        FacetSetInterface::FACET_MULTIQUERY => MultiQuery::class,
        FacetSetInterface::FACET_RANGE => Range::class,
        FacetSetInterface::FACET_PIVOT => Pivot::class,
        FacetSetInterface::FACET_INTERVAL => Interval::class,
        FacetSetInterface::JSON_FACET_AGGREGATION => JsonAggregation::class,
        FacetSetInterface::JSON_FACET_TERMS => JsonTerms::class,
        FacetSetInterface::JSON_FACET_QUERY => JsonQuery::class,
        FacetSetInterface::JSON_FACET_RANGE => JsonRange::class,
    ];

    /**
     * Get component type.
     *
     * @return string
     */
    public function getType(): string
    {
        return ComponentAwareQueryInterface::COMPONENT_FACETSET;
    }

    /**
     * Get a requestbuilder for this query.
     *
     * @return RequestBuilder
     */
    public function getRequestBuilder(): ComponentRequestBuilderInterface
    {
        return new RequestBuilder();
    }

    /**
     * Get a response parser for this query.
     *
     * @return ResponseParser
     */
    public function getResponseParser(): ?ComponentParserInterface
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
    public function setExtractFromResponse(bool $extract): self
    {
        $this->setOption('extractfromresponse', $extract);

        return $this;
    }

    /**
     * Get the extractfromresponse option value.
     *
     * @return bool|null
     */
    public function getExtractFromResponse(): ?bool
    {
        return $this->getOption('extractfromresponse');
    }

    /**
     * Get a facet field instance.
     *
     * @see FacetSetTrait::createFacet for more information on $options
     *
     * @param array|object|string|null $options
     * @param bool                     $add
     *
     * @return \Solarium\Component\Facet\Field|FacetInterface
     */
    public function createFacetField($options = null, bool $add = true): FacetInterface
    {
        return $this->createFacet(FacetSetInterface::FACET_FIELD, $options, $add);
    }

    /**
     * Get a facet query instance.
     *
     * @see FacetSetTrait::createFacet for more information on $options
     *
     * @param array|object|string|null $options
     * @param bool                     $add
     *
     * @return \Solarium\Component\Facet\Query
     */
    public function createFacetQuery($options = null, bool $add = true): FacetInterface
    {
        return $this->createFacet(FacetSetInterface::FACET_QUERY, $options, $add);
    }

    /**
     * Get a facet multiquery instance.
     *
     * @see FacetSetTrait::createFacet for more information on $options
     *
     * @param array|object|string|null $options
     * @param bool                     $add
     *
     * @return \Solarium\Component\Facet\MultiQuery
     */
    public function createFacetMultiQuery($options = null, bool $add = true): FacetInterface
    {
        return $this->createFacet(FacetSetInterface::FACET_MULTIQUERY, $options, $add);
    }

    /**
     * Get a facet range instance.
     *
     * @see FacetSetTrait::createFacet for more information on $options
     *
     * @param array|object|string|null $options
     * @param bool                     $add
     *
     * @return \Solarium\Component\Facet\Range
     */
    public function createFacetRange($options = null, bool $add = true): FacetInterface
    {
        return $this->createFacet(FacetSetInterface::FACET_RANGE, $options, $add);
    }

    /**
     * Get a facet pivot instance.
     *
     * @see FacetSetTrait::createFacet for more information on $options
     *
     * @param array|object|string|null $options
     * @param bool                     $add
     *
     * @return \Solarium\Component\Facet\Pivot
     */
    public function createFacetPivot($options = null, bool $add = true): FacetInterface
    {
        return $this->createFacet(FacetSetInterface::FACET_PIVOT, $options, $add);
    }

    /**
     * Get a facet interval instance.
     *
     * @see FacetSetTrait::createFacet for more information on $options
     *
     * @param array|object|string|null $options
     * @param bool                     $add
     *
     * @return \Solarium\Component\Facet\Interval
     */
    public function createFacetInterval($options = null, bool $add = true): FacetInterface
    {
        return $this->createFacet(FacetSetInterface::FACET_INTERVAL, $options, $add);
    }

    /**
     * Get a json facet aggregation instance.
     *
     * @see FacetSetTrait::createFacet for more information on $options
     *
     * @param array|object|string|null $options
     * @param bool                     $add
     *
     * @return \Solarium\Component\Facet\JsonAggregation
     */
    public function createJsonFacetAggregation($options = null, bool $add = true): FacetInterface
    {
        return $this->createFacet(FacetSetInterface::JSON_FACET_AGGREGATION, $options, $add);
    }

    /**
     * Get a json facet terms instance.
     *
     * @see FacetSetTrait::createFacet for more information on $options
     *
     * @param array|object|string|null $options
     * @param bool                     $add
     *
     * @return \Solarium\Component\Facet\JsonTerms
     */
    public function createJsonFacetTerms($options = null, bool $add = true): FacetInterface
    {
        return $this->createFacet(FacetSetInterface::JSON_FACET_TERMS, $options, $add);
    }

    /**
     * Get a json facet query instance.
     *
     * @see FacetSetTrait::createFacet for more information on $options
     *
     * @param array|object|string|null $options
     * @param bool                     $add
     *
     * @return \Solarium\Component\Facet\JsonQuery
     */
    public function createJsonFacetQuery($options = null, bool $add = true): FacetInterface
    {
        return $this->createFacet(FacetSetInterface::JSON_FACET_QUERY, $options, $add);
    }

    /**
     * Get a json facet range instance.
     *
     * @see FacetSetTrait::createFacet for more information on $options
     *
     * @param array|object|string|null $options
     * @param bool                     $add
     *
     * @return \Solarium\Component\Facet\JsonRange
     */
    public function createJsonFacetRange($options = null, bool $add = true): FacetInterface
    {
        return $this->createFacet(FacetSetInterface::JSON_FACET_RANGE, $options, $add);
    }
}

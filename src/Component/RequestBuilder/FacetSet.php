<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\RequestBuilder;

use Solarium\Component\Facet\Field as FacetField;
use Solarium\Component\Facet\Interval as FacetInterval;
use Solarium\Component\Facet\JsonFacetInterface;
use Solarium\Component\Facet\MultiQuery as FacetMultiQuery;
use Solarium\Component\Facet\Pivot as FacetPivot;
use Solarium\Component\Facet\Query as FacetQuery;
use Solarium\Component\Facet\Range as FacetRange;
use Solarium\Component\FacetSet as FacetSetComponent;
use Solarium\Component\FacetSetInterface;
use Solarium\Core\Client\Request;
use Solarium\Core\ConfigurableInterface;
use Solarium\Exception\UnexpectedValueException;
use Solarium\QueryType\Select\RequestBuilder;

/**
 * Add select component FacetSet to the request.
 */
class FacetSet extends RequestBuilder implements ComponentRequestBuilderInterface
{
    /**
     * Add request settings for FacetSet.
     *
     * @param FacetSetComponent $component
     * @param Request           $request
     *
     * @throws \Solarium\Exception\UnexpectedValueException
     *
     * @return Request
     */
    public function buildComponent(ConfigurableInterface $component, Request $request): Request
    {
        $facets = $component->getFacets();

        if (0 !== \count($facets)) {
            $nonJson = false;
            $jsonFacets = [];

            // create a list of facet fields
            // 1) filter for FACET_FIELD
            // 2) get field name
            // 3) count occurence
            $facetFields = array_count_values(array_map(
                static function ($value) {
                    return $value->getField();
                },
                array_filter($facets, static function ($value) {
                    return FacetSetInterface::FACET_FIELD === $value->getType();
                })
            ));
            foreach ($facets as $key => $facet) {
                switch ($facet->getType()) {
                    case FacetSetInterface::FACET_FIELD:
                        /* @var FacetField $facet */
                        $this->addFacetField($request, $facet, 1 < $facetFields[$facet->getField()]);
                        $nonJson = true;
                        break;
                    case FacetSetInterface::FACET_QUERY:
                        /* @var FacetQuery $facet */
                        $this->addFacetQuery($request, $facet);
                        $nonJson = true;
                        break;
                    case FacetSetInterface::FACET_MULTIQUERY:
                        /* @var FacetMultiQuery $facet */
                        $this->addFacetMultiQuery($request, $facet);
                        $nonJson = true;
                        break;
                    case FacetSetInterface::FACET_RANGE:
                        /* @var FacetRange $facet */
                        $this->addFacetRange($request, $facet);
                        $nonJson = true;
                        break;
                    case FacetSetInterface::FACET_PIVOT:
                        /* @var FacetPivot $facet */
                        $this->addFacetPivot($request, $facet);
                        $nonJson = true;
                        break;
                    case FacetSetInterface::FACET_INTERVAL:
                        /* @var FacetInterval $facet */
                        $this->addFacetInterval($request, $facet);
                        $nonJson = true;
                        break;
                    case FacetSetInterface::JSON_FACET_TERMS:
                    case FacetSetInterface::JSON_FACET_QUERY:
                    case FacetSetInterface::JSON_FACET_RANGE:
                    case FacetSetInterface::JSON_FACET_AGGREGATION:
                        /* @var JsonFacetInterface $facet */
                        $jsonFacets[$key] = $facet->serialize();
                        break;
                    default:
                        throw new UnexpectedValueException('Unknown facet type');
                }
            }

            if ($nonJson) {
                // enable non-json faceting
                $request->addParam('facet', 'true');

                // global facet params
                $request->addParam('facet.prefix', $component->getPrefix());
                $request->addParam('facet.contains', $component->getContains());
                $request->addParam('facet.contains.ignoreCase', $component->getContainsIgnoreCase());
                $request->addParam('facet.matches', $component->getMatches());
                $request->addParam('facet.sort', $component->getSort());
                $request->addParam('facet.limit', $component->getLimit());
                $request->addParam('facet.offset', $component->getOffset());
                $request->addParam('facet.mincount', $component->getMinCount());
                $request->addParam('facet.missing', $component->getMissing());
                $request->addParam('facet.method', $component->getMethod());
                $request->addParam('facet.enum.cache.minDf', $component->getEnumCacheMinimumDocumentFrequency());
                $request->addParam('facet.exists', $component->getExists());
                $request->addParam('facet.excludeTerms', $component->getExcludeTerms());
                $request->addParam('facet.overrequest.count', $component->getOverrequestCount());
                $request->addParam('facet.overrequest.ratio', $component->getOverrequestRatio());
                $request->addParam('facet.threads', $component->getThreads());
                $request->addParam('facet.pivot.mincount', $component->getPivotMinCount());
            }

            if ($jsonFacets) {
                $request->addParam('json.facet', json_encode($jsonFacets));
            }
        }

        return $request;
    }

    /**
     * Add params for a field facet to request.
     *
     * @param Request    $request
     * @param FacetField $facet
     * @param bool       $useLocalParams TRUE, if local params instead of global field params should be used. Must be set if the same field is used in different facets. Default is keeping the global field params (https://issues.apache.org/jira/browse/SOLR-6193)
     */
    public function addFacetField(Request $request, FacetField $facet, bool $useLocalParams = false): void
    {
        $field = $facet->getField();

        if ($useLocalParams) {
            $localParams = [
                'key' => $facet->getKey(),
                'ex' => $facet->getLocalParameters()->getExcludes(),
                'terms' => $facet->getLocalParameters()->getTerms(),
                'facet.prefix' => $facet->getPrefix(),
                'facet.contains' => $facet->getContains(),
                'facet.contains.ignoreCase' => $facet->getContainsIgnoreCase(),
                'facet.matches' => $facet->getMatches(),
                'facet.sort' => $facet->getSort(),
                'facet.limit' => $facet->getLimit(),
                'facet.offset' => $facet->getOffset(),
                'facet.mincount' => $facet->getMinCount(),
                'facet.missing' => $facet->getMissing(),
                'facet.method' => $facet->getMethod(),
                'facet.enum.cache.minDf' => $facet->getEnumCacheMinimumDocumentFrequency(),
                'facet.exists' => $facet->getExists(),
                'facet.excludeTerms' => $facet->getExcludeTerms(),
                'facet.overrequest.count' => $facet->getOverrequestCount(),
                'facet.overrequest.ratio' => $facet->getOverrequestRatio(),
                'facet.threads' => $facet->getThreads(),
            ];

            $request->addParam(
                'facet.field',
                $this->renderLocalParams(
                    $field,
                    $localParams
                )
            );
        } else {
            $request->addParam(
                'facet.field',
                $this->renderLocalParams(
                    $field,
                    $facet->getLocalParameters()->getParameters()
                )
            );

            $request->addParam("f.$field.facet.prefix", $facet->getPrefix());
            $request->addParam("f.$field.facet.contains", $facet->getContains());
            $request->addParam("f.$field.facet.contains.ignoreCase", $facet->getContainsIgnoreCase());
            $request->addParam("f.$field.facet.matches", $facet->getMatches());
            $request->addParam("f.$field.facet.sort", $facet->getSort());
            $request->addParam("f.$field.facet.limit", $facet->getLimit());
            $request->addParam("f.$field.facet.offset", $facet->getOffset());
            $request->addParam("f.$field.facet.mincount", $facet->getMinCount());
            $request->addParam("f.$field.facet.missing", $facet->getMissing());
            $request->addParam("f.$field.facet.method", $facet->getMethod());
            $request->addParam("f.$field.facet.enum.cache.minDf", $facet->getEnumCacheMinimumDocumentFrequency());
            $request->addParam("f.$field.facet.exists", $facet->getExists());
            $request->addParam("f.$field.facet.excludeTerms", $facet->getExcludeTerms());
            $request->addParam("f.$field.facet.overrequest.count", $facet->getOverrequestCount());
            $request->addParam("f.$field.facet.overrequest.ratio", $facet->getOverrequestRatio());
            $request->addParam("f.$field.facet.threads", $facet->getThreads());
        }
    }

    /**
     * Add params for a facet query to request.
     *
     * @param Request    $request
     * @param FacetQuery $facet
     */
    public function addFacetQuery($request, $facet): void
    {
        $request->addParam(
            'facet.query',
            $this->renderLocalParams(
                $facet->getQuery(),
                $facet->getLocalParameters()->getParameters()
            )
        );
    }

    /**
     * Add params for a multiquery facet to request.
     *
     * @param Request         $request
     * @param FacetMultiQuery $facet
     */
    public function addFacetMultiQuery($request, $facet): void
    {
        foreach ($facet->getQueries() as $facetQuery) {
            $this->addFacetQuery($request, $facetQuery);
        }
    }

    /**
     * Add params for a range facet to request.
     *
     * @param Request    $request
     * @param FacetRange $facet
     */
    public function addFacetRange($request, $facet): void
    {
        $field = $facet->getField();

        $request->addParam(
            'facet.range',
            $this->renderLocalParams(
                $field,
                $facet->getLocalParameters()->getParameters()
            )
        );

        $request->addParam("f.$field.facet.range.start", $facet->getStart());
        $request->addParam("f.$field.facet.range.end", $facet->getEnd());
        $request->addParam("f.$field.facet.range.gap", $facet->getGap());
        $request->addParam("f.$field.facet.range.hardend", $facet->getHardend());
        $request->addParam("f.$field.facet.mincount", $facet->getMinCount());

        foreach ($facet->getOther() as $otherValue) {
            $request->addParam("f.$field.facet.range.other", $otherValue);
        }

        foreach ($facet->getInclude() as $includeValue) {
            $request->addParam("f.$field.facet.range.include", $includeValue);
        }

        if (null !== $pivot = $facet->getPivot()) {
            $request->addParam(
                'facet.pivot',
                $this->renderLocalParams(
                    implode(',', $pivot->getFields()),
                    $pivot->getLocalParameters()->getParameters()
                )
            );
        }
    }

    /**
     * Add params for a range facet to request.
     *
     * @param Request    $request
     * @param FacetPivot $facet
     */
    public function addFacetPivot($request, $facet): void
    {
        $fields = $facet->getFields();
        $stats = $facet->getStats();

        if (\count($stats) > 0) {
            $key = ['stats' => implode('', $stats)];

            // when specifying stats, Solr sets the field as key
            $facet->setKey(implode(',', $fields));
        } else {
            $key = ['key' => $facet->getKey()];
        }

        $request->addParam(
            'facet.pivot',
            $this->renderLocalParams(
                implode(',', $fields),
                $facet->getLocalParameters()->getParameters()
            )
        );

        foreach ($fields as $field) {
            $request->addParam("f.$field.facet.pivot.mincount", $facet->getPivotMinCount());
            $request->addParam("f.$field.facet.limit", $facet->getLimit());
            $request->addParam("f.$field.facet.offset", $facet->getOffset());
            $request->addParam("f.$field.facet.sort", $facet->getSort());
            $request->addParam("f.$field.facet.overrequest.count", $facet->getOverrequestCount());
            $request->addParam("f.$field.facet.overrequest.ratio", $facet->getOverrequestRatio());
        }
    }

    /**
     * Add params for a interval facet to request.
     *
     * @param Request       $request
     * @param FacetInterval $facet
     */
    public function addFacetInterval($request, $facet): void
    {
        $field = $facet->getField();

        $request->addParam(
            'facet.interval',
            $this->renderLocalParams(
                $field,
                $facet->getLocalParameters()->getParameters()
            )
        );

        foreach ($facet->getSet() as $key => $setValue) {
            if (\is_string($key)) {
                $setValue = $this->renderLocalParams($setValue, ['key' => $key]);
            }
            $request->addParam("f.$field.facet.interval.set", $setValue);
        }
    }
}

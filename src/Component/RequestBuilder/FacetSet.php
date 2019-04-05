<?php

namespace Solarium\Component\RequestBuilder;

use Solarium\Component\Facet\Field as FacetField;
use Solarium\Component\Facet\Interval as FacetInterval;
use Solarium\Component\Facet\JsonFacetInterface;
use Solarium\Component\Facet\MultiQuery as FacetMultiQuery;
use Solarium\Component\Facet\Pivot as FacetPivot;
use Solarium\Component\Facet\Query as FacetQuery;
use Solarium\Component\Facet\Range as FacetRange;
use Solarium\Component\FacetSet as FacetsetComponent;
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
     *
     * @param FacetsetComponent $component
     * @param Request           $request
     *
     * @throws UnexpectedValueException
     *
     * @return Request
     */
    public function buildComponent(ConfigurableInterface $component, Request $request): Request
    {
        $facets = $component->getFacets();
        if (0 !== count($facets)) {
            $non_json = false;
            $json_facets = [];

            // create a list of facet fields
            // 1) filter for FACET_FIELD
            // 2) get field name
            // 3) count occurence
            $facet_fields = array_count_values(array_map(function ($value) {
                return $value->getField();
            }, array_filter($facets, function ($value) {
                return FacetSetInterface::FACET_FIELD == $value->getType();
            })));
            foreach ($facets as $key => $facet) {
                switch ($facet->getType()) {
                    case FacetSetInterface::FACET_FIELD:
                        /* @var FacetField $facet */
                        $this->addFacetField($request, $facet, (1 < $facet_fields[$facet->getField()]));
                        $non_json = true;
                        break;
                    case FacetSetInterface::FACET_QUERY:
                        /* @var FacetQuery $facet */
                        $this->addFacetQuery($request, $facet);
                        $non_json = true;
                        break;
                    case FacetSetInterface::FACET_MULTIQUERY:
                        /* @var FacetMultiQuery $facet */
                        $this->addFacetMultiQuery($request, $facet);
                        $non_json = true;
                        break;
                    case FacetSetInterface::FACET_RANGE:
                        /* @var FacetRange $facet */
                        $this->addFacetRange($request, $facet);
                        $non_json = true;
                        break;
                    case FacetSetInterface::FACET_PIVOT:
                        /* @var FacetPivot $facet */
                        $this->addFacetPivot($request, $facet);
                        $non_json = true;
                        break;
                    case FacetSetInterface::FACET_INTERVAL:
                        /* @var FacetInterval $facet */
                        $this->addFacetInterval($request, $facet);
                        $non_json = true;
                        break;
                    case FacetSetInterface::JSON_FACET_TERMS:
                    case FacetSetInterface::JSON_FACET_QUERY:
                    case FacetSetInterface::JSON_FACET_RANGE:
                    case FacetSetInterface::JSON_FACET_AGGREGATION:
                        /* @var JsonFacetInterface $facet */
                        $json_facets[$key] = $facet->serialize();
                        break;
                    default:
                        throw new UnexpectedValueException('Unknown facet type');
                }
            }

            if ($non_json) {
                // enable non-json faceting
                $request->addParam('facet', 'true');

                // global facet params
                $request->addParam('facet.sort', $component->getSort());
                $request->addParam('facet.prefix', $component->getPrefix());
                $request->addParam('facet.contains', $component->getContains());
                $request->addParam('facet.contains.ignoreCase', $component->getContainsIgnoreCase());
                $request->addParam('facet.missing', $component->getMissing());
                $request->addParam('facet.mincount', $component->getMinCount());
                $request->addParam('facet.limit', $component->getLimit());
            }

            if ($json_facets) {
                $request->addParam('json.facet', json_encode($json_facets));
            }
        }

        return $request;
    }

    /**
     * Add params for a field facet to request.
     *
     * @param Request    $request
     * @param FacetField $facet
     * @param bool       $use_local_params TRUE, if local params instead of global field params should be used. Must be set if the same field is used in different facets. Default is keeping the global field params (https://issues.apache.org/jira/browse/SOLR-6193)
     */
    public function addFacetField(Request $request, FacetField $facet, bool $use_local_params = false)
    {
        $field = $facet->getField();

        if ($use_local_params) {
            $local_params = ['key' => $facet->getKey(),
                'ex' => $facet->getExcludes(),
                'facet.limit' => $facet->getLimit(),
                'facet.sort' => $facet->getSort(),
                'facet.prefix' => $facet->getPrefix(),
                'facet.contains' => $facet->getContains(),
                'facet.contains.ignoreCase' => $facet->getContainsIgnoreCase(),
                'facet.offset' => $facet->getOffset(),
                'facet.mincount' => $facet->getMinCount(),
                'facet.missing' => $facet->getMissing(),
                'facet.method' => $facet->getMethod(),
            ];
        } else {
            $local_params = ['key' => $facet->getKey(), 'ex' => $facet->getExcludes()];
        }
        $request->addParam(
            'facet.field',
            $this->renderLocalParams(
                $field,
                $local_params
            )
        );
        if (!$use_local_params) {
            $request->addParam("f.$field.facet.limit", $facet->getLimit());
            $request->addParam("f.$field.facet.sort", $facet->getSort());
            $request->addParam("f.$field.facet.prefix", $facet->getPrefix());
            $request->addParam("f.$field.facet.contains", $facet->getContains());
            $request->addParam("f.$field.facet.contains.ignoreCase", $facet->getContainsIgnoreCase());
            $request->addParam("f.$field.facet.offset", $facet->getOffset());
            $request->addParam("f.$field.facet.mincount", $facet->getMinCount());
            $request->addParam("f.$field.facet.missing", $facet->getMissing());
            $request->addParam("f.$field.facet.method", $facet->getMethod());
        }
    }

    /**
     * Add params for a facet query to request.
     *
     * @param Request    $request
     * @param FacetQuery $facet
     */
    public function addFacetQuery($request, $facet)
    {
        $request->addParam(
            'facet.query',
            $this->renderLocalParams(
                $facet->getQuery(),
                ['key' => $facet->getKey(), 'ex' => $facet->getExcludes()]
            )
        );
    }

    /**
     * Add params for a multiquery facet to request.
     *
     * @param Request         $request
     * @param FacetMultiQuery $facet
     */
    public function addFacetMultiQuery($request, $facet)
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
    public function addFacetRange($request, $facet)
    {
        $field = $facet->getField();

        $request->addParam(
            'facet.range',
            $this->renderLocalParams(
                $field,
                ['key' => $facet->getKey(), 'ex' => $facet->getExcludes()]
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
    }

    /**
     * Add params for a range facet to request.
     *
     * @param Request    $request
     * @param FacetPivot $facet
     */
    public function addFacetPivot($request, $facet)
    {
        $stats = $facet->getStats();

        if (count($stats) > 0) {
            $key = ['stats' => implode('', $stats)];

            // when specifying stats, solr sets the field as key
            $facet->setKey(implode(',', $facet->getFields()));
        } else {
            $key = ['key' => $facet->getKey()];
        }

        $request->addParam(
            'facet.pivot',
            $this->renderLocalParams(
                implode(',', $facet->getFields()),
                array_merge($key, ['ex' => $facet->getExcludes()])
            )
        );
        $request->addParam('facet.pivot.mincount', $facet->getMinCount(), true);
    }

    /**
     * Add params for a interval facet to request.
     *
     * @param Request       $request
     * @param FacetInterval $facet
     */
    public function addFacetInterval($request, $facet)
    {
        $field = $facet->getField();

        $request->addParam(
            'facet.interval',
            $this->renderLocalParams(
                $field,
                ['key' => $facet->getKey(), 'ex' => $facet->getExcludes()]
            )
        );

        foreach ($facet->getSet() as $key => $setValue) {
            if (is_string($key)) {
                $setValue = '{!key="'.$key.'"}'.$setValue;
            }
            $request->addParam("f.$field.facet.interval.set", $setValue);
        }
    }
}

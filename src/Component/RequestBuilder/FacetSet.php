<?php

namespace Solarium\Component\RequestBuilder;

use Solarium\Component\Facet\Field as FacetField;
use Solarium\Component\Facet\Interval as FacetInterval;
use Solarium\Component\Facet\MultiQuery as FacetMultiQuery;
use Solarium\Component\Facet\Pivot as FacetPivot;
use Solarium\Component\Facet\Query as FacetQuery;
use Solarium\Component\Facet\Range as FacetRange;
use Solarium\Component\FacetSet as FacetsetComponent;
use Solarium\Core\Client\Request;
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
    public function buildComponent($component, $request)
    {
        $facets = $component->getFacets();
        if (0 !== count($facets)) {
            // enable faceting
            $request->addParam('facet', 'true');

            // global facet params
            $request->addParam('facet.sort', $component->getSort());
            $request->addParam('facet.prefix', $component->getPrefix());
            $request->addParam('facet.contains', $component->getContains());
            $request->addParam('facet.contains.ignoreCase', null === ($ignoreCase = $component->getContainsIgnoreCase()) ? null : ($ignoreCase ? 'true' : 'false'));
            $request->addParam('facet.missing', $component->getMissing());
            $request->addParam('facet.mincount', $component->getMinCount());
            $request->addParam('facet.limit', $component->getLimit());

            foreach ($facets as $facet) {
                switch ($facet->getType()) {
                    case FacetsetComponent::FACET_FIELD:
                        $this->addFacetField($request, $facet);
                        break;
                    case FacetsetComponent::FACET_QUERY:
                        $this->addFacetQuery($request, $facet);
                        break;
                    case FacetsetComponent::FACET_MULTIQUERY:
                        $this->addFacetMultiQuery($request, $facet);
                        break;
                    case FacetsetComponent::FACET_RANGE:
                        $this->addFacetRange($request, $facet);
                        break;
                    case FacetsetComponent::FACET_PIVOT:
                        $this->addFacetPivot($request, $facet);
                        break;
                    case FacetsetComponent::FACET_INTERVAL:
                        $this->addFacetInterval($request, $facet);
                        break;
                    default:
                        throw new UnexpectedValueException('Unknown facet type');
                }
            }
        }

        return $request;
    }

    /**
     * Add params for a field facet to request.
     *
     * @param Request    $request
     * @param FacetField $facet
     */
    public function addFacetField($request, $facet)
    {
        $field = $facet->getField();

        $request->addParam(
            'facet.field',
            $this->renderLocalParams(
                $field,
                ['key' => $facet->getKey(), 'ex' => $facet->getExcludes()]
            )
        );

        $request->addParam("f.$field.facet.limit", $facet->getLimit());
        $request->addParam("f.$field.facet.sort", $facet->getSort());
        $request->addParam("f.$field.facet.prefix", $facet->getPrefix());
        $request->addParam("f.$field.facet.contains", $facet->getContains());
        $request->addParam("f.$field.facet.contains.ignoreCase", null === ($ignoreCase = $facet->getContainsIgnoreCase()) ? null : ($ignoreCase ? 'true' : 'false'));
        $request->addParam("f.$field.facet.offset", $facet->getOffset());
        $request->addParam("f.$field.facet.mincount", $facet->getMinCount());
        $request->addParam("f.$field.facet.missing", $facet->getMissing());
        $request->addParam("f.$field.facet.method", $facet->getMethod());
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

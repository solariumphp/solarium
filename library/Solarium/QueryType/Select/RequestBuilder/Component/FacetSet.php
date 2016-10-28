<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 *
 * @copyright Copyright 2011 Bas de Nooijer <solarium@raspberry.nl>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 *
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */

namespace Solarium\QueryType\Select\RequestBuilder\Component;

use Solarium\Core\Client\Request;
use Solarium\QueryType\Select\RequestBuilder\RequestBuilder;
use Solarium\QueryType\Select\Query\Component\FacetSet as FacetsetComponent;
use Solarium\QueryType\Select\Query\Component\Facet\Field as FacetField;
use Solarium\QueryType\Select\Query\Component\Facet\MultiQuery as FacetMultiQuery;
use Solarium\QueryType\Select\Query\Component\Facet\Query as FacetQuery;
use Solarium\QueryType\Select\Query\Component\Facet\Range as FacetRange;
use Solarium\QueryType\Select\Query\Component\Facet\Pivot as FacetPivot;
use Solarium\QueryType\Select\Query\Component\Facet\Interval as FacetInterval;
use Solarium\Exception\UnexpectedValueException;

/**
 * Add select component FacetSet to the request.
 */
class FacetSet extends RequestBuilder implements ComponentRequestBuilderInterface
{
    /**
     * Add request settings for FacetSet.
     *
     * @throws UnexpectedValueException
     *
     * @param FacetsetComponent $component
     * @param Request           $request
     *
     * @return Request
     */
    public function buildComponent($component, $request)
    {
        $facets = $component->getFacets();
        if (count($facets) !== 0) {
            // enable faceting
            $request->addParam('facet', 'true');

            // global facet params
            $request->addParam('facet.sort', $component->getSort());
            $request->addParam('facet.prefix', $component->getPrefix());
            $request->addParam('facet.contains', $component->getContains());
            $request->addParam('facet.contains.ignoreCase', is_null($ignoreCase = $component->getContainsIgnoreCase()) ? null : ($ignoreCase ? 'true' : 'false'));
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
                array('key' => $facet->getKey(), 'ex' => $facet->getExcludes())
            )
        );

        $request->addParam("f.$field.facet.limit", $facet->getLimit());
        $request->addParam("f.$field.facet.sort", $facet->getSort());
        $request->addParam("f.$field.facet.prefix", $facet->getPrefix());
        $request->addParam("f.$field.facet.contains", $facet->getContains());
        $request->addParam("f.$field.facet.contains.ignoreCase", is_null($ignoreCase = $facet->getContainsIgnoreCase()) ? null : ($ignoreCase ? 'true' : 'false'));
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
                array('key' => $facet->getKey(), 'ex' => $facet->getExcludes())
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
                array('key' => $facet->getKey(), 'ex' => $facet->getExcludes())
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
            $key = array('stats' => implode('', $stats));

            // when specifying stats, solr sets the field as key
            $facet->setKey(implode(',', $facet->getFields()));
        } else {
            $key = array('key' => $facet->getKey());
        }

        $request->addParam(
            'facet.pivot',
            $this->renderLocalParams(
                implode(',', $facet->getFields()),
                array_merge($key, array('ex' => $facet->getExcludes()))
            )
        );
        $request->addParam('facet.pivot.mincount', $facet->getMinCount(), true);
    }

    /**
     * Add params for a interval facet to request
     *
     * @param  Request    $request
     * @param  FacetInterval $facet
     * @return void
     */
    public function addFacetInterval($request, $facet)
    {
        $field = $facet->getField();

        $request->addParam(
            'facet.interval',
            $this->renderLocalParams(
                $field,
                array('key' => $facet->getKey(), 'ex' => $facet->getExcludes())
            )
        );

        foreach ($facet->getSet() as $key => $setValue) {
            if(is_string($key)) {
                $setValue = '{!key="'.$key.'"}'.$setValue;
            }
            $request->addParam("f.$field.facet.interval.set", $setValue);
        }
    }
}

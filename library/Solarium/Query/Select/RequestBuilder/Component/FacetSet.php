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
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */
namespace Solarium\Query\Select\RequestBuilder\Component;
use Solarium\Core\Exception;
use Solarium\Core\Client\Request;
use Solarium\Query\Select\RequestBuilder\RequestBuilder;
use Solarium\Query\Select\Query\Component\FacetSet as FacetsetComponent;
use Solarium\Query\Select\Query\Component\Facet\Field as FacetField;
use Solarium\Query\Select\Query\Component\Facet\MultiQuery as FacetMultiQuery;
use Solarium\Query\Select\Query\Component\Facet\Query as FacetQuery;
use Solarium\Query\Select\Query\Component\Facet\Range as FacetRange;


/**
 * Add select component FacetSet to the request
 */
class FacetSet extends RequestBuilder
{

    /**
     * Add request settings for FacetSet
     *
     * @param FacetsetComponent $component
     * @param Request $request
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
            $request->addParam('facet.missing', $component->getMissing());
            $request->addParam('facet.mincount', $component->getMinCount());
            $request->addParam('facet.limit', $component->getLimit());

            foreach ($facets as $facet) {
                switch ($facet->getType())
                {
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
                    default:
                        throw new Exception('Unknown facet type');
                }
            }
        }

        return $request;
    }

    /**
     * Add params for a field facet to request
     *
     * @param Request $request
     * @param FacetField $facet
     * @return void
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
        $request->addParam("f.$field.facet.offset", $facet->getOffset());
        $request->addParam("f.$field.facet.mincount", $facet->getMinCount());
        $request->addParam("f.$field.facet.missing", $facet->getMissing());
        $request->addParam("f.$field.facet.method", $facet->getMethod());
    }

    /**
     * Add params for a facet query to request
     *
     * @param Request $request
     * @param FacetQuery $facet
     * @return void
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
     * Add params for a multiquery facet to request
     *
     * @param Request $request
     * @param FacetMultiQuery $facet
     * @return void
     */
    public function addFacetMultiQuery($request, $facet)
    {
        foreach ($facet->getQueries() as $facetQuery) {
            $this->addFacetQuery($request, $facetQuery);
        }
    }

    /**
     * Add params for a range facet to request
     *
     * @param Request $request
     * @param FacetRange $facet
     * @return void
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

        if ($facet->getInclude() !== null) {
            $other = explode(',', $facet->getOther());
            foreach ($other as $otherValue) {
                $request->addParam("f.$field.facet.range.other", trim($otherValue));
            }
        }

        if ($facet->getInclude() !== null) {
            $include = explode(',', $facet->getInclude());
            foreach ($include as $includeValue) {
                $request->addParam("f.$field.facet.range.include", trim($includeValue));
            }
        }
    }
}
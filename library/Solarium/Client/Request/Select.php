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
 * @package Solarium
 * @subpackage Client
 */

/**
 * Builds select request, for use in adapters.
 */
class Solarium_Client_Request_Select extends Solarium_Client_Request
{

    public function _init()
    {
        $this->_params = array(
            'q'     => $this->_query->getQuery(),
            'start' => $this->_query->getStart(),
            'rows'  => $this->_query->getRows(),
            'fl'    => implode(',', $this->_query->getFields()),
            'wt'    => 'json',
        );

        $sort = array();
        foreach ($this->_query->getSortFields() AS $field => $order) {
            $sort[] = $field . ' ' . $order;
        }
        if (count($sort) !== 0) {
            $this->addParam('sort', implode(',', $sort));
        }

        $filterQueries = $this->_query->getFilterQueries();
        if (count($filterQueries) !== 0) {
            foreach ($filterQueries AS $filterQuery) {
                $fq = $this->renderLocalParams(
                    $filterQuery->getQuery(),
                    array('tag' => $filterQuery->getTags())
                );
                $this->addParam('fq', $fq);
            }
        }

        $facets = $this->_query->getFacets();
        if (count($facets) !== 0) {

            // enable faceting
            $this->_params['facet'] = 'true';

            foreach ($facets AS $facet) {
                switch ($facet->getType())
                {
                    case Solarium_Query_Select_Facet::FIELD:
                        $this->addFacetField($facet);
                        break;
                    case Solarium_Query_Select_Facet::QUERY:
                        $this->addFacetQuery($facet);
                        break;
                    default:
                        throw new Solarium_Exception('Unknown facet type');
                }
            }
        }
    }

    public function addFacetField($facet)
    {
        $field = $facet->getField();

        $this->addParam(
            'facet.field',
            $this->renderLocalParams(
                $field,
                array('key' => $facet->getKey(), 'ex' => $facet->getExcludes())
            )
        );

        $this->addParam("f.$field.facet.limit", $facet->getLimit());
        $this->addParam("f.$field.facet.sort", $facet->getSort());
        $this->addParam("f.$field.facet.prefix", $facet->getPrefix());
        $this->addParam("f.$field.facet.offset", $facet->getOffset());
        $this->addParam("f.$field.facet.mincount", $facet->getMinCount());
        $this->addParam("f.$field.facet.missing", $facet->getMissing());
        $this->addParam("f.$field.facet.method", $facet->getMethod());
    }

    public function addFacetQuery($facet)
    {
        $this->addParam(
            'facet.query',
            $this->renderLocalParams(
                $facet->getQuery(),
                array('key' => $facet->getKey(), 'ex' => $facet->getExcludes())
            )
        );
    }

}
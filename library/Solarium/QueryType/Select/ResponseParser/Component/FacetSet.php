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
namespace Solarium\QueryType\Select\ResponseParser\Component;

use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\Query\Component\FacetSet as QueryFacetSet;
use Solarium\QueryType\Select\Query\Component\Facet\Field as QueryFacetField;
use Solarium\QueryType\Select\Query\Component\Facet\Query as QueryFacetQuery;
use Solarium\QueryType\Select\Query\Component\Facet\MultiQuery as QueryFacetMultiQuery;
use Solarium\QueryType\Select\Query\Component\Facet\Range as QueryFacetRange;
use Solarium\QueryType\Select\Query\Component\Facet\Pivot as QueryFacetPivot;
use Solarium\QueryType\Select\Result\FacetSet as ResultFacetSet;
use Solarium\QueryType\Select\Result\Facet\Field as ResultFacetField;
use Solarium\QueryType\Select\Result\Facet\Query as ResultFacetQuery;
use Solarium\QueryType\Select\Result\Facet\MultiQuery as ResultFacetMultiQuery;
use Solarium\QueryType\Select\Result\Facet\Range as ResultFacetRange;
use Solarium\QueryType\Select\Result\Facet\Pivot\Pivot as ResultFacetPivot;
use Solarium\Exception\RuntimeException;
use Solarium\Core\Query\ResponseParser as ResponseParserAbstract;

/**
 * Parse select component FacetSet result from the data
 */
class FacetSet extends ResponseParserAbstract implements ComponentParserInterface
{
    /**
     * Parse result data into result objects
     *
     * @throws RuntimeException
     * @param  Query            $query
     * @param  QueryFacetSet    $facetSet
     * @param  array            $data
     * @return ResultFacetSet
     */
    public function parse($query, $facetSet, $data)
    {
        if ($facetSet->getExtractFromResponse() === true) {
            if (empty($data['facet_counts']) === false) {
                foreach ($data['facet_counts'] as $key => $facets) {
                    switch ($key) {
                        case 'facet_fields':
                            $method = 'createFacetField';
                            break;
                        case 'facet_queries':
                            $method = 'createFacetQuery';
                            break;
                        case 'facet_ranges':
                            $method = 'createFacetRange';
                            break;
                        case 'facet_pivot':
                            $method = 'createFacetPivot';
                            break;
                        default:
                            throw new RuntimeException('Unknown facet class identifier');
                    }
                    foreach ($facets as $k => $facet) {
                        $facetObject = $facetSet->$method($k);
                        if ($key == 'facet_pivot') {
                            /** @var \Solarium\QueryType\Select\Query\Component\Facet\Pivot $facetObject */
                            $facetObject->setFields($k);
                        }
                    }
                }
            }
        }

        $facets = array();
        foreach ($facetSet->getFacets() as $key => $facet) {
            switch ($facet->getType()) {
                case QueryFacetSet::FACET_FIELD:
                    $result = $this->facetField($query, $facet, $data);
                    break;
                case QueryFacetSet::FACET_QUERY:
                    $result = $this->facetQuery($facet, $data);
                    break;
                case QueryFacetSet::FACET_MULTIQUERY:
                    $result = $this->facetMultiQuery($facet, $data);
                    break;
                case QueryFacetSet::FACET_RANGE:
                    $result = $this->facetRange($query, $facet, $data);
                    break;
                case QueryFacetSet::FACET_PIVOT:
                    $result = $this->facetPivot($query, $facet, $data);
                    break;
                default:
                    throw new RuntimeException('Unknown facet type');
            }

            if ($result !== null) {
                $facets[$key] = $result;
            }
        }

        return $this->createFacetSet($facets);
    }

    /**
     * Create a facetset result object
     *
     * @param  array          $facets
     * @return ResultFacetSet
     */
    protected function createFacetSet($facets)
    {
        return new ResultFacetSet($facets);
    }

    /**
     * Add a facet result for a field facet
     *
     * @param  Query            $query
     * @param  QueryFacetField  $facet
     * @param  array            $data
     * @return ResultFacetField|null
     */
    protected function facetField($query, $facet, $data)
    {
        $key = $facet->getKey();
        if (!isset($data['facet_counts']['facet_fields'][$key])) {
            return null;
        }

        if ($query->getResponseWriter() == $query::WT_JSON) {
            $data['facet_counts']['facet_fields'][$key] = $this->convertToKeyValueArray(
                $data['facet_counts']['facet_fields'][$key]
            );
        }

        return new ResultFacetField($data['facet_counts']['facet_fields'][$key]);
    }

    /**
     * Add a facet result for a facet query
     *
     * @param  QueryFacetQuery  $facet
     * @param  array            $data
     * @return ResultFacetQuery|null
     */
    protected function facetQuery($facet, $data)
    {
        $key = $facet->getKey();
        if (!isset($data['facet_counts']['facet_queries'][$key])) {
            return null;
        }

        return new ResultFacetQuery($data['facet_counts']['facet_queries'][$key]);
    }

    /**
     * Add a facet result for a multiquery facet
     *
     * @param  QueryFacetMultiQuery  $facet
     * @param  array                 $data
     * @return ResultFacetMultiQuery|null
     */
    protected function facetMultiQuery($facet, $data)
    {
        $values = array();
        foreach ($facet->getQueries() as $query) {
            $key = $query->getKey();
            if (isset($data['facet_counts']['facet_queries'][$key])) {
                $count = $data['facet_counts']['facet_queries'][$key];
                $values[$key] = $count;
            }
        }

        if (count($values) <= 0) {
            return null;
        }

        return new ResultFacetMultiQuery($values);
    }

    /**
     * Add a facet result for a range facet
     *
     * @param  Query            $query
     * @param  QueryFacetRange  $facet
     * @param  array            $data
     * @return ResultFacetRange|null
     */
    protected function facetRange($query, $facet, $data)
    {
        $key = $facet->getKey();
        if (!isset($data['facet_counts']['facet_ranges'][$key])) {
            return null;
        }

        $data = $data['facet_counts']['facet_ranges'][$key];
        $before = (isset($data['before'])) ? $data['before'] : null;
        $after = (isset($data['after'])) ? $data['after'] : null;
        $between = (isset($data['between'])) ? $data['between'] : null;
        $start = (isset($data['start'])) ? $data['start'] : null;
        $end = (isset($data['end'])) ? $data['end'] : null;
        $gap = (isset($data['gap'])) ? $data['gap'] : null;

        if ($query->getResponseWriter() == $query::WT_JSON) {
            $data['counts'] = $this->convertToKeyValueArray($data['counts']);
        }

        return new ResultFacetRange($data['counts'], $before, $after, $between, $start, $end, $gap);
    }

    /**
     * Add a facet result for a range facet
     *
     * @param  Query            $query
     * @param  QueryFacetPivot  $facet
     * @param  array            $data
     * @return ResultFacetPivot|null
     */
    protected function facetPivot($query, $facet, $data)
    {
        $key = implode(',', $facet->getFields());
        if (!isset($data['facet_counts']['facet_pivot'][$key])) {
            return null;
        }

        return new ResultFacetPivot($data['facet_counts']['facet_pivot'][$key]);
    }
}

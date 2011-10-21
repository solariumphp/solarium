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
 *
 * @package Solarium
 * @subpackage Client
 */

/**
 * Parse select component FacetSet result from the data
 *
 * @package Solarium
 * @subpackage Client
 */
class Solarium_Client_ResponseParser_Select_Component_FacetSet
{

    /**
     * Parse result data into result objects
     *
     * @param Solarium_Query_Select $query
     * @param Solarium_Query_Select_Component_FacetSet $facetSet
     * @param array $data
     * @return Solarium_Result_Select_FacetSet
     */
    public function parse($query, $facetSet, $data)
    {
        $facets = array();
        foreach ($facetSet->getFacets() AS $key => $facet) {
            switch ($facet->getType()) {
                case Solarium_Query_Select_Component_FacetSet::FACET_FIELD:
                    $result = $this->_facetField($facet, $data);
                    break;
                case Solarium_Query_Select_Component_FacetSet::FACET_QUERY:
                    $result = $this->_facetQuery($facet, $data);
                    break;
                case Solarium_Query_Select_Component_FacetSet::FACET_MULTIQUERY:
                    $result = $this->_facetMultiQuery($facet, $data);
                    break;
                case Solarium_Query_Select_Component_FacetSet::FACET_RANGE:
                    $result = $this->_facetRange($facet, $data);
                    break;
                default:
                    throw new Solarium_Exception('Unknown facet type');
            }

            if($result !== null) $facets[$key] = $result;
        }

        return $this->_createFacetSet($facets);
    }

    /**
     * Create a facetset result object
     *
     * @param array $facets
     * @return Solarium_Result_Select_FacetSet
     */
    protected function _createFacetSet($facets)
    {
        return new Solarium_Result_Select_FacetSet($facets);
    }

    /**
     * Add a facet result for a field facet
     *
     * @param Solarium_Query_Select_Component_Facet_Field $facet
     * @param array $data
     * @return void
     */
    protected function _facetField($facet, $data)
    {
        $key = $facet->getKey();
        if (isset($data['facet_counts']['facet_fields'][$key])) {

            $values = array_chunk(
                $data['facet_counts']['facet_fields'][$key],
                2
            );

            $facetValues = array();
            foreach ($values AS $value) {
                $facetValues[$value[0]] = $value[1];
            }

            return new Solarium_Result_Select_Facet_Field($facetValues);
        }
    }

    /**
     * Add a facet result for a facet query
     *
     * @param Solarium_Query_Select_Component_Facet_Query $facet
     * @param array $data
     * @return void
     */
    protected function _facetQuery($facet, $data)
    {
        $key = $facet->getKey();
        if (isset($data['facet_counts']['facet_queries'][$key])) {

            $value = $data['facet_counts']['facet_queries'][$key];
            return new Solarium_Result_Select_Facet_Query($value);
        }
    }

    /**
     * Add a facet result for a multiquery facet
     *
     * @param Solarium_Query_Select_Component_Facet_MultiQuery $facet
     * @param array $data
     * @return void
     */
    protected function _facetMultiQuery($facet, $data)
    {
        $values = array();
        foreach ($facet->getQueries() AS $query) {
            $key = $query->getKey();
            if (isset($data['facet_counts']['facet_queries'][$key])) {
                $count = $data['facet_counts']['facet_queries'][$key];
                $values[$key] = $count;
            }
        }

        if (count($values) > 0) {
            return new Solarium_Result_Select_Facet_MultiQuery($values);
        }
    }


    /**
     * Add a facet result for a range facet
     *
     * @param Solarium_Query_Select_Component_Facet_Range $facet
     * @param array $data
     * @return void
     */
    protected function _facetRange($facet, $data)
    {
        $key = $facet->getKey();
        if (isset($data['facet_counts']['facet_ranges'][$key])) {

            $data = $data['facet_counts']['facet_ranges'][$key];
            $before = (isset($data['before'])) ? $data['before'] : null;
            $after = (isset($data['after'])) ? $data['after'] : null;
            $between = (isset($data['between'])) ? $data['between'] : null;

            $offset = 0;
            $counts = array();
            while (isset($data['counts'][$offset]) && isset($data['counts'][$offset+1])) {
                $counts[$data['counts'][$offset]] = $data['counts'][$offset+1];
                $offset += 2;
            }

            return new Solarium_Result_Select_Facet_Range($counts, $before, $after, $between);
        }
    }

}
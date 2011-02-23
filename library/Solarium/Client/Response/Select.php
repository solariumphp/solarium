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
 * Handles Solr select response, for use in adapters.
 */
class Solarium_Client_Response_Select extends Solarium_Client_Response
{
    protected $_facets = array();

    public function getResult()
    {
        $documentClass = $this->_query->getOption('documentclass');
        $documents = array();
        if (isset($this->_data['response']['docs'])) {
            foreach ($this->_data['response']['docs'] AS $doc) {
                $fields = (array)$doc;
                $documents[] = new $documentClass($fields);
            }
        }

        foreach ($this->_query->getFacets() AS $facet) {
            switch ($facet->getType()) {
                case Solarium_Query_Select_Facet::FIELD:
                    $this->_addFacetField($facet);
                    break;
                case Solarium_Query_Select_Facet::QUERY:
                    $this->_addFacetQuery($facet);
                    break;
                default:
                    throw new Solarium_Exception('Unknown facet type');
            }
        }

        $status = $this->_data['responseHeader']['status'];
        $queryTime = $this->_data['responseHeader']['QTime'];
        $numFound = $this->_data['response']['numFound'];

        $resultClass = $this->_query->getOption('resultclass');
        return new $resultClass(
            $status, $queryTime, $numFound, $documents, $this->_facets
        );
    }

    protected function _addFacetField($facet)
    {
        $key = $facet->getKey();
        if (isset($this->_data['facet_counts']['facet_fields'][$key])) {

            $values = array_chunk(
                $this->_data['facet_counts']['facet_fields'][$key],
                2
            );

            $facetValues = array();
            foreach ($values AS $value) {
                $facetValues[$value[0]] = $value[1];
            }

            $this->_facets[$key] =
                new Solarium_Result_Select_Facet_Field($facetValues);
        }
    }

    protected function _addFacetQuery($facet)
    {
        $key = $facet->getKey();
        if (isset($this->_data['facet_counts']['facet_queries'][$key])) {

            $value = $this->_data['facet_counts']['facet_queries'][$key];
            $this->_facets[$key] =
                new Solarium_Result_Select_Facet_Query($value);
        }
    }
}
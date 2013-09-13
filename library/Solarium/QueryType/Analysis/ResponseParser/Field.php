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
namespace Solarium\QueryType\Analysis\ResponseParser;

use Solarium\Core\Query\Result\Result;
use Solarium\QueryType\Analysis\Result as AnalysisResult;
use Solarium\QueryType\Analysis\Result\ResultList;
use Solarium\QueryType\Analysis\Result\Item;
use Solarium\QueryType\Analysis\Result\Types;
use Solarium\Core\Query\ResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface as ResponseParserInterface;

/**
 * Parse document analysis response data
 */
class Field extends ResponseParserAbstract implements ResponseParserInterface
{
    /**
     * Parse response data
     *
     * @param  Result $result
     * @return array
     */
    public function parse($result)
    {
        $data = $result->getData();

        if (isset($data['analysis'])) {
            $items = $this->parseAnalysis($result, $data['analysis']);
        } else {
            $items = array();
        }

        return $this->addHeaderInfo($data, array('items' => $items));
    }

    /**
     * Parser
     *
     * @param  Result  $result
     * @param  array   $data
     * @return Types[]
     */
    protected function parseAnalysis($result, $data)
    {
        $types = array();
        foreach ($data as $documentKey => $documentData) {
            $fields = $this->parseTypes($result, $documentData);
            $types[] = new AnalysisResult\ResultList($documentKey, $fields);
        }

        return $types;
    }

    /**
     * Parse analysis types and items
     *
     * @param  Result  $result
     * @param  array   $typeData
     * @return Types[]
     */
    protected function parseTypes($result, $typeData)
    {
        $query = $result->getQuery();

        $results = array();
        foreach ($typeData as $fieldKey => $fieldData) {

            $types = array();
            foreach ($fieldData as $typeKey => $typeData) {

                if ($query->getResponseWriter() == $query::WT_JSON) {
                    // fix for extra level for key fields
                    if (count($typeData) == 1) {
                        $typeData = current($typeData);
                    }
                    $typeData = $this->convertToKeyValueArray($typeData);
                }

                $classes = array();
                foreach ($typeData as $class => $analysis) {
                    if (is_string($analysis)) {

                        $item = new Item(
                            array(
                                'text' => $analysis,
                                'start' => null,
                                'end' => null,
                                'position' => null,
                                'positionHistory' => null,
                                'type' => null,
                            )
                        );

                        $classes[] = new ResultList($class, array($item));

                    } else {

                        $items = array();
                        foreach ($analysis as $itemData) {
                            $items[] = new Item($itemData);
                        }

                        $classes[] = new ResultList($class, $items);
                    }
                }

                $types[] = new ResultList($typeKey, $classes);
            }

            $results[] = new Types($fieldKey, $types);
        }

        return $results;
    }
}

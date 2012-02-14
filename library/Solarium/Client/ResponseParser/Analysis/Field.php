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
 * Parse document analysis response data
 *
 * @package Solarium
 * @subpackage Client
 */
class Solarium_Client_ResponseParser_Analysis_Field extends Solarium_Client_ResponseParser
{

    /**
     * Parse response data
     *
     * @param Solarium_Result $result
     * @return array
     */
    public function parse($result)
    {
        $data = $result->getData();

        if (isset($data['analysis'])) {
            $items = $this->_parseAnalysis($data['analysis']);
        } else {
            $items = array();
        }

        return array(
            'status' => $data['responseHeader']['status'],
            'queryTime' => $data['responseHeader']['QTime'],
            'items' => $items
        );
    }

    /**
     * Parser
     *
     * @param array $data
     * @return array
     */
    protected function _parseAnalysis($data)
    {
        $types = array();
        foreach ($data as $documentKey => $documentData) {
            $fields = $this->_parseTypes($documentData);
            $types[] = new Solarium_Result_Analysis_List($documentKey, $fields);
        }

        return $types;
    }

    /**
     * Parse analysis types and items
     *
     * @param array $typeData
     * @return array
     */
    protected function _parseTypes($typeData)
    {
        $results = array();
        foreach ($typeData as $fieldKey => $fieldData) {

            $types = array();
            foreach ($fieldData as $typeKey => $typeData) {

                // fix for extra level for key fields
                if (count($typeData) == 1) {
                    $typeData = current($typeData);
                }

                $counter = 0;
                $classes = array();
                while (isset($typeData[$counter]) && isset($typeData[$counter+1])) {
                    $class = $typeData[$counter];
                    $analysis = $typeData[$counter+1];

                    if (is_string($analysis)) {

                        $item = new Solarium_Result_Analysis_Item(
                            array(
                                'text' => $analysis,
                                'start' => null,
                                'end' => null,
                                'position' => null,
                                'positionHistory' => null,
                                'type' => null,
                            )
                        );

                        $classes[] = new Solarium_Result_Analysis_List($class, array($item));

                    } else {

                        $items = array();
                        foreach ($analysis as $itemData) {
                            $items[] = new Solarium_Result_Analysis_Item($itemData);
                        }

                        $classes[] = new Solarium_Result_Analysis_List($class, $items);
                    }

                    $counter += 2;
                }

                $types[] = new Solarium_Result_Analysis_List($typeKey, $classes);
            }

            $results[] = new Solarium_Result_Analysis_Types($fieldKey, $types);
        }

        return $results;
    }

}
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
 * Parse select component Debug result from the data
 *
 * @package Solarium
 * @subpackage Client
 */
class Solarium_Client_ResponseParser_Select_Component_Debug
{

    /**
     * Parse result data into result objects
     *
     * @param Solarium_Query_Select $query
     * @param Solarium_Query_Select_Component_Debug $component
     * @param array $data
     * @return Solarium_Result_Select_Debug|null
     */
    public function parse($query, $component, $data)
    {
        $result = null;

        if (isset($data['debug'])) {
            $debug = $data['debug'];

            // get basic values from data
            $queryString = (isset($debug['querystring'])) ? $debug['querystring'] : '';
            $parsedQuery = (isset($debug['parsedquery'])) ? $debug['parsedquery'] : '';
            $queryParser = (isset($debug['QParser'])) ? $debug['QParser'] : '';
            $otherQuery = (isset($debug['otherQuery'])) ? $debug['otherQuery'] : '';

            // parse explain data
            if (isset($debug['explain']) && is_array($debug['explain'])) {
                $explain = $this->_parseDocumentSet($debug['explain']);
            } else {
                $explain = new Solarium_Result_Select_Debug_DocumentSet(array());
            }

            // parse explainOther data
            if (isset($debug['explainOther']) && is_array($debug['explainOther'])) {
                $explainOther = $this->_parseDocumentSet($debug['explainOther']);
            } else {
                $explainOther = new Solarium_Result_Select_Debug_DocumentSet(array());
            }

            // parse timing data
            $timing = null;
            if (isset($debug['timing']) && is_array($debug['timing'])) {
                $time = null;
                $timingPhases = array();
                foreach ($debug['timing'] as $key => $timingData) {
                    switch($key) {
                        case 'time':
                            $time = $timingData;
                            break;
                        default:
                            $timingPhases[$key] = $this->_parseTimingPhase($key, $timingData);
                    }
                }
                $timing = new Solarium_Result_Select_Debug_Timing($time, $timingPhases);
            }

            // create result object
            $result = new Solarium_Result_Select_Debug(
                $queryString,
                $parsedQuery,
                $queryParser,
                $otherQuery,
                $explain,
                $explainOther,
                $timing
            );
        }

        return $result;
    }

    /**
     * Parse data into a documentset
     *
     * Used for explain and explainOther
     *
     * @param array $data
     * @return Solarium_Result_Select_Debug_DocumentSet
     */
    protected function _parseDocumentSet($data)
    {
        $docs = array();
        foreach ($data as $key => $documentData) {

            $details = array();
            if (isset($documentData['details']) && is_array($documentData['details'])) {
                foreach ($documentData['details'] as $detailData) {
                    $details[] = new Solarium_Result_Select_Debug_Detail(
                        $detailData['match'],
                        $detailData['value'],
                        $detailData['description']
                    );
                }
            }

            $docs[$key] = new Solarium_Result_Select_Debug_Document(
                $key,
                $documentData['match'],
                $documentData['value'],
                $documentData['description'],
                $details
            );
        }

        return new Solarium_Result_Select_Debug_DocumentSet($docs);
    }

    /**
     * Parse raw timing phase data into a result class
     *
     * @param string $name
     * @param array $data
     * @return Solarium_Result_Select_Debug_TimingPhase
     */
    protected function _parseTimingPhase($name, $data)
    {
        $time = 0.0;
        $classes = array();
        foreach ($data as $key => $timingData) {
            switch($key) {
                case 'time':
                    $time = $timingData;
                    break;
                default:
                    $classes[$key] = $timingData['time'];
            }
        }

        return new Solarium_Result_Select_Debug_TimingPhase($name, $time, $classes);
    }

}
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
 * @namespace
 */
namespace Solarium\Client\ResponseParser\Select\Component;
use Solarium\Result\Select\Spellcheck as ResultSelectSpellcheck;

/**
 * Parse select component Highlighting result from the data
 *
 * @package Solarium
 * @subpackage Client
 */
class Spellcheck
{

    /**
     * Parse result data into result objects
     *
     * @param Solarium\Query\Select $query
     * @param Solarium\Query\Select\Component\Spellcheck $spellcheck
     * @param array $data
     * @return Solarium\Result\Select\Spellcheck|null
     */
    public function parse($query, $spellcheck, $data)
    {
        $results = array();
        if (
            isset($data['spellcheck']['suggestions']) &&
            is_array($data['spellcheck']['suggestions']) &&
            count($data['spellcheck']['suggestions']) > 0
        ) {

            $spellcheckResults = $data['spellcheck']['suggestions'];

            $suggestions = array();
            $correctlySpelled = null;
            $collation = null;

            $index = 0;
            while (isset($spellcheckResults[$index]) && isset($spellcheckResults[$index+1])) {
                $key = $spellcheckResults[$index];
                $value = $spellcheckResults[$index+1];

                switch ($key) {
                    case 'correctlySpelled':
                        $correctlySpelled = $value;
                        break;
                    case 'collation':
                        $collation = $this->_parseCollation($value);
                        break;
                    default:
                        $suggestions[] = $this->_parseSuggestion($key, $value);
                }

                $index +=2;
            }

            return new ResultSelectSpellcheck\Spellcheck($suggestions, $collation, $correctlySpelled);
        } else {
            return null;
        }
    }

    /**
     * Parse collation data into a result object
     *
     * @param array $values
     * @return Solarium\Result\Select\ResultSelectSpellcheck\Collation
     */
    protected function _parseCollation($values)
    {
        if (is_string($values)) {

            return new ResultSelectSpellcheck\Collation($values, null, array());

        } else {

            $query = null;
            $hits = null;
            $correctionResult = null;

            $index = 0;
            while (isset($values[$index]) && isset($values[$index+1])) {
                $key = $values[$index];
                $value = $values[$index+1];

                switch ($key) {
                    case 'collationQuery':
                        $query = $value;
                        break;
                    case 'hits':
                        $hits = $value;
                        break;
                    case 'misspellingsAndCorrections':
                        $correctionResult = $value;
                        break;
                }

                $index +=2;
            }

            $corrections = array();
            if ($correctionResult !== null) {
                $index = 0;
                while (isset($correctionResult[$index]) && isset($correctionResult[$index+1])) {
                    $input = $correctionResult[$index];
                    $correction = $correctionResult[$index+1];

                    $corrections[$input] = $correction;
                    $index += 2;
                }
            }

            return new ResultSelectSpellcheck\Collation($query, $hits, $corrections);
        }
    }

    /**
     * Parse suggestion data into a result object
     *
     * @param string $key
     * @param array $value
     * @return Solarium\Result\Select\ResultSelectSpellcheck\Suggestion
     */
    protected function _parseSuggestion($key, $value)
    {
        $numFound = (isset($value['numFound'])) ? $value['numFound'] : null;
        $startOffset = (isset($value['startOffset'])) ? $value['startOffset'] : null;
        $endOffset = (isset($value['endOffset'])) ? $value['endOffset'] : null;
        $originalFrequency = (isset($value['origFreq'])) ? $value['origFreq'] : null;

        if (is_string($value['suggestion'][0])) {
            $word = $value['suggestion'][0];
            $frequency = null;
        } else {
            $word = $value['suggestion'][0]['word'];
            $frequency = $value['suggestion'][0]['freq'];
        }

        return new ResultSelectSpellcheck\Suggestion(
            $numFound, $startOffset, $endOffset, $originalFrequency, $word, $frequency
        );
    }
}
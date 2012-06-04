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
namespace Solarium\Query\Select\ResponseParser\Component;
use Solarium\Query\Select\Query\Query;
use Solarium\Query\Select\Query\Component\Spellcheck as SpellcheckComponent;
use Solarium\Query\Select\Result\Spellcheck as SpellcheckResult;

/**
 * Parse select component Highlighting result from the data
 */
class Spellcheck
{

    /**
     * Parse result data into result objects
     *
     * @param Query $query
     * @param SpellcheckComponent $spellcheck
     * @param array $data
     * @return SpellcheckResult\Result|null
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
            $collations = array();

            $index = 0;
            while (isset($spellcheckResults[$index]) && isset($spellcheckResults[$index+1])) {
                $key = $spellcheckResults[$index];
                $value = $spellcheckResults[$index+1];

                switch ($key) {
                    case 'correctlySpelled':
                        $correctlySpelled = $value;
                        break;
                    case 'collation':
                        $collations[] = $this->parseCollation($value);
                        break;
                    default:
                        $suggestions[] = $this->parseSuggestion($key, $value);
                }

                $index +=2;
            }

            return new SpellcheckResult\Result($suggestions, $collations, $correctlySpelled);
        } else {
            return null;
        }
    }

    /**
     * Parse collation data into a result object
     *
     * @param array $values
     * @return SpellcheckResult\Collation
     */
    protected function parseCollation($values)
    {
        if (is_string($values)) {

            return new SpellcheckResult\Collation($values, null, array());

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

            return new SpellcheckResult\Collation($query, $hits, $corrections);
        }
    }

    /**
     * Parse suggestion data into a result object
     *
     * @param string $key
     * @param array $value
     * @return SpellcheckResult\Suggestion
     */
    protected function parseSuggestion($key, $value)
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

        return new SpellcheckResult\Suggestion(
            $numFound, $startOffset, $endOffset, $originalFrequency, $word, $frequency
        );
    }
}
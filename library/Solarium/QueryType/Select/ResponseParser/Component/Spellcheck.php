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
use Solarium\QueryType\Select\Query\Component\Spellcheck as SpellcheckComponent;
use Solarium\QueryType\Select\Result\Spellcheck as SpellcheckResult;
use Solarium\QueryType\Select\Result\Spellcheck\Result;
use Solarium\QueryType\Select\Result\Spellcheck\Collation;
use Solarium\QueryType\Select\Result\Spellcheck\Suggestion;

use Solarium\Core\Query\ResponseParser as ResponseParserAbstract;

/**
 * Parse select component Highlighting result from the data
 */
class Spellcheck extends ResponseParserAbstract implements ComponentParserInterface
{
    /**
     * Parse result data into result objects
     *
     * @param  Query               $query
     * @param  SpellcheckComponent $spellcheck
     * @param  array               $data
     * @return Result|null
     */
    public function parse($query, $spellcheck, $data)
    {
        if (isset($data['spellcheck']['suggestions']) &&
            is_array($data['spellcheck']['suggestions']) &&
            count($data['spellcheck']['suggestions']) > 0
        ) {

            $spellcheckResults = $data['spellcheck']['suggestions'];
            if ($query->getResponseWriter() == $query::WT_JSON) {
                $spellcheckResults = $this->convertToKeyValueArray($spellcheckResults);
            }

            $suggestions = array();
            $correctlySpelled = null;
            $collations = array();

            foreach ($spellcheckResults as $key => $value) {

                switch ($key) {
                    case 'correctlySpelled':
                        $correctlySpelled = $value;
                        break;
                    case 'collation':
                        $collations = $this->parseCollation($query, $value);
                        break;
                    default:
                        $suggestions[] = $this->parseSuggestion($key, $value);
                }
            }

            return new SpellcheckResult\Result($suggestions, $collations, $correctlySpelled);
        } else {
            return null;
        }
    }

    /**
     * Parse collation data into a result object
     *
     * @param  Query     $queryObject
     * @param  array     $values
     * @return Collation[]
     */
    protected function parseCollation($queryObject, $values)
    {
        $collations = array();
        if (is_string($values)) {

            $collations[] = new Collation($values, null, array());

        } elseif (is_array($values) && isset($values[0]) && is_string($values[0]) && $values[0] !== 'collationQuery') {

            foreach ($values as $value) {
                $collations[] = new Collation($value, null, array());
            }

        } else {

            if ($queryObject->getResponseWriter() == $queryObject::WT_JSON) {
                if (is_array(current($values))) {
                    foreach ($values as $key => $value) {
                        $values[$key] = $this->convertToKeyValueArray($value);
                    }
                } else {
                    $values = array($this->convertToKeyValueArray($values));
                }
            }

            foreach ($values as $collationValue) {
                $query = null;
                $hits = null;
                $correctionResult = null;

                foreach ($collationValue as $key => $value) {
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
                }

                $corrections = array();
                if ($correctionResult !== null) {

                    if ($queryObject->getResponseWriter() == $queryObject::WT_JSON) {
                        $correctionResult = $this->convertToKeyValueArray($correctionResult);
                    }

                    foreach ($correctionResult as $input => $correction) {
                        $corrections[$input] = $correction;
                    }
                }

                $collations[] = new Collation($query, $hits, $corrections);
            }
        }

        return $collations;
    }

    /**
     * Parse suggestion data into a result object
     *
     * @param  string     $key
     * @param  array      $value
     * @return Suggestion
     */
    protected function parseSuggestion($key, $value)
    {
        $numFound = (isset($value['numFound'])) ? $value['numFound'] : null;
        $startOffset = (isset($value['startOffset'])) ? $value['startOffset'] : null;
        $endOffset = (isset($value['endOffset'])) ? $value['endOffset'] : null;
        $originalFrequency = (isset($value['origFreq'])) ? $value['origFreq'] : null;

        $words = array();
        if (isset($value['suggestion']) && is_array($value['suggestion'])) {
            foreach ($value['suggestion'] as $suggestion) {
                if (is_string($suggestion)) {
                    $suggestion = array(
                        'word' => $suggestion,
                        'freq' => null,
                    );
                }
                $words[] = $suggestion;
            }
        }

        return new Suggestion($numFound, $startOffset, $endOffset, $originalFrequency, $words);
    }
}

<?php

namespace Solarium\Component\ResponseParser;

use Solarium\Component\Result\Spellcheck\Collation;
use Solarium\Component\Result\Spellcheck\Result;
use Solarium\Component\Result\Spellcheck\Suggestion;
use Solarium\Component\Spellcheck as SpellcheckComponent;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;

/**
 * Parse select component Highlighting result from the data.
 */
class Spellcheck extends ResponseParserAbstract implements ComponentParserInterface
{
    /**
     * Parse result data into result objects.
     *
     * @param AbstractQuery       $query
     * @param SpellcheckComponent $spellcheck
     * @param array               $data
     *
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

            $suggestions = [];
            $collations = [];
            $correctlySpelled = false;

            foreach ($spellcheckResults as $key => $value) {
                switch ($key) {
                    case 'correctlySpelled':
                        $correctlySpelled = $value;
                        break;
                    case 'collation':
                        $collations = $this->parseCollation($query, $value);
                        break;
                    default:
                        if (array_key_exists(0, $value)) {
                            foreach ($value as $currentValue) {
                                $suggestions[] = $this->parseSuggestion($key, $currentValue);
                            }
                        } else {
                            $suggestions[] = $this->parseSuggestion($key, $value);
                        }
                }
            }

            /*
             * https://issues.apache.org/jira/browse/SOLR-3029
             * Solr5 has moved collations and correctlySpelled
             * directly under spellcheck.
             */
            if (isset($data['spellcheck']['collations']) &&
                is_array($data['spellcheck']['collations'])
            ) {
                foreach ($this->convertToKeyValueArray($data['spellcheck']['collations']) as $collationResult) {
                    $collations = array_merge($collations, $this->parseCollation($query, $collationResult));
                }
            }

            if (isset($data['spellcheck']['correctlySpelled'])
            ) {
                $correctlySpelled = $data['spellcheck']['correctlySpelled'];
            }

            return new Result($suggestions, $collations, $correctlySpelled);
        }

        return null;
    }

    /**
     * Parse collation data into a result object.
     *
     * @param AbstractQuery $queryObject
     * @param array         $values
     *
     * @return Collation[]
     */
    protected function parseCollation($queryObject, $values)
    {
        $collations = [];
        if (is_string($values)) {
            $collations[] = new Collation($values, null, []);
        } elseif (is_array($values) && isset($values[0]) && is_string($values[0]) && 'collationQuery' !== $values[0]) {
            foreach ($values as $value) {
                $collations[] = new Collation($value, null, []);
            }
        } else {
            if ($queryObject->getResponseWriter() == $queryObject::WT_JSON) {
                if (is_array(current($values))) {
                    foreach ($values as $key => $value) {
                        if (array_key_exists('collationQuery', $value)) {
                            $values[$key] = $value;
                        } else {
                            $values[$key] = $this->convertToKeyValueArray($value);
                        }
                    }
                } else {
                    if (array_key_exists('collationQuery', $values)) {
                        $values = [$values];
                    } else {
                        $values = [$this->convertToKeyValueArray($values)];
                    }
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

                $corrections = [];
                if (null !== $correctionResult) {
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
     * Parse suggestion data into a result object.
     *
     * @param string $key
     * @param array  $value
     *
     * @return Suggestion
     */
    protected function parseSuggestion($key, $value)
    {
        $numFound = (isset($value['numFound'])) ? $value['numFound'] : null;
        $startOffset = (isset($value['startOffset'])) ? $value['startOffset'] : null;
        $endOffset = (isset($value['endOffset'])) ? $value['endOffset'] : null;
        $originalFrequency = (isset($value['origFreq'])) ? $value['origFreq'] : null;

        $words = [];
        if (isset($value['suggestion']) && is_array($value['suggestion'])) {
            foreach ($value['suggestion'] as $suggestion) {
                if (is_string($suggestion)) {
                    $suggestion = [
                        'word' => $suggestion,
                        'freq' => null,
                    ];
                }
                $words[] = $suggestion;
            }
        }

        return new Suggestion($numFound, $startOffset, $endOffset, $originalFrequency, $words);
    }
}

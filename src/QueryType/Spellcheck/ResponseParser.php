<?php

namespace Solarium\QueryType\Spellcheck;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface as ResponseParserInterface;
use Solarium\QueryType\Spellcheck\Result\Result;

/**
 * Parse Spellcheck response data.
 */
class ResponseParser extends ResponseParserAbstract implements ResponseParserInterface
{
    /**
     * Get result data for the response.
     *
     * @param Result $result
     *
     * @return array
     */
    public function parse($result)
    {
        $data = $result->getData();
        $query = $result->getQuery();

        $suggestions = [];
        $allSuggestions = [];
        $collation = null;

        if (isset($data['spellcheck']['suggestions']) && is_array($data['spellcheck']['suggestions'])) {
            $suggestResults = $data['spellcheck']['suggestions'];
            $termClass = $query->getOption('termclass');

            if ($query->getResponseWriter() == $query::WT_JSON) {
                $suggestResults = $this->convertToKeyValueArray($suggestResults);
            }

            foreach ($suggestResults as $term => $termData) {
                if ('collation' == $term) {
                    $collation = $termData;
                } else {
                    if (!array_key_exists(0, $termData)) {
                        $termData = [$termData];
                    }

                    foreach ($termData as $currentTermData) {
                        $allSuggestions[] = $this->createTerm($termClass, $currentTermData);

                        if (!array_key_exists($term, $suggestions)) {
                            $suggestions[$term] = $this->createTerm($termClass, $currentTermData);
                        }
                    }
                }
            }
        }

        return $this->addHeaderInfo(
            $data,
            [
                'results' => $suggestions,
                'all' => $allSuggestions,
                'collation' => $collation,
            ]
        );
    }

    private function createTerm($termClass, array $termData)
    {
        return new $termClass(
            $termData['numFound'],
            $termData['startOffset'],
            $termData['endOffset'],
            $termData['suggestion']
        );
    }
}

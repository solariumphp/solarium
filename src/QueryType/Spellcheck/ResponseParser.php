<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Spellcheck;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\QueryType\Spellcheck\Result\Result;

/**
 * Parse Spellcheck response data.
 */
class ResponseParser extends ResponseParserAbstract implements ResponseParserInterface
{
    /**
     * Get result data for the response.
     *
     * @param \Solarium\Core\Query\Result\ResultInterface $result
     *
     * @return array
     */
    public function parse(ResultInterface $result): array
    {
        $data = $result->getData();
        $query = $result->getQuery();

        $suggestions = [];
        $allSuggestions = [];
        $collation = null;

        if (isset($data['spellcheck']['suggestions']) && \is_array($data['spellcheck']['suggestions'])) {
            $suggestResults = $data['spellcheck']['suggestions'];
            $termClass = $query->getOption('termclass');

            if ($query->getResponseWriter() === $query::WT_JSON) {
                $suggestResults = $this->convertToKeyValueArray($suggestResults);
            }

            foreach ($suggestResults as $term => $termData) {
                if ('collation' === $term) {
                    $collation = $termData;
                } else {
                    if (!\array_key_exists(0, $termData)) {
                        $termData = [$termData];
                    }

                    foreach ($termData as $currentTermData) {
                        $allSuggestions[] = $this->createTerm($termClass, $currentTermData);

                        if (!\array_key_exists($term, $suggestions)) {
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

    /**
     * @param string $termClass
     * @param array  $termData
     *
     * @return mixed
     */
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

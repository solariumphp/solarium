<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\QueryType\Suggester;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface;
use Solarium\Core\Query\Result\ResultInterface;
use Solarium\QueryType\Suggester\Result\Result;

/**
 * Parse Suggester response data.
 */
class ResponseParser extends ResponseParserAbstract implements ResponseParserInterface
{
    /**
     * Get result data for the response.
     *
     * @param Result|ResultInterface $result
     *
     * @return array
     */
    public function parse(ResultInterface $result): array
    {
        $data = $result->getData();
        $query = $result->getQuery();

        $dictionaries = [];
        $allSuggestions = [];

        if (isset($data['suggest']) && \is_array($data['suggest'])) {
            $dictionaryClass = $query->getOption('dictionaryclass');
            $termClass = $query->getOption('termclass');

            foreach ($data['suggest'] as $dictionary => $dictionaryResults) {
                $terms = [];
                foreach ($dictionaryResults as $term => $termData) {
                    $allSuggestions[] = $this->createTerm($termClass, $termData);
                    $terms[$term] = $this->createTerm($termClass, $termData);
                }
                $dictionaries[$dictionary] = $this->createDictionary($dictionaryClass, $terms);
            }
        }

        return $this->addHeaderInfo(
            $data,
            [
                'results' => $dictionaries,
                'all' => $allSuggestions,
            ]
        );
    }

    /**
     * @param string $dictionaryClass
     * @param array  $terms
     *
     * @return mixed
     */
    private function createDictionary($dictionaryClass, array $terms)
    {
        return new $dictionaryClass(
            $terms
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
            $termData['suggestions']
        );
    }
}

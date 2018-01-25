<?php

namespace Solarium\Component\ResponseParser;

use Solarium\Component\Result\Suggester\Result;
use Solarium\Component\Suggester as SuggesterComponent;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Core\Query\AbstractResponseParser;
use Solarium\QueryType\Suggester\Result\Dictionary;
use Solarium\QueryType\Suggester\Result\Term;

/**
 * Parse select component Highlighting result from the data.
 */
class Suggester extends AbstractResponseParser implements ComponentParserInterface
{
    /**
     * Parse result data into result objects.
     *
     * @param AbstractQuery      $query
     * @param SuggesterComponent $suggester
     * @param array              $data
     *
     * @return Result|null
     */
    public function parse($query, $suggester, $data)
    {
        $dictionaries = [];
        $allSuggestions = [];

        if (isset($data['suggest']) && is_array($data['suggest'])) {
            foreach ($data['suggest'] as $dictionary => $dictionaryResults) {
                $terms = [];
                foreach ($dictionaryResults as $term => $termData) {
                    $allSuggestions[] = $this->createTerm($termData);
                    $terms[$term] = $this->createTerm($termData);
                }
                $dictionaries[$dictionary] = $this->createDictionary($terms);
            }

            return new Result($dictionaries, $allSuggestions);
        }

        return null;
    }

    private function createDictionary(array $terms)
    {
        return new Dictionary(
            $terms
        );
    }

    private function createTerm(array $termData)
    {
        return new Term(
            $termData['numFound'],
            $termData['suggestions']
        );
    }
}

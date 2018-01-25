<?php

namespace Solarium\Component\ResponseParser;

use Solarium\Component\MoreLikeThis as MoreLikeThisComponent;
use Solarium\Component\Result\MoreLikeThis\MoreLikeThis as MoreLikeThisResult;
use Solarium\Component\Result\MoreLikeThis\Result;
use Solarium\QueryType\Analysis\Query\AbstractQuery;

/**
 * Parse select component MoreLikeThis result from the data.
 */
class MoreLikeThis implements ComponentParserInterface
{
    /**
     * Parse result data into result objects.
     *
     * @param AbstractQuery         $query
     * @param MoreLikeThisComponent $moreLikeThis
     * @param array                 $data
     *
     * @return MoreLikeThis
     */
    public function parse($query, $moreLikeThis, $data)
    {
        $results = [];
        if (isset($data['moreLikeThis'])) {
            $documentClass = $query->getOption('documentclass');

            $searchResults = $data['moreLikeThis'];
            foreach ($searchResults as $key => $result) {
                // create document instances
                $docs = [];
                foreach ($result['docs'] as $fields) {
                    $docs[] = new $documentClass($fields);
                }

                $results[$key] = new Result(
                    $result['numFound'],
                    isset($result['maxScore']) ? $result['maxScore'] : null,
                    $docs
                );
            }
        }

        return new MoreLikeThisResult($results);
    }
}

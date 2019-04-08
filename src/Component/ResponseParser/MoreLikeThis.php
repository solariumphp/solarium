<?php

namespace Solarium\Component\ResponseParser;

use Solarium\Component\AbstractComponent;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\MoreLikeThis as MoreLikeThisComponent;
use Solarium\Component\Result\MoreLikeThis\MoreLikeThis as MoreLikeThisResult;
use Solarium\Component\Result\MoreLikeThis\Result;
use Solarium\Exception\InvalidArgumentException;
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
     * @return MoreLikeThisResult
     *
     * @throws InvalidArgumentException
     */
    public function parse(?ComponentAwareQueryInterface $query, ?AbstractComponent $moreLikeThis, array $data): MoreLikeThisResult
    {
        $results = [];
        if (isset($data['moreLikeThis'])) {
            if (!$query) {
                throw new InvalidArgumentException('A valid query object needs to be provided.');
            }
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

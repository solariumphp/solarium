<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\ResponseParser;

use Solarium\Component\AbstractComponent;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\MoreLikeThis as MoreLikeThisComponent;
use Solarium\Component\Result\MoreLikeThis\MoreLikeThis as MoreLikeThisResult;
use Solarium\Component\Result\MoreLikeThis\Result;
use Solarium\Core\Query\AbstractResponseParser;
use Solarium\Exception\InvalidArgumentException;
use Solarium\QueryType\Analysis\Query\AbstractQuery;

/**
 * Parse select component MoreLikeThis result from the data.
 */
class MoreLikeThis extends AbstractResponseParser implements ComponentParserInterface
{
    /**
     * Parse result data into result objects.
     *
     * @param AbstractQuery         $query
     * @param MoreLikeThisComponent $moreLikeThis
     * @param array                 $data
     *
     * @throws InvalidArgumentException
     *
     * @return MoreLikeThisResult
     */
    public function parse(?ComponentAwareQueryInterface $query, ?AbstractComponent $moreLikeThis, array $data): MoreLikeThisResult
    {
        $results = [];
        $interestingTerms = [];

        if (isset($data['moreLikeThis'])) {
            if (!$query) {
                throw new InvalidArgumentException('A valid query object needs to be provided.');
            }
            $documentClass = $query->getOption('documentclass');
            $searchResults = $data['moreLikeThis'];

            // There seems to be a bug in Solr that json.nl=flat is ignored in a distributed search on Solr
            // Cloud. In that case the "map" format is returned which doesn't need to be converted. But we don't
            // use it in general because it has limitations for some components.
            if (isset($searchResults[0]) && $query && $query::WT_JSON === $query->getResponseWriter()) {
                // We have a "flat" json result.
                $searchResults = $this->convertToKeyValueArray($searchResults);
            }

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

            if ('none' === $moreLikeThis->getInterestingTerms()) {
                $interestingTerms = null;
            } elseif (isset($data['interestingTerms'])) {
                // We don't need to convertToKeyValueArray. Solr's MoreLikeThisComponent uses a SimpleOrderedMap
                // for representing interesting terms. A SimpleOrdereMap is always returned using the "map" format.
                $interestingTerms = $data['interestingTerms'];
            }
        }

        return new MoreLikeThisResult($results, $interestingTerms);
    }
}

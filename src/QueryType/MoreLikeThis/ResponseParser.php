<?php

namespace Solarium\QueryType\MoreLikeThis;

use Solarium\Core\Query\Result\ResultInterface;
use Solarium\QueryType\Select\ResponseParser as SelectResponseParser;

/**
 * Parse MoreLikeThis response data.
 */
class ResponseParser extends SelectResponseParser
{
    /**
     * Get result data for the response.
     *
     * @param Result $result
     *
     * @return array
     */
    public function parse(ResultInterface $result): array
    {
        $data = $result->getData();
        /** @var Query $query */
        $query = $result->getQuery();

        $parseResult = parent::parse($result);
        if (isset($data['interestingTerms']) && 'none' !== $query->getInterestingTerms()) {
            $terms = $data['interestingTerms'];
            if ('details' === $query->getInterestingTerms()) {
                if ($query->getResponseWriter() == $query::WT_JSON) {
                    $terms = $this->convertToKeyValueArray($terms);
                }
            }
            $parseResult['interestingTerms'] = $terms;
        }

        if (isset($data['match']['docs'][0]) && true === $query->getMatchInclude()) {
            $matchData = $data['match']['docs'][0];

            $documentClass = $query->getOption('documentclass');
            $fields = (array) $matchData;
            $parseResult['match'] = new $documentClass($fields);
        }

        return $parseResult;
    }
}

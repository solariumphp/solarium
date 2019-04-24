<?php

namespace Solarium\QueryType\Analysis\ResponseParser;

use Solarium\Core\Query\Result\ResultInterface;
use Solarium\QueryType\Analysis\Result\ResultList;

/**
 * Parse document analysis response data.
 */
class Document extends Field
{
    /**
     * Parse implementation.
     *
     * @param ResultInterface $result
     * @param array           $data
     *
     * @return ResultList[]
     */
    protected function parseAnalysis(ResultInterface $result, array $data): array
    {
        $documents = [];
        foreach ($data as $documentKey => $documentData) {
            $fields = $this->parseTypes($result, $documentData);
            $documents[] = new ResultList($documentKey, $fields);
        }

        return $documents;
    }
}

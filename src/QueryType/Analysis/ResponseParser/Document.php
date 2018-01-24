<?php

namespace Solarium\QueryType\Analysis\ResponseParser;

use Solarium\QueryType\Analysis\Result\ResultList;

/**
 * Parse document analysis response data.
 */
class Document extends Field
{
    /**
     * Parse implementation.
     *
     * @param array $result
     * @param array $data
     *
     * @return ResultList[]
     */
    protected function parseAnalysis($result, $data)
    {
        $documents = [];
        foreach ($data as $documentKey => $documentData) {
            $fields = $this->parseTypes($result, $documentData);
            $documents[] = new ResultList($documentKey, $fields);
        }

        return $documents;
    }
}

<?php

namespace Solarium\Component\ResponseParser;

use Solarium\Component\Highlighting\Highlighting as HighlightingComponent;
use Solarium\Component\Result\Highlighting\Highlighting as HighlightingResult;
use Solarium\Component\Result\Highlighting\Result;
use Solarium\QueryType\Select\Query\Query;

/**
 * Parse select component Highlighting result from the data.
 */
class Highlighting implements ComponentParserInterface
{
    /**
     * Parse result data into result objects.
     *
     * @param Query                 $query
     * @param HighlightingComponent $highlighting
     * @param array                 $data
     *
     * @return HighlightingResult
     */
    public function parse($query, $highlighting, $data)
    {
        $results = [];
        if (isset($data['highlighting'])) {
            $highlightResults = $data['highlighting'];
            foreach ($highlightResults as $key => $result) {
                $results[$key] = new Result(
                    $result
                );
            }
        }

        return new HighlightingResult($results);
    }
}

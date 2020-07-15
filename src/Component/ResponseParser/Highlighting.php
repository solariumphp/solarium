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
     * @param Query|null            $query
     * @param HighlightingComponent $highlighting
     * @param array                 $data
     *
     * @return HighlightingResult
     */
    public function parse(?ComponentAwareQueryInterface $query, ?AbstractComponent $highlighting, array $data): HighlightingResult
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

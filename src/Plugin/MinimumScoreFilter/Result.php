<?php

namespace Solarium\Plugin\MinimumScoreFilter;

use Solarium\QueryType\Select\Result\Result as SelectResult;

/**
 * Minimumscore filter query result.
 *
 * Extends select query result, adds filtering / marking
 */
class Result extends SelectResult
{
    /**
     * Map parser data into properties.
     *
     * @param array $mapData
     */
    protected function mapData($mapData)
    {
        foreach ($mapData as $key => $data) {
            if ('documents' == $key) {
                $filter = new Filter();
                $mode = $this->getQuery()->getFilterMode();
                $ratio = $this->getQuery()->getFilterRatio();
                $data = $filter->filterDocuments($data, $mapData['maxscore'], $ratio, $mode);
            }
            $this->$key = $data;
        }
    }
}

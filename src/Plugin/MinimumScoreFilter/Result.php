<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

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
    protected function mapData(array $mapData)
    {
        foreach ($mapData as $key => $data) {
            if ('documents' === $key && $data) {
                $filter = new Filter();
                /** @var Query $query */
                $query = $this->getQuery();
                $mode = $query->getFilterMode();
                $ratio = $query->getFilterRatio();
                $data = $filter->filterDocuments($data, $mapData['maxscore'], $ratio, $mode);
            }
            $this->{$key} = $data;
        }
    }
}

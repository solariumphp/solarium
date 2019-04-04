<?php

namespace Solarium\Component\ResponseParser;

use Solarium\Component\AbstractComponent;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\Result\Stats\FacetValue as ResultStatsFacetValue;
use Solarium\Component\Result\Stats\Result as ResultStatsResult;
use Solarium\Component\Result\Stats\Stats as ResultStats;
use Solarium\Component\Stats\Stats as StatsComponent;
use Solarium\QueryType\Select\Query\Query;

/**
 * Parse select component Stats result from the data.
 */
class Stats implements ComponentParserInterface
{
    /**
     * Parse result data into result objects.
     *
     * @param Query          $query
     * @param StatsComponent $statsComponent
     * @param array          $data
     *
     * @return ResultStats;
     */
    public function parse(?ComponentAwareQueryInterface $query, ?AbstractComponent $statsComponent, array $data): ResultStats
    {
        $results = [];
        if (isset($data['stats']['stats_fields'])) {
            $statResults = $data['stats']['stats_fields'];
            foreach ($statResults as $field => $stats) {
                if (isset($stats['facets'])) {
                    foreach ($stats['facets'] as $facetField => $values) {
                        foreach ($values as $value => $valueStats) {
                            $stats['facets'][$facetField][$value] = new ResultStatsFacetValue($value, $valueStats);
                        }
                    }
                }

                $results[$field] = new ResultStatsResult($field, $stats);
            }
        }

        return new ResultStats($results);
    }
}

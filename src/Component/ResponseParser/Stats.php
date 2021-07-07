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
use Solarium\Component\Result\Stats\FacetValue as ResultStatsFacetValue;
use Solarium\Component\Result\Stats\Result as ResultStatsResult;
use Solarium\Component\Result\Stats\Stats as ResultStats;
use Solarium\Component\Stats\Stats as StatsComponent;
use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Exception\InvalidArgumentException;
use Solarium\QueryType\Select\Query\Query;

/**
 * Parse select component Stats result from the data.
 */
class Stats extends ResponseParserAbstract implements ComponentParserInterface
{
    /**
     * Parse result data into result objects.
     *
     * @param Query          $query
     * @param StatsComponent $statsComponent
     * @param array          $data
     *
     * @throws InvalidArgumentException
     *
     * @return ResultStats;
     */
    public function parse(?ComponentAwareQueryInterface $query, ?AbstractComponent $statsComponent, array $data): ResultStats
    {
        if (!$query) {
            throw new InvalidArgumentException('A valid query object needs to be provided.');
        }

        $results = [];
        if (isset($data['stats']['stats_fields'])) {
            $statResults = $data['stats']['stats_fields'];
            foreach ($statResults as $field => $stats) {
                if (isset($stats['facets'])) {
                    foreach ($stats['facets'] as $facetField => $values) {
                        foreach ($values as $value => $valueStats) {
                            if ($query->getResponseWriter() === $query::WT_JSON) {
                                $valueStats = $this->normalizeParsedJsonValues($valueStats);
                            }

                            $stats['facets'][$facetField][$value] = new ResultStatsFacetValue($value, $valueStats);
                        }
                    }
                }

                if ($query->getResponseWriter() === $query::WT_JSON) {
                    $stats = $this->normalizeParsedJsonValues($stats);
                }

                $results[$field] = new ResultStatsResult($field, $stats);
            }
        }

        return new ResultStats($results);
    }

    /**
     * Normalize values that were parsed from JSON.
     *
     * - Convert string 'NaN' to float NAN for mean.
     * - Convert percentiles to associative array.
     *
     * @param array $stats
     *
     * @return array
     */
    protected function normalizeParsedJsonValues(array $stats): array
    {
        if (isset($stats['mean']) && 'NaN' === $stats['mean']) {
            $stats['mean'] = NAN;
        }

        if (isset($stats['percentiles'])) {
            $stats['percentiles'] = $this->convertToKeyValueArray($stats['percentiles']);
        }

        return $stats;
    }
}

<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Component\ResponseParser;

trait NormalizeParsedJsonStatsTrait
{
    /**
     * Normalize stats values that were parsed from JSON.
     *
     * - Convert string 'NaN' to float NAN for mean.
     * - Convert percentiles to associative array.
     *
     * @param array $stats
     *
     * @return array
     */
    protected function normalizeParsedJsonStats(array $stats): array
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

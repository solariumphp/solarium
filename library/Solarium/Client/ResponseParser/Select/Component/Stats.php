<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 *
 * @copyright Copyright 2011 Bas de Nooijer <solarium@raspberry.nl>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 * @link http://www.solarium-project.org/
 *
 * @package Solarium
 * @subpackage Client
 */

/**
 * @namespace
 */
namespace Solarium\Client\ResponseParser\Select\Component;

/**
 * Parse select component Stats result from the data
 *
 * @package Solarium
 * @subpackage Client
 */
class Stats
{

    /**
     * Parse result data into result objects
     *
     * @param Solarium\Query\Select $query
     * @param Solarium\Query\Select\Component\Stats $stats
     * @param array $data
     * @return Solarium\Result\Select\Stats
     */
    public function parse($query, $stats, $data)
    {
        $results = array();
        if (isset($data['stats']['stats_fields'])) {

            $statResults = $data['stats']['stats_fields'];
            foreach ($statResults AS $field => $stats) {
                if (isset($stats['facets'])) {
                    foreach ($stats['facets'] as $facetField => $values) {
                        foreach ($values as $value => $valueStats) {
                            $stats['facets'][$facetField][$value] = new \Solarium\Result\Select\Stats\FacetValue(
                                $value, $valueStats
                            );
                        }
                    }
                }

                $results[$field] = new \Solarium\Result\Select\Stats\Result($field, $stats);
            }
        }

        return new \Solarium\Result\Select\Stats\Stats($results);
    }
}
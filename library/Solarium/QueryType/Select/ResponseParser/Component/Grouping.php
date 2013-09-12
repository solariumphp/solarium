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
 */

/**
 * @namespace
 */
namespace Solarium\QueryType\Select\ResponseParser\Component;

use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\Query\Component\Grouping as GroupingComponent;
use Solarium\QueryType\Select\Result\Grouping\Result;
use Solarium\QueryType\Select\Result\Grouping\ValueGroup;
use Solarium\QueryType\Select\Result\Grouping\QueryGroup;
use Solarium\QueryType\Select\Result\Grouping\FieldGroup;

/**
 * Parse select component Grouping result from the data
 */
class Grouping implements ComponentParserInterface
{
    /**
     * Parse result data into result objects
     *
     * @param  Query             $query
     * @param  GroupingComponent $grouping
     * @param  array             $data
     * @return Result
     */
    public function parse($query, $grouping, $data)
    {
        $groups = array();

        if (isset($data['grouped'])) {

            // parse field groups
            foreach ($grouping->getFields() as $field) {
                if (isset($data['grouped'][$field])) {
                    $result = $data['grouped'][$field];

                    $matches = (isset($result['matches'])) ? $result['matches'] : null;
                    $groupCount = (isset($result['ngroups'])) ? $result['ngroups'] : null;
                    $valueGroups = array();
                    foreach ($result['groups'] as $valueGroupResult) {

                        $value = (isset($valueGroupResult['groupValue'])) ?
                                $valueGroupResult['groupValue'] : null;

                        $numFound = (isset($valueGroupResult['doclist']['numFound'])) ?
                                $valueGroupResult['doclist']['numFound'] : null;

                        $start = (isset($valueGroupResult['doclist']['start'])) ?
                                $valueGroupResult['doclist']['start'] : null;

                        // create document instances
                        $documentClass = $query->getOption('documentclass');
                        $documents = array();
                        if (isset($valueGroupResult['doclist']['docs']) &&
                            is_array($valueGroupResult['doclist']['docs'])) {

                            foreach ($valueGroupResult['doclist']['docs'] as $doc) {
                                $documents[] = new $documentClass($doc);
                            }
                        }

                        $valueGroups[] = new ValueGroup($value, $numFound, $start, $documents);
                    }

                    $groups[$field] = new FieldGroup($matches, $groupCount, $valueGroups);
                }
            }

            // parse query groups
            foreach ($grouping->getQueries() as $groupQuery) {
                if (isset($data['grouped'][$groupQuery])) {
                    $result = $data['grouped'][$groupQuery];

                    // get statistics
                    $matches = (isset($result['matches'])) ? $result['matches'] : null;
                    $numFound = (isset($result['doclist']['numFound'])) ? $result['doclist']['numFound'] : null;
                    $start = (isset($result['doclist']['start'])) ? $result['doclist']['start'] : null;
                    $maxScore = (isset($result['doclist']['maxScore'])) ? $result['doclist']['maxScore'] : null;

                    // create document instances
                    $documentClass = $query->getOption('documentclass');
                    $documents = array();
                    if (isset($result['doclist']['docs']) && is_array($result['doclist']['docs'])) {
                        foreach ($result['doclist']['docs'] as $doc) {
                            $documents[] = new $documentClass($doc);
                        }
                    }

                    // create a group result object
                    $group = new QueryGroup($matches, $numFound, $start, $maxScore, $documents);
                    $groups[$groupQuery] = $group;
                }
            }
        }

        return new Result($groups);
    }
}

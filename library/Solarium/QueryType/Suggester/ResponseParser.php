<?php
/**
 * Copyright 2011 Gasol Wu. PIXNET Digital Media Corporation.
 * All rights reserved.
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
 * @copyright Copyright 2011 Gasol Wu <gasol.wu@gmail.com>
 * @license http://github.com/basdenooijer/solarium/raw/master/COPYING
 *
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */

namespace Solarium\QueryType\Suggester;

use Solarium\Core\Query\AbstractResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface as ResponseParserInterface;
use Solarium\QueryType\Suggester\Result\Result;

/**
 * Parse Suggester response data.
 */
class ResponseParser extends ResponseParserAbstract implements ResponseParserInterface
{
    /**
     * Get result data for the response.
     *
     * @param Result $result
     *
     * @return array
     */
    public function parse($result)
    {
        $data = $result->getData();
        $query = $result->getQuery();

        $suggestions = array();
        $allSuggestions = array();
        $collation = null;

        if (isset($data['spellcheck']['suggestions']) && is_array($data['spellcheck']['suggestions'])) {
            $suggestResults = $data['spellcheck']['suggestions'];
            $termClass = $query->getOption('termclass');

            if ($query->getResponseWriter() == $query::WT_JSON) {
                $suggestResults = $this->convertToKeyValueArray($suggestResults);
            }

            foreach ($suggestResults as $term => $termData) {
                if ($term == 'collation') {
                    $collation = $termData;
                } else {
                    if (!array_key_exists(0, $termData)) {
                        $termData = array($termData);
                    }

                    foreach ($termData as $currentTermData) {
                        $allSuggestions[] = $this->createTerm($termClass, $currentTermData);

                        if (!array_key_exists($term, $suggestions)) {
                            $suggestions[$term] = $this->createTerm($termClass, $currentTermData);
                        }
                    }
                }
            }
        }

        return $this->addHeaderInfo(
            $data,
            array(
                'results' => $suggestions,
                'all' => $allSuggestions,
                'collation' => $collation,
            )
        );
    }

    private function createTerm($termClass, array $termData)
    {
        return new $termClass(
            $termData['numFound'],
            $termData['startOffset'],
            $termData['endOffset'],
            $termData['suggestion']
        );
    }
}

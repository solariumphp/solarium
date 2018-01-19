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

        $dictionaries = [];
        $allSuggestions = [];

        if (isset($data['suggest']) && is_array($data['suggest'])) {
            $dictionaryClass = $query->getOption('dictionaryclass');
            $termClass = $query->getOption('termclass');

            foreach ($data['suggest'] as $dictionary => $dictionaryResults) {
                $terms = [];
                foreach ($dictionaryResults as $term => $termData) {
                    $allSuggestions[] = $this->createTerm($termClass, $termData);
                    $terms[$term] = $this->createTerm($termClass, $termData);
                }
                $dictionaries[$dictionary] = $this->createDictionary($dictionaryClass, $terms);
            }
        }

        return $this->addHeaderInfo(
            $data,
            [
                'results' => $dictionaries,
                'all' => $allSuggestions,
            ]
        );
    }

    private function createDictionary($dictionaryClass, array $terms)
    {
        return new $dictionaryClass(
            $terms
        );
    }

    private function createTerm($termClass, array $termData)
    {
        return new $termClass(
            $termData['numFound'],
            $termData['suggestions']
        );
    }
}

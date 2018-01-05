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
 *
 * @link http://www.solarium-project.org/
 */

/**
 * @namespace
 */

namespace Solarium\Component\ResponseParser;

use Solarium\Core\Query\AbstractQuery;
use Solarium\Component\Suggester as SuggesterComponent;
use Solarium\Component\Result\Suggester\Result;
use Solarium\Core\Query\AbstractResponseParser;
use Solarium\QueryType\Suggester\Result\Dictionary;
use Solarium\QueryType\Suggester\Result\Term;

/**
 * Parse select component Highlighting result from the data.
 */
class Suggester extends AbstractResponseParser implements ComponentParserInterface
{
    /**
     * Parse result data into result objects.
     *
     * @param AbstractQuery       $query
     * @param SuggesterComponent  $suggester
     * @param array               $data
     *
     * @return Result|null
     */
    public function parse($query, $suggester, $data)
    {
        $dictionaries = [];
        $allSuggestions = [];

        if (isset($data['suggest']) && is_array($data['suggest'])) {
            foreach ($data['suggest'] as $dictionary => $dictionaryResults) {
                $terms = [];
                foreach ($dictionaryResults as $term => $termData) {
                    $allSuggestions[] = $this->createTerm($termData);
                    $terms[$term] = $this->createTerm($termData);
                }
                $dictionaries[$dictionary] = $this->createDictionary($terms);
            }
            return new Result($dictionaries, $allSuggestions);
        }

        return null;
    }

    private function createDictionary(array $terms)
    {
        return new Dictionary(
            $terms
        );
    }

    private function createTerm(array $termData)
    {
        return new Term(
            $termData['numFound'],
            $termData['suggestions']
        );
    }
}

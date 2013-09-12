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
namespace Solarium\QueryType\Select\ResponseParser;

use Solarium\Core\Query\ResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface as ResponseParserInterface;
use Solarium\QueryType\Select\Result\Result;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Select\Query\Query;

/**
 * Parse select response data
 */
class ResponseParser extends ResponseParserAbstract implements ResponseParserInterface
{
    /**
     * Get result data for the response
     *
     * @throws RuntimeException
     * @param  Result           $result
     * @return array
     */
    public function parse($result)
    {
        $data = $result->getData();

        /**
         * @var $query Query
         */
        $query = $result->getQuery();

        // create document instances
        $documentClass = $query->getOption('documentclass');
        $classes = class_implements($documentClass);
        if (!in_array('Solarium\QueryType\Select\Result\DocumentInterface', $classes) &&
            !in_array('Solarium\QueryType\Update\Query\Document\DocumentInterface', $classes)
        ) {
            throw new RuntimeException('The result document class must implement a document interface');
        }

        $documents = array();
        if (isset($data['response']['docs'])) {
            foreach ($data['response']['docs'] as $doc) {
                $fields = (array) $doc;
                $documents[] = new $documentClass($fields);
            }
        }

        // component results
        $components = array();
        foreach ($query->getComponents() as $component) {
            $componentParser = $component->getResponseParser();
            if ($componentParser) {
                $components[$component->getType()] = $componentParser->parse($query, $component, $data);
            }
        }

        if (isset($data['response']['numFound'])) {
            $numFound = $data['response']['numFound'];
        } else {
            $numFound = null;
        }

        return $this->addHeaderInfo(
            $data,
            array(
                'numfound' => $numFound,
                'documents' => $documents,
                'components' => $components
            )
        );
    }
}

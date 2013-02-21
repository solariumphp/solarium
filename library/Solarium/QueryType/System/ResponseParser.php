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
namespace Solarium\QueryType\System;
use Solarium\Core\Query\ResponseParser as ResponseParserAbstract;
use Solarium\Core\Query\ResponseParserInterface as ResponseParserInterface;

/**
 * Parse system response data
 */
class ResponseParser extends ResponseParserAbstract implements ResponseParserInterface
{
    /**
     * Get result data for the response
     *
     * @throws RuntimeException
     * @param  \Solarium\Core\Client\Request $result
     * @return array
     */
    public function parse($result)
    {
        $data = $result->getData();
        $result = $this->flattenData($data);
        return $this->addHeaderInfo($data, $result);
    }

    /**
     * Recursive method that flattens the raw data array.
     *
     * A hierarchy of "core" -> "directory" -> "data" is flattened to
     * "CoreDirectoryData". A hierarchy of "lucene" -> "lucene-impl-version" is
     * flattened to "LuceneImplVersion".
     *
     * @param  array $data         The raw data returned by the Solr server.
     * @param  array &$parent_keys Stores the parent keys of the current array.
     * @return array               The flattened array.
     */
    public function flattenData(array $data, array &$parent_keys = array())
    {
        $result = array();

        foreach ($data as $key => $value) {

            // Check if there is another level of the hierarchy. If the array is
            // keyed numerically then the data itself is an array, and we should
            // not recurse into it.
            if (is_array($value) && !isset($data[$key][0])) {

                // Capture parent key, recurse into next level of the hierarchy.
                $parent_keys[] = $key;
                $result += $this->flattenData($data[$key], $parent_keys);

            } else {

                // Break into parts so we can camel case at non-alphanumeric
                // characters. For example, "lucene-impl-version" will be
                // normalized to "LuceneImplVersion".
                $key_parts = preg_split('/[- ]/', $key);
                $normalized_key = $this->camelCaseArray($key_parts);

                // Prevent things like "SystemSystem" and "LuceneLucene" by
                // checking if the parent key matches first part of the
                // normalized key.
                $parent_key = end($parent_keys);
                reset($parent_keys);
                if (false !== stripos($normalized_key, $parent_key)) {
                    $normalized_key = substr($normalized_key, strlen($parent_key));
                }

                // Finalize the normalized key and store the value.
                $normalized_parents = $this->camelCaseArray($parent_keys);
                $result[$normalized_parents . $normalized_key] = $value;
            }
        }

        // Moving up a level in the hierarchy, so pop off the current parent.
        if ($parent_keys) {
            array_pop($parent_keys);
        }

        return $result;
    }

    /**
     * Concatenate and camel case an array.
     *
     * @param  array  $array The array being concatenated and camel cased.
     * @return string
     */
    public function camelCaseArray(array $array)
    {
        return join('', array_map('ucfirst', $array));
    }
}

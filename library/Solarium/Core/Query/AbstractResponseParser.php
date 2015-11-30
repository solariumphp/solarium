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

namespace Solarium\Core\Query;

/**
 * Abstract class for response parsers.
 *
 * Base class with shared functionality for querytype responseparser implementations
 */
abstract class AbstractResponseParser
{
    /**
     * Converts a flat key-value array (alternating rows) as used in Solr JSON results to a real key value array.
     *
     * @param array $data
     *
     * @return array
     */
    public function convertToKeyValueArray($data)
    {
        // key counter to convert values to arrays when keys are re-used
        $keys = array();

        $dataCount = count($data);
        $result = array();
        for ($i = 0; $i < $dataCount; $i += 2) {
            $key  = $data[$i];
            $value = $data[$i+1];
            if (array_key_exists($key, $keys)) {
                if ($keys[$key] == 1) {
                    $result[$key] = array($result[$key]);
                }
                $result[$key][] = $value;
                $keys[$key]++;
            } else {
                $keys[$key] = 1;
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Parses header data (if available) and adds it to result data.
     *
     * @param array $data
     * @param array $result
     *
     * @return mixed
     */
    public function addHeaderInfo($data, $result)
    {
        $status = null;
        $queryTime = null;

        if (isset($data['responseHeader'])) {
            $status = $data['responseHeader']['status'];
            $queryTime = $data['responseHeader']['QTime'];
        }

        $result['status'] = $status;
        $result['queryTime'] = $queryTime;

        return $result;
    }
}

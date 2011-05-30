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
 * @subpackage Query
 */

/**
 * Select query helper
 *
 * Generates small snippets for use in queries, filterqueries and sorting
 *
 * @package Solarium
 * @subpackage Query
 */
class Solarium_Query_Select_Helper
{

    /**
     * Render a range query
     *
     * From and to can be any type of data. For instance int, string or point.
     *
     * Example: rangeQuery('store', '45,-94', '46,-93')
     * Returns: store:[45,-94 TO 46,-93]
     *
     * @static
     * @param string $field
     * @param string $from
     * @param string $to
     * @param boolean $inclusive
     * @return string
     */
    public function rangeQuery($field, $from, $to, $inclusive = true)
    {
        if ($inclusive) {
            return $field . ':[' . $from . ' TO ' . $to . ']';
        } else {
            return $field . ':{' . $from . ' TO ' . $to . '}';
        }
    }

    /**
     * Render a geofilt (distance) filter
     *
     * Find all entries within the distance of a certain point.
     *
     * @static
     * @param  $pointX
     * @param  $pointY
     * @param  $field
     * @param  $distance
     * @return string
     */
    public function geofilt($pointX, $pointY, $field, $distance)
    {
        return $this->qparser(
            'geofilt',
            array(
                'pt' => $pointX.','.$pointY,
                'sfield' => $field,
                'd' => $distance
            )
        );
    }

    /**
     * Render a bbox (boundingbox) filter
     *
     * Exact distance calculations can be somewhat expensive and it can often
     * make sense to use a quick approximation instead. The bbox filter is
     * guaranteed to encompass all of the points of interest, but it may also
     * include other points that are slightly outside of the required distance.
     *
     * @static
     * @param string $pointX
     * @param string $pointY
     * @param string $field
     * @param string $distance
     * @return string
     */
    public function bbox($pointX, $pointY, $field, $distance)
    {
        return $this->qparser(
            'bbox',
            array(
                'pt' => $pointX.','.$pointY,
                'sfield' => $field,
                'd' => $distance
            )
        );
    }

    /**
     * Render a geodist function call
     *
     * geodist is a function query that yields the calculated distance.
     * This gives the flexibility to do a number of interesting things,
     * such as sorting by the distance (Solr can sort by any function query),
     * or combining the distance with the relevancy score,
     * such as boosting by the inverse of the distance.
     *
     * @static
     * @param  $pointX
     * @param  $pointY
     * @param  $field
     * @return string
     */
    public function geodist($pointX, $pointY, $field)
    {
        return $this->functionCall(
            'geodist',
            array($pointX, $pointY, $field)
        );
    }

    /**
     * Render a qparser plugin call
     *
     * @static
     * @param string $name
     * @param array $params
     * @return string
     */
    public function qparser($name, $params = array())
    {
        $output = '{!'.$name;
        foreach ($params AS $key=>$value) {
            $output .= ' ' . $key . '=' . $value;
        }
        $output .= '}';
        
        return $output;
    }

    /**
     * Render a functionCall
     *
     * @static
     * @param string $name
     * @param array $params
     * @return string
     */
    public function functionCall($name, $params = array())
    {
        return $name . '(' . implode($params, ',') . ')';
    }

}
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
 * Query helper
 *
 * Generates small snippets for use in queries, filterqueries and sorting
 *
 * @package Solarium
 * @subpackage Query
 */
class Solarium_Query_Helper
{

    /**
     * Placeholder pattern for use in the assemble method
     *
     * @var string
     */
    protected $_placeHolderPattern = '/%(L|P|T|)([0-9]+)%/i';

    /**
     * Array of parts to use for assembling a query string
     *
     * @var array
     */
    protected $_assembleParts;

    /**
     * Counter to keep dereferenced params unique (within a single query instance)
     *
     * @var int
     */
    protected $_derefencedParamsLastKey = 0;

    /**
     * Solarium_Query instance, optional.
     * Used for dereferenced params.
     *
     * @var Solarium_Query
     */
    protected $_query;

    /**
     * Constructor
     *
     * @param Solarium_Query $query
     */
    public function __construct($query = null)
    {
        $this->_query = $query;
    }

    /**
     * Escape a term
     *
     * A term is a single word.
     * All characters that have a special meaning in a Solr query are escaped.
     *
     * If you want to use the input as a phrase please use the {@link phrase()}
     * method, because a phrase requires much less escaping.\
     *
     * @link http://lucene.apache.org/java/docs/queryparsersyntax.html#Escaping%20Special%20Characters
     *
     * @param string $input
     * @return string
     */
    public function escapeTerm($input)
    {
        $pattern = '/(\+|-|&&|\|\||!|\(|\)|\{|}|\[|]|\^|"|~|\*|\?|:|\\\)/';

        return preg_replace($pattern, '\\\$1', $input);
    }

    /**
     * Escape a phrase
     *
     * A phrase is a group of words.
     * Special characters will be escaped and the phrase will be surrounded by
     * double quotes to group the input into a single phrase. So don't put
     * quotes around the input.
     *
     * Do mind that you cannot build a complete query first and then pass it to
     * this method, the whole query will be escaped. You need to escape only the
     * 'content' of your query.
     *
     * @param string $input
     * @return string
     */
    public function escapePhrase($input)
    {
        return '"' . preg_replace('/("|\\\)/', '\\\$1', $input) . '"';
    }

    /**
     * Format a date to the expected formatting used in SOLR
     *
     * This format was derived to be standards compliant (ISO 8601)
     * A date field shall be of the form 1995-12-31T23:59:59Z The trailing "Z" designates UTC time and is mandatory
     *
     * @see http://lucene.apache.org/solr/api/org/apache/solr/schema/DateField.html
     *
     * @param int|string|DateTime $input accepted formats: timestamp, date string or DateTime
     * @return string|false false is returned in case of invalid input
     */
    public function formatDate($input)
    {
        switch(true){


            // input of datetime object
            case $input instanceof DateTime:
                // no work needed
                break;


            // input of timestamp or date/time string
            case is_string($input) || is_numeric($input):

                // if date/time string: convert to timestamp first
                if (is_string($input)) $input = strtotime($input);

                // now try converting the timestamp to a datetime instance, on failure return false
                try {
                    $input = new DateTime('@' . $input);
                } catch (Exception $e) {
                    $input = false;
                }
                break;


            // any other input formats can be added in additional cases here...
            // case $input instanceof Zend_Date:


            // unsupported input format
            default:
                $input = false;
                break;
        }


        // handle the filtered input
        if ($input) {
            // when we get here the input is always a datetime object
            $input->setTimezone(new DateTimeZone('UTC'));
            $iso8601 = $input->format(DateTime::ISO8601);
            $iso8601 = strstr($iso8601, '+', true); //strip timezone
            $iso8601 .= 'Z';
            return $iso8601;
        } else {
            // unsupported input
            return false;
        }
    }

    /**
     * Render a range query
     *
     * From and to can be any type of data. For instance int, string or point.
     *
     * Example: rangeQuery('store', '45,-94', '46,-93')
     * Returns: store:[45,-94 TO 46,-93]
     *
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
     * @param string $name
     * @param array $params
     * @param boolean $dereferenced
     * @return string
     */
    public function qparser($name, $params = array(), $dereferenced = false)
    {
        if ($dereferenced) {

            if (!$this->_query) {
                throw new Solarium_Exception(
                    'Dereferenced params can only be used in a Solarium_Query_Helper instance retrieved from the query '
                    . 'by using the getHelper() method, this instance was manually created'
                );
            }

            foreach ($params as $paramKey => $paramValue) {
                $this->_derefencedParamsLastKey++;
                $derefKey = 'deref_' . $this->_derefencedParamsLastKey;
                $this->_query->addParam($derefKey, $paramValue);
                $params[$paramKey] = '$'.$derefKey;
            }
        }

        $output = '{!'.$name;
        foreach ($params as $key=>$value) {
            $output .= ' ' . $key . '=' . $value;
        }
        $output .= '}';

        return $output;
    }

    /**
     * Render a functionCall
     *
     * @param string $name
     * @param array $params
     * @return string
     */
    public function functionCall($name, $params = array())
    {
        return $name . '(' . implode($params, ',') . ')';
    }

    /**
     * Assemble a querystring with placeholders
     *
     * These placeholder modes are supported:
     * %1% = no mode, will default to literal
     * %L2% = literal
     * %P3% = phrase-escaped
     * %T4% = term-escaped
     *
     * Numbering starts at 1, so number 1 refers to the first entry
     * of $parts (which has array key 0)
     * You can use the same part multiple times, even in multiple modes.
     * The mode letters are not case sensitive.
     *
     * The mode matching pattern can be customized by overriding the
     * value of $this->_placeHolderPattern
     *
     * @since 2.1.0
     *
     * @param string $query
     * @param array $parts Array of strings
     * @return string
     */
    public function assemble($query, $parts)
    {
        $this->_assembleParts = $parts;

        return preg_replace_callback(
            $this->_placeHolderPattern,
            array($this, '_renderPlaceHolder'),
            $query
        );
    }

    /**
     * Render placeholders in a querystring
     *
     * @throws Solarium_Exception
     * @param array $matches
     * @return string
     */
    protected function _renderPlaceHolder($matches)
    {
        $partNumber = $matches[2];
        $partMode = strtoupper($matches[1]);

        if (isset($this->_assembleParts[$partNumber-1])) {
            $value = $this->_assembleParts[$partNumber-1];
        } else {
            throw new Solarium_Exception('No value supplied for part #' . $partNumber . ' in query assembler');
        }

        switch($partMode)
        {
            case 'P':
                $value = $this->escapePhrase($value);
                break;
            case 'T':
                $value = $this->escapeTerm($value);
                break;
        }

        return $value;
    }

    /**
     * Render join localparams syntax
     *
     * @see http://wiki.apache.org/solr/Join
     * @since 2.4.0
     *
     * @param string $from
     * @param string $to
     * @param boolean $dereferenced
     * @return string
     */
    public function join($from, $to, $dereferenced = false)
    {
        return $this->qparser('join', array('from' => $from, 'to' => $to), $dereferenced);
    }

}

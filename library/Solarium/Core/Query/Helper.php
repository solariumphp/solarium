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

use Solarium\Exception\InvalidArgumentException;

/**
 * Query helper.
 *
 * Generates small snippets for use in queries, filterqueries and sorting
 */
class Helper
{
    /**
     * Placeholder pattern for use in the assemble method.
     *
     * @var string
     */
    protected $placeHolderPattern = '/%(L|P|T|)([0-9]+)%/i';

    /**
     * Array of parts to use for assembling a query string.
     *
     * @var array
     */
    protected $assembleParts;

    /**
     * Counter to keep dereferenced params unique (within a single query instance).
     *
     * @var int
     */
    protected $derefencedParamsLastKey = 0;

    /**
     * Solarium Query instance, optional.
     * Used for dereferenced params.
     *
     * @var AbstractQuery
     */
    protected $query;

    /**
     * Constructor.
     *
     * @param AbstractQuery $query
     */
    public function __construct($query = null)
    {
        $this->query = $query;
    }

    /**
     * Escape a term.
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
     *
     * @return string
     */
    public function escapeTerm($input)
    {
        $pattern = '/(\+|-|&&|\|\||!|\(|\)|\{|}|\[|]|\^|"|~|\*|\?|:|\/|\\\)/';

        return preg_replace($pattern, '\\\$1', $input);
    }

    /**
     * Escape a phrase.
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
     *
     * @return string
     */
    public function escapePhrase($input)
    {
        return '"'.preg_replace('/("|\\\)/', '\\\$1', $input).'"';
    }

    /**
     * Format a date to the expected formatting used in SOLR.
     *
     * This format was derived to be standards compliant (ISO 8601)
     * A date field shall be of the form 1995-12-31T23:59:59Z The trailing "Z" designates UTC time and is mandatory
     *
     * @see http://lucene.apache.org/solr/api/org/apache/solr/schema/DateField.html
     *
     * @param int|string|\DateTime $input accepted formats: timestamp, date string or DateTime
     *
     * @return string|boolean false is returned in case of invalid input
     */
    public function formatDate($input)
    {
        switch (true) {

            // input of datetime object
            case $input instanceof \DateTime:
                // no work needed
                break;

            // input of timestamp or date/time string
            case is_string($input) || is_numeric($input):

                // if date/time string: convert to timestamp first
                if (is_string($input)) {
                    $input = strtotime($input);
                }

                // now try converting the timestamp to a datetime instance, on failure return false
                try {
                    $input = new \DateTime('@'.$input);
                } catch (\Exception $e) {
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
            $input->setTimezone(new \DateTimeZone('UTC'));
            $iso8601 = $input->format(\DateTime::ISO8601);
            $iso8601 = strstr($iso8601, '+', true); //strip timezone
            $iso8601 .= 'Z';

            return $iso8601;
        } else {
            // unsupported input
            return false;
        }
    }

    /**
     * Render a range query.
     *
     * From and to can be any type of data. For instance int, string or point.
     * If they are null, then '*' will be used.
     *
     * Example: rangeQuery('store', '45,-94', '46,-93')
     * Returns: store:[45,-94 TO 46,-93]
     *
     * Example: rangeQuery('store', '5', '*', false)
     * Returns: store:{5 TO *}
     *
     * @param string  $field
     * @param string  $from
     * @param string  $to
     * @param boolean $inclusive
     *
     * @return string
     */
    public function rangeQuery($field, $from, $to, $inclusive = true)
    {
        if ($from === null) {
            $from = '*';
        }

        if ($to === null) {
            $to = '*';
        }

        if ($inclusive) {
            return $field.':['.$from.' TO '.$to.']';
        } else {
            return $field.':{'.$from.' TO '.$to.'}';
        }
    }

    /**
     * Render a geofilt (distance) filter.
     *
     * Find all entries within the distance of a certain point.
     *
     * @param string  $field
     * @param string  $pointX
     * @param string  $pointY
     * @param string  $distance
     * @param boolean $dereferenced
     *
     * @return string
     */
    public function geofilt($field, $pointX, $pointY, $distance, $dereferenced = false)
    {
        return $this->qparser(
            'geofilt',
            array(
                'pt' => $pointX.','.$pointY,
                'sfield' => $field,
                'd' => $distance,
            ),
            $dereferenced
        );
    }

    /**
     * Render a bbox (boundingbox) filter.
     *
     * Exact distance calculations can be somewhat expensive and it can often
     * make sense to use a quick approximation instead. The bbox filter is
     * guaranteed to encompass all of the points of interest, but it may also
     * include other points that are slightly outside of the required distance.
     *
     * @param string  $field
     * @param string  $pointX
     * @param string  $pointY
     * @param string  $distance
     * @param boolean $dereferenced
     *
     * @return string
     */
    public function bbox($field, $pointX, $pointY, $distance, $dereferenced = false)
    {
        return $this->qparser(
            'bbox',
            array(
                'pt' => $pointX.','.$pointY,
                'sfield' => $field,
                'd' => $distance,
            ),
            $dereferenced
        );
    }

    /**
     * Render a geodist function call.
     *
     * geodist is a function query that yields the calculated distance.
     * This gives the flexibility to do a number of interesting things,
     * such as sorting by the distance (Solr can sort by any function query),
     * or combining the distance with the relevancy score,
     * such as boosting by the inverse of the distance.
     *
     * @param string  $field
     * @param string  $pointX
     * @param string  $pointY
     * @param boolean $dereferenced
     *
     * @return string
     */
    public function geodist($field, $pointX, $pointY, $dereferenced = false)
    {
        return $this->functionCall(
            'geodist',
            array('sfield' => $field, 'pt' => $pointX.','.$pointY),
            $dereferenced
        );
    }

    /**
     * Render a qparser plugin call.
     *
     * @throws InvalidArgumentException
     *
     * @param string  $name
     * @param array   $params
     * @param boolean $dereferenced
     * @param boolean $forceKeys
     *
     * @return string
     */
    public function qparser($name, $params = array(), $dereferenced = false, $forceKeys = false)
    {
        if ($dereferenced) {
            if (!$this->query) {
                throw new InvalidArgumentException(
                    'Dereferenced params can only be used in a Solarium query helper instance retrieved from the query '.'by using the getHelper() method, this instance was manually created'
                );
            }

            foreach ($params as $paramKey => $paramValue) {
                if (is_int($paramKey) || $forceKeys) {
                    $this->derefencedParamsLastKey++;
                    $derefKey = 'deref_'.$this->derefencedParamsLastKey;
                } else {
                    $derefKey = $paramKey;
                }
                $this->query->addParam($derefKey, $paramValue);
                $params[$paramKey] = '$'.$derefKey;
            }
        }

        $output = '{!'.$name;
        foreach ($params as $key => $value) {
            if (!$dereferenced || $forceKeys || is_int($key)) {
                $output .= ' '.$key.'='.$value;
            }
        }
        $output .= '}';

        return $output;
    }

    /**
     * Render a functionCall.
     *
     * @param string  $name
     * @param array   $params
     * @param boolean $dereferenced
     *
     * @return string
     */
    public function functionCall($name, $params = array(), $dereferenced = false)
    {
        if ($dereferenced) {
            foreach ($params as $key => $value) {
                $this->query->addParam($key, $value);
            }

            return $name.'()';
        } else {
            return $name.'('.implode($params, ',').')';
        }
    }

    /**
     * Assemble a querystring with placeholders.
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
     * value of $this->placeHolderPattern
     *
     * @since 2.1.0
     *
     * @param string $query
     * @param array  $parts Array of strings
     *
     * @return string
     */
    public function assemble($query, $parts)
    {
        $this->assembleParts = $parts;

        return preg_replace_callback(
            $this->placeHolderPattern,
            array($this, 'renderPlaceHolder'),
            $query
        );
    }

    /**
     * Render join localparams syntax.
     *
     * @see http://wiki.apache.org/solr/Join
     * @since 2.4.0
     *
     * @param string  $from
     * @param string  $to
     * @param boolean $dereferenced
     *
     * @return string
     */
    public function join($from, $to, $dereferenced = false)
    {
        return $this->qparser('join', array('from' => $from, 'to' => $to), $dereferenced, $dereferenced);
    }

    /**
     * Render term query.
     *
     * Useful for avoiding query parser escaping madness when drilling into facets via fq parameters, example:
     * {!term f=weight}1.5
     *
     * This is a Solr 3.2+ feature.
     *
     * @see http://wiki.apache.org/solr/SolrQuerySyntax#Other_built-in_useful_query_parsers
     *
     * @param string $field
     * @param float  $weight
     *
     * @return string
     */
    public function qparserTerm($field, $weight)
    {
        return $this->qparser('term', array('f' => $field)).$weight;
    }

    /**
     * Render cache control param for use in filterquery.
     *
     * This is a Solr 3.4+ feature.
     *
     * @see http://wiki.apache.org/solr/CommonQueryParameters#Caching_of_filters
     *
     * @param boolean    $useCache
     * @param float|null $cost
     *
     * @return string
     */
    public function cacheControl($useCache, $cost = null)
    {
        if ($useCache === true) {
            $cache = 'true';
        } else {
            $cache = 'false';
        }

        $result = '{!cache='.$cache;
        if (null !== $cost) {
            $result .= ' cost='.$cost;
        }
        $result .= '}';

        return $result;
    }

    /**
     * Filters control characters that cause issues with servlet containers.
     *
     * Mainly useful to filter data before adding it to a document for the update query.
     *
     * @param string $data
     *
     * @return mixed
     */
    public function filterControlCharacters($data)
    {
        return preg_replace('@[\x00-\x08\x0B\x0C\x0E-\x1F]@', ' ', $data);
    }

    /**
     * Render placeholders in a querystring.
     *
     * @throws InvalidArgumentException
     *
     * @param array $matches
     *
     * @return string
     */
    protected function renderPlaceHolder($matches)
    {
        $partNumber = $matches[2];
        $partMode = strtoupper($matches[1]);

        if (isset($this->assembleParts[$partNumber-1])) {
            $value = $this->assembleParts[$partNumber-1];
        } else {
            throw new InvalidArgumentException('No value supplied for part #'.$partNumber.' in query assembler');
        }

        switch ($partMode) {
            case 'P':
                $value = $this->escapePhrase($value);
                break;
            case 'T':
                $value = $this->escapeTerm($value);
                break;
        }

        return $value;
    }
}

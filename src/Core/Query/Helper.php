<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Core\Query;

use Solarium\Exception\InvalidArgumentException;
use Solarium\Support\Utility;

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
    protected $placeHolderPattern = '/%(L|P|T|)(\d+)%/i';

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
     * @var QueryInterface
     */
    protected $query;

    /**
     * Constructor.
     *
     * @param QueryInterface|null $query
     */
    public function __construct(?QueryInterface $query = null)
    {
        $this->query = $query;
    }

    /**
     * Escape a term.
     *
     * A term is a single word.
     * All characters that have a special meaning in a Solr query are escaped.
     *
     * If you want to use the input as a phrase please use the {@link escapePhrase()}
     * method, because a phrase requires much less escaping.
     *
     * @see https://solr.apache.org/guide/the-standard-query-parser.html#escaping-special-characters
     *
     * @param string $input
     *
     * @return string
     */
    public function escapeTerm(string $input): string
    {
        if (preg_match('/(^|\s)(AND|OR|TO)($|\s)/', strtoupper($input), $matches)) {
            return $this->escapePhrase($input);
        }

        return preg_replace('/( |\+|-|&&|\|\||!|\(|\)|\{|}|\[|]|\^|"|~|\*|\?|:|\/|\\\)/', '\\\$1', $input);
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
    public function escapePhrase(string $input): string
    {
        return '"'.preg_replace('/("|\\\)/', '\\\$1', $input).'"';
    }

    /**
     * Escape a local parameter value.
     *
     * This method wraps the value in single quotes if it contains a space,
     * a single quote, a double quote, or a right curly bracket. It backslash
     * escapes single quotes and backslashes within that quoted string.
     *
     * If an optional pre-escaped separator character is passed, a backslash
     * preceding this character will not be escaped with another backslash.
     * {@internal Based on splitSmart() in org.apache.solr.common.util.StrUtils}
     *
     * A value that doesn't require quoting is returned as is.
     *
     * @see https://solr.apache.org/guide/local-parameters-in-queries.html#basic-syntax-of-local-parameters
     *
     * @param string      $value
     * @param string|null $preEscapedSeparator Separator character that is already escaped with a backslash
     *
     * @return string
     */
    public function escapeLocalParamValue(string $value, ?string $preEscapedSeparator = null): string
    {
        if (preg_match('/[ \'"}]/', $value)) {
            $pattern = "/('|\\\\)/";

            if (null !== $preEscapedSeparator) {
                $char = preg_quote(substr($preEscapedSeparator, 0, 1), '/');
                $pattern = "/('|\\\\(?!$char))/";
            }

            $value = "'".preg_replace($pattern, '\\\$1', $value)."'";
        }

        return $value;
    }

    /**
     * Format a date to the expected formatting used in Solr.
     *
     * This format was derived to be standards compliant (ISO 8601).
     * A date field shall be of the form 1995-12-31T23:59:59Z.
     * The trailing "Z" designates UTC time and is mandatory.
     *
     * @see https://solr.apache.org/guide/working-with-dates.html#date-formatting
     *
     * @param int|string|\DateTimeInterface $input Accepted formats: timestamp, date string, DateTime or
     *                                             DateTimeImmutable
     *
     * @return string|false false is returned in case of invalid input
     */
    public function formatDate($input)
    {
        switch (true) {
            case $input instanceof \DateTimeInterface:
                // input of DateTime or DateTimeImmutable object
                $input = clone $input;
                break;
            case \is_string($input):
            case is_numeric($input):
                // input of timestamp or date/time string
                // if date/time string: convert to timestamp first
                if (\is_string($input)) {
                    $input = strtotime($input);
                }

                // now try converting the timestamp to a datetime instance, on failure return false
                try {
                    $input = new \DateTime('@'.$input);
                } catch (\Exception $e) {
                    $input = false;
                }
                break;
            default:
                // unsupported input format
                $input = false;
                break;
        }

        // handle the filtered input
        if ($input) {
            // when we get here $input is always a DateTime or DateTimeImmutable object
            $input = $input->setTimezone(new \DateTimeZone('UTC'));

            return $input->format('Y-m-d\TH:i:s\Z');
        }

        // unsupported input
        return false;
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
     * Example: rangeQuery('store', 5, null, false)
     * Returns: store:{5 TO *}
     *
     * Example: rangeQuery('price', 0, 10, [true, false])
     * Returns: price:[0 TO 10}
     *
     * @param string                $field
     * @param int|float|string|null $from
     * @param int|float|string|null $to
     * @param bool|bool[]           $inclusive TRUE or [TRUE, TRUE] for inclusive, FALSE or [FALSE, FALSE] for exclusive,
     *                                         [TRUE, FALSE] for left-inclusive only, [FALSE, TRUE] for right-inclusive only
     *
     * @return string
     */
    public function rangeQuery(string $field, $from, $to, $inclusive = true): string
    {
        if (null === $from) {
            $from = '*';
        } elseif (!\is_int($from) && !\is_float($from) && !Utility::isPointValue($from)) {
            $from = $this->escapePhrase($from);
        }

        if (null === $to) {
            $to = '*';
        } elseif (!\is_int($to) && !\is_float($to) && !Utility::isPointValue($to)) {
            $to = $this->escapePhrase($to);
        }

        if (\is_array($inclusive)) {
            list($leftInclusive, $rightInclusive) = $inclusive;
        } else {
            $leftInclusive = $rightInclusive = $inclusive;
        }

        return sprintf('%s:%s%s TO %s%s', $field, $leftInclusive ? '[' : '{', $from, $to, $rightInclusive ? ']' : '}');
    }

    /**
     * Render a geofilt (distance) filter.
     *
     * Find all entries within the distance of a certain point.
     *
     * @param string $field
     * @param string $pointX
     * @param string $pointY
     * @param string $distance
     * @param bool   $dereferenced
     *
     * @return string
     */
    public function geofilt(string $field, string $pointX, string $pointY, string $distance, bool $dereferenced = false): string
    {
        return $this->qparser(
            'geofilt',
            [
                'pt' => $pointX.','.$pointY,
                'sfield' => $field,
                'd' => $distance,
            ],
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
     * @param string $field
     * @param string $pointX
     * @param string $pointY
     * @param string $distance
     * @param bool   $dereferenced
     *
     * @return string
     */
    public function bbox(string $field, string $pointX, string $pointY, string $distance, bool $dereferenced = false): string
    {
        return $this->qparser(
            'bbox',
            [
                'pt' => $pointX.','.$pointY,
                'sfield' => $field,
                'd' => $distance,
            ],
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
     * @param string $field
     * @param string $pointX
     * @param string $pointY
     * @param bool   $dereferenced
     *
     * @return string
     */
    public function geodist(string $field, string $pointX, string $pointY, bool $dereferenced = false): string
    {
        return $this->functionCall(
            'geodist',
            ['sfield' => $field, 'pt' => $pointX.','.$pointY],
            $dereferenced
        );
    }

    /**
     * Render a qparser plugin call.
     *
     * @param string $name
     * @param array  $params
     * @param bool   $dereferenced
     * @param bool   $forceKeys
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    public function qparser(string $name, array $params = [], bool $dereferenced = false, bool $forceKeys = false): string
    {
        if ($dereferenced) {
            if (!$this->query) {
                throw new InvalidArgumentException('Dereferenced params can only be used in a Solarium query helper instance retrieved from the query by using the getHelper() method, this instance was manually created.');
            }

            foreach ($params as $paramKey => $paramValue) {
                if (\is_int($paramKey) || $forceKeys) {
                    ++$this->derefencedParamsLastKey;
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
            if (!$dereferenced || $forceKeys || \is_int($key)) {
                if (\is_array($value)) {
                    $value = implode(',', $value);
                } elseif (\is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                }

                $output .= ' '.$key.'='.$this->escapeLocalParamValue($value);
            }
        }
        $output .= '}';

        return $output;
    }

    /**
     * Render a functionCall.
     *
     * @param string $name
     * @param array  $params
     * @param bool   $dereferenced
     *
     * @return string
     */
    public function functionCall(string $name, array $params = [], bool $dereferenced = false): string
    {
        if ($dereferenced) {
            foreach ($params as $key => $value) {
                $this->query->addParam($key, $value);
            }

            return $name.'()';
        }

        return $name.'('.implode(',', $params).')';
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
     * of $parts (which has array key 0).
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
    public function assemble(string $query, array $parts): string
    {
        $this->assembleParts = $parts;

        return preg_replace_callback(
            $this->placeHolderPattern,
            [$this, 'renderPlaceHolder'],
            $query
        );
    }

    /**
     * Render join localparams syntax.
     *
     * @see https://solr.apache.org/guide/other-parsers.html#join-query-parser
     *
     * @param string $from
     * @param string $to
     * @param bool   $dereferenced
     *
     * @return string
     */
    public function join(string $from, string $to, $dereferenced = false): string
    {
        return $this->qparser('join', ['from' => $from, 'to' => $to], $dereferenced, $dereferenced);
    }

    /**
     * Render term query.
     *
     * Useful for avoiding query parser escaping madness when drilling into facets via fq parameters, example:
     * {!term f=weight}1.5
     *
     * This is a Solr 3.2+ feature.
     *
     * @see https://solr.apache.org/guide/other-parsers.html#term-query-parser
     *
     * @param string $field
     * @param float  $weight
     *
     * @return string
     */
    public function qparserTerm(string $field, float $weight): string
    {
        return $this->qparser('term', ['f' => $field]).$weight;
    }

    /**
     * Render cache control param for use in filterquery.
     *
     * This is a Solr 3.4+ feature.
     *
     * @see https://solr.apache.org/guide/common-query-parameters.html#cache-parameter
     *
     * @param bool       $useCache
     * @param float|null $cost
     *
     * @return string
     *
     * @deprecated Will be removed in Solarium 6. Use FilterQuery::setCache() and FilterQuery::setCost() instead.
     */
    public function cacheControl(bool $useCache, ?float $cost = null): string
    {
        $cache = 'false';

        if (true === $useCache) {
            $cache = 'true';
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
     * Removing restricted XML chars:
     * [#x1-#x8] | [#xB-#xC] | [#xE-#x1F] | [#x7F] | [#x80-#x84] | [#x86-#x9F].
     * And [#xFDD0-#xFDDF] | [#xFFFE-#xFFFF] | [#xnFFFE-#xnFFFF] where n [1;10]
     * Remove [#x0], this is not described in XML standard, but it was removed
     * in previous version.
     * Inspired by UTF8Utils::checkForIllegalCodepoints.
     *
     * @param string $data
     *
     * @return string
     */
    public function filterControlCharacters(string $data): string
    {
        return preg_replace('/
          [\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F\\x7F] # U+0000 to U+0008, U+000B, U+000C, U+000E to U+001F and U+007F
        |
          \\xC2[\\x80-\\x84\\x86\\x9F] # U+0080 to U+0084, U+0086 to U+009F
        |
          \\xED(?:\\xA0[\\x80-\\xFF]|[\\xA1-\\xBE][\\x00-\\xFF]|\\xBF[\\x00-\\xBF]) # U+D800 to U+DFFFF
        |
          \\xEF\\xB7[\\x90-\\xAF] # U+FDD0 to U+FDEF
        |
          \\xEF\\xBF[\\xBE\\xBF] # U+FFFE and U+FFFF
        |
          [\\xF0-\\xF4][\\x8F-\\xBF]\\xBF[\\xBE\\xBF] # U+nFFFE and U+nFFFF (1 <= n <= 10_{16})
        /x', ' ', $data);
    }

    /**
     * Escape text for use as parsed character data content in an XML element.
     *
     * This escapes characters that can't appear as character data using their
     * corresponding entity references. Per the definition of XML, "&" and "<"
     * MUST be escaped when used in character data, ">" MUST be escaped in the
     * string "]]>" and MAY be otherwise, so we escape it to be safe.
     *
     * @param string $data
     *
     * @return string
     */
    public function escapeXMLCharacterData(string $data): string
    {
        // we don't use htmlspecialchars because it only supports a limited number of character sets
        return str_replace(['&', '<', '>'], ['&amp;', '&lt;', '&gt;'], $data);
    }

    /**
     * Render placeholders in a querystring.
     *
     * @param array $matches
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    protected function renderPlaceHolder(array $matches): string
    {
        $partNumber = $matches[2];
        $partMode = strtoupper($matches[1]);

        if (isset($this->assembleParts[$partNumber - 1])) {
            $value = $this->assembleParts[$partNumber - 1];
        } else {
            throw new InvalidArgumentException(sprintf('No value supplied for part #%d in query assembler', $partNumber));
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

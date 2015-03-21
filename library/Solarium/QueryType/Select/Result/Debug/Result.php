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

namespace Solarium\QueryType\Select\Result\Debug;

/**
 * Select component debug result.
 */
class Result implements \IteratorAggregate, \Countable
{
    /**
     * QueryString.
     *
     * @var string
     */
    protected $queryString;

    /**
     * ParsedQuery.
     *
     * @var string
     */
    protected $parsedQuery;

    /**
     * QueryParser.
     *
     * @var string
     */
    protected $queryParser;

    /**
     * OtherQuery.
     *
     * @var string
     */
    protected $otherQuery;

    /**
     * Explain instance.
     *
     * @var DocumentSet
     */
    protected $explain;

    /**
     * ExplainOther instance.
     *
     * @var DocumentSet
     */
    protected $explainOther;

    /**
     * Timing instance.
     *
     * @var Timing
     */
    protected $timing;

    /**
     * Constructor.
     *
     * @param string      $queryString
     * @param string      $parsedQuery
     * @param string      $queryParser
     * @param string      $otherQuery
     * @param DocumentSet $explain
     * @param DocumentSet $explainOther
     * @param Timing      $timing
     */
    public function __construct($queryString, $parsedQuery, $queryParser, $otherQuery, $explain, $explainOther, $timing)
    {
        $this->queryString = $queryString;
        $this->parsedQuery = $parsedQuery;
        $this->queryParser = $queryParser;
        $this->otherQuery = $otherQuery;
        $this->explain = $explain;
        $this->explainOther = $explainOther;
        $this->timing = $timing;
    }

    /**
     * Get input querystring.
     *
     * @return string
     */
    public function getQueryString()
    {
        return $this->queryString;
    }

    /**
     * Get the result of the queryparser.
     *
     * @return string
     */
    public function getParsedQuery()
    {
        return $this->parsedQuery;
    }

    /**
     * Get the used queryparser.
     *
     * @return string
     */
    public function getQueryParser()
    {
        return $this->queryParser;
    }

    /**
     * Get other query (only available if set in query).
     *
     * @return string
     */
    public function getOtherQuery()
    {
        return $this->otherQuery;
    }

    /**
     * Get explain document set.
     *
     * @return DocumentSet
     */
    public function getExplain()
    {
        return $this->explain;
    }

    /**
     * Get explain other document set (only available if otherquery was set in query).
     *
     * @return DocumentSet
     */
    public function getExplainOther()
    {
        return $this->explainOther;
    }

    /**
     * Get timing object.
     *
     * @return Timing
     */
    public function getTiming()
    {
        return $this->timing;
    }

    /**
     * IteratorAggregate implementation.
     *
     * Iterates the explain results
     *
     * @return DocumentSet
     */
    public function getIterator()
    {
        return $this->explain;
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count()
    {
        return count($this->explain);
    }
}

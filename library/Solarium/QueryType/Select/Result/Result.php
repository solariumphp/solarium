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
namespace Solarium\QueryType\Select\Result;

use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\Core\Query\Result\QueryType as BaseResult;
use Solarium\QueryType\Select\Result\DocumentInterface;

/**
 * Select query result
 *
 * This is the standard resulttype for a select query. Example usage:
 * <code>
 * // total solr results
 * $result->getNumFound();
 *
 * // results fetched
 * count($result);
 *
 * // get a single facet by key
 * $result->getFacet('category');
 *
 * // iterate over fetched docs
 * foreach ($result as $doc) {
 *    ....
 * }
 * </code>
 */
class Result extends BaseResult implements \IteratorAggregate, \Countable
{
    /**
     * Solr numFound
     *
     * This is NOT the number of document fetched from Solr!
     *
     * @var int
     */
    protected $numfound;

    /**
     * Document instances array
     *
     * @var array
     */
    protected $documents;

    /**
     * Component results
     */
    protected $components;

    /**
     * Status code returned by Solr
     *
     * @var int
     */
    protected $status;

    /**
     * Solr index queryTime
     *
     * This doesn't include things like the HTTP responsetime. Purely the Solr
     * query execution time.
     *
     * @var int
     */
    protected $queryTime;

    /**
     * Get Solr status code
     *
     * This is not the HTTP status code! The normal value for success is 0.
     *
     * @return int
     */
    public function getStatus()
    {
        $this->parseResponse();

        return $this->status;
    }

    /**
     * Get Solr query time
     *
     * This doesn't include things like the HTTP responsetime. Purely the Solr
     * query execution time.
     *
     * @return int
     */
    public function getQueryTime()
    {
        $this->parseResponse();

        return $this->queryTime;
    }

    /**
     * get Solr numFound
     *
     * Returns the total number of documents found by Solr (this is NOT the
     * number of document fetched from Solr!)
     *
     * @return int
     */
    public function getNumFound()
    {
        $this->parseResponse();

        return $this->numfound;
    }

    /**
     * Get all documents
     *
     * @return DocumentInterface[]
     */
    public function getDocuments()
    {
        $this->parseResponse();

        return $this->documents;
    }

    /**
     * IteratorAggregate implementation
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        $this->parseResponse();

        return new \ArrayIterator($this->documents);
    }

    /**
     * Countable implementation
     *
     * @return int
     */
    public function count()
    {
        $this->parseResponse();

        return count($this->documents);
    }

    /**
     * Get all component results
     *
     * @return array
     */
    public function getComponents()
    {
        $this->parseResponse();

        return $this->components;
    }

    /**
     * Get a component result by key
     *
     * @param  string $key
     * @return mixed
     */
    public function getComponent($key)
    {
        $this->parseResponse();

        if (isset($this->components[$key])) {
            return $this->components[$key];
        } else {
            return null;
        }
    }

    /**
     * Get morelikethis component result
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\QueryType\Select\Result\MoreLikeThis\Result
     */
    public function getMoreLikeThis()
    {
        return $this->getComponent(SelectQuery::COMPONENT_MORELIKETHIS);
    }

    /**
     * Get highlighting component result
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\QueryType\Select\Result\Highlighting\Result
     */
    public function getHighlighting()
    {
        return $this->getComponent(SelectQuery::COMPONENT_HIGHLIGHTING);
    }

    /**
     * Get grouping component result
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\QueryType\Select\Result\Grouping\Result
     */
    public function getGrouping()
    {
        return $this->getComponent(SelectQuery::COMPONENT_GROUPING);
    }

    /**
     * Get facetset component result
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return FacetSet
     */
    public function getFacetSet()
    {
        return $this->getComponent(SelectQuery::COMPONENT_FACETSET);
    }

    /**
     * Get spellcheck component result
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\QueryType\Select\Result\Spellcheck\Result
     */
    public function getSpellcheck()
    {
        return $this->getComponent(SelectQuery::COMPONENT_SPELLCHECK);
    }

    /**
     * Get stats component result
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\QueryType\Select\Result\Stats\Result
     */
    public function getStats()
    {
        return $this->getComponent(SelectQuery::COMPONENT_STATS);
    }

    /**
     * Get debug component result
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return \Solarium\QueryType\Select\Result\Debug\Result
     */
    public function getDebug()
    {
        return $this->getComponent(SelectQuery::COMPONENT_DEBUG);
    }
}

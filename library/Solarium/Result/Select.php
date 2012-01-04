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
 * @subpackage Result
 */

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
 * foreach ($result AS $doc) {
 *    ....
 * }
 * </code>
 *
 * @package Solarium
 * @subpackage Result
 */
class Solarium_Result_Select extends Solarium_Result_QueryType
    implements IteratorAggregate, Countable
{

    /**
     * Solr numFound
     *
     * This is NOT the number of document fetched from Solr!
     *
     * @var int
     */
    protected $_numfound;

    /**
     * Document instances array
     *
     * @var array
     */
    protected $_documents;

    /**
     * Component results
     */
    protected $_components;

    /**
     * Status code returned by Solr
     *
     * @var int
     */
    protected $_status;

    /**
     * Solr index queryTime
     *
     * This doesn't include things like the HTTP responsetime. Purely the Solr
     * query execution time.
     *
     * @var int
     */
    protected $_queryTime;

    /**
     * Get Solr status code
     *
     * This is not the HTTP status code! The normal value for success is 0.
     *
     * @return int
     */
    public function getStatus()
    {
        $this->_parseResponse();

        return $this->_status;
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
        $this->_parseResponse();

        return $this->_queryTime;
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
        $this->_parseResponse();

        return $this->_numfound;
    }

    /**
     * Get all documents
     *
     * @return array
     */
    public function getDocuments()
    {
        $this->_parseResponse();

        return $this->_documents;
    }

    /**
     * IteratorAggregate implementation
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        $this->_parseResponse();

        return new ArrayIterator($this->_documents);
    }

    /**
     * Countable implementation
     *
     * @return int
     */
    public function count()
    {
        $this->_parseResponse();

        return count($this->_documents);
    }

    /**
     * Get all component results
     *
     * @return array
     */
    public function getComponents()
    {
        $this->_parseResponse();

        return $this->_components;
    }

    /**
     * Get a component result by key
     *
     * @param string $key
     * @return Solarium_Result_Select_Component
     */
    public function getComponent($key)
    {
        $this->_parseResponse();

        if (isset($this->_components[$key])) {
            return $this->_components[$key];
        } else {
            return null;
        }
    }

    /**
     * Get morelikethis component result
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return Solarium_Result_Select_MoreLikeThis
     */
    public function getMoreLikeThis()
    {
        return $this->getComponent(Solarium_Query_Select::COMPONENT_MORELIKETHIS);
    }

    /**
     * Get highlighting component result
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return Solarium_Result_Select_Highlighting
     */
    public function getHighlighting()
    {
        return $this->getComponent(Solarium_Query_Select::COMPONENT_HIGHLIGHTING);
    }

    /**
     * Get grouping component result
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return Solarium_Result_Select_Grouping
     */
    public function getGrouping()
    {
        return $this->getComponent(Solarium_Query_Select::COMPONENT_GROUPING);
    }

    /**
     * Get facetset component result
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return Solarium_Result_Select_FacetSet
     */
    public function getFacetSet()
    {
        return $this->getComponent(Solarium_Query_Select::COMPONENT_FACETSET);
    }

    /**
     * Get spellcheck component result
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return Solarium_Result_Select_Spellcheck
     */
    public function getSpellcheck()
    {
        return $this->getComponent(Solarium_Query_Select::COMPONENT_SPELLCHECK);
    }

    /**
     * Get stats component result
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return Solarium_Result_Select_Stats
     */
    public function getStats()
    {
        return $this->getComponent(Solarium_Query_Select::COMPONENT_STATS);
    }

    /**
     * Get debug component result
     *
     * This is a convenience method that maps presets to getComponent
     *
     * @return Solarium_Result_Select_Debug
     */
    public function getDebug()
    {
        return $this->getComponent(Solarium_Query_Select::COMPONENT_DEBUG);
    }
}
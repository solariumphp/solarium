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
 * @package Solarium
 * @subpackage Result
 */

/**
 * Select query result
 */
class Solarium_Result_Select extends Solarium_Result_Query
    implements Iterator, Countable
{

    /**
     * Number of documents found by Solr (this is NOT the number of document
     * fetched from Solr!)
     *
     * @var int
     */
    protected $_numFound;

    /**
     * Document instances array
     *
     * @var array
     */
    protected $_documents;

    /**
     * Facet result instances
     *
     * @var array
     */
    protected $_facets;

    /**
     * Pointer to document array position for iterator implementation
     *
     * @var int
     */
    protected $_position;

    /**
     * Constructor. This is the only point where data can be set in this
     * immutable value object.
     *
     * @param int $status
     * @param int $queryTime
     * @param int $numFound
     * @param array $documents
     * @param array $facets
     * @return void
     */
    public function __construct($status, $queryTime, $numFound, $documents,
                                $facets)
    {
        $this->_status = $status;
        $this->_queryTime = $queryTime;
        $this->_numFound = $numFound;
        $this->_documents = $documents;
        $this->_facets = $facets;
    }

    /**
     * Returns the total number of documents found by Solr (this is NOT the
     * number of document fetched from Solr!)
     *
     * @return int
     */
    public function getNumFound()
    {
        return $this->_numFound;
    }

    /**
     * Return all fetched documents in an array
     *
     * @return array
     */
    public function getDocuments()
    {
        return $this->_documents;
    }

    /**
     * Return all facet results in an array
     *
     * @return array
     */
    public function getFacets()
    {
        return $this->_facets;
    }

    /**
     * Return a facet result
     *
     * @param string $key
     * @return Solarium_Result_Select_Facet
     */
    public function getFacet($key)
    {
        if (isset($this->_facets[$key])) {
            return $this->_facets[$key];
        } else {
            return null;   
        }
    }

    /**
     * Count method for Countable interface
     *
     * @return int
     */
    public function count()
    {
        return count($this->_documents);
    }

    /**
     * Iterator implementation
     *
     * @return void
     */
    public function rewind()
    {
        $this->_position = 0;
    }

    /**
     * Iterator implementation
     *
     * @return Solarium_Result_Select_Document
     */
    function current()
    {
        return $this->_documents[$this->_position];
    }

    /**
     * Iterator implementation
     *
     * @return integer
     */
    public function key()
    {
        return $this->_position;
    }

    /**
     * Iterator implementation
     *
     * @return void
     */
    public function next()
    {
        ++$this->_position;
    }

    /**
     * Iterator implementation
     *
     * @return boolean
     */
    public function valid()
    {
        return isset($this->_documents[$this->_position]);
    }
}
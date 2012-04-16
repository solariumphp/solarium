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
 */

/**
 * Prefetch plugin
 *
 * This plugin can be used to create an 'endless' iterator over a complete resultset. The iterator will take care of
 * fetching the data in sets (sequential prefetching).
 *
 * @package Solarium
 * @subpackage Plugin
 */
class Solarium_Plugin_PrefetchIterator extends Solarium_Plugin_Abstract implements Iterator, Countable
{

    /**
     * Default options
     *
     * @var array
     */
    protected $_options = array(
        'prefetch' => 100,
    );

    /**
     * Query instance to execute
     *
     * @var Solarium_Query_Select
     */
    protected $_query;

    /**
     * Start position (offset)
     *
     * @var int
     */
    protected $_start = 0;

    /**
     * Last resultset from the query instance
     *
     * @var Solarium_Result_Select
     */
    protected $_result;

    /**
     * Iterator position
     *
     * @var int
     */
    protected $_position;

    /**
     * Documents from the last resultset
     *
     * @var array
     */
    protected $_documents;

    /**
     * Set prefetch option
     *
     * @param integer $value
     * @return self Provides fluent interface
     */
    public function setPrefetch($value)
    {
        return $this->_setOption('prefetch', $value);
    }

    /**
     * Get prefetch option
     *
     * @return integer
     */
    public function getPrefetch()
    {
        return $this->getOption('prefetch');
    }

    /**
     * Set query to use for prefetching
     *
     * @param Solarium_Query_Select $query
     * @return self Provides fluent interface
     */
    public function setQuery($query)
    {
        $this->_query = $query;
        return $this;
    }

    /**
     * Get the query object used
     *
     * @return Solarium_Query_Select
     */
    public function getQuery()
    {
        return $this->_query;
    }

    /**
     * Countable implementation
     *
     * @return int
     */
    public function count()
    {
        // if no results are available yet, get them now
        if (null == $this->_result) $this->_fetchNext();

        return $this->_result->getNumFound();
    }

    /**
     * Iterator implementation
     */
    function rewind()
    {
        $this->_position = 0;

        // this condition prevent useless re-fetching of data if a count is done before the iterator is used
        if ($this->_start !== $this->_options['prefetch']) {
            $this->_start = 0;
        }
    }

    /**
     * Iterator implementation
     */
    function current()
    {
        $adjustedIndex = $this->_position % $this->_options['prefetch'];
        return $this->_documents[$adjustedIndex];
    }

    /**
     * Iterator implementation
     *
     * @return int
     */
    function key()
    {
        return $this->_position;
    }

    /**
     * Iterator implementation
     */
    function next()
    {
        ++$this->_position;
    }

    /**
     * Iterator implementation
     *
     * @return boolean
     */
    function valid()
    {
        $adjustedIndex = $this->_position % $this->_options['prefetch'];

        // this condition prevent useless re-fetching of data if a count is done before the iterator is used
        if ($adjustedIndex == 0 && ($this->_position !== 0 || null == $this->_result)) {
            $this->_fetchNext();
        }

        return isset($this->_documents[$adjustedIndex]);
    }

    /**
     * Fetch the next set of results
     *
     * @return void
     */
    protected function _fetchNext()
    {
        $this->_query->setStart($this->_start)->setRows($this->getPrefetch());
        $this->_result = $this->_client->execute($this->_query);
        $this->_documents = $this->_result->getDocuments();
        $this->_start += $this->getPrefetch();
    }
}
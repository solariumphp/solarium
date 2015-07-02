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

namespace Solarium\Plugin;

use Solarium\Core\Plugin\AbstractPlugin;
use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Select\Result\Result as SelectResult;
use Solarium\QueryType\Select\Result\DocumentInterface;
use Solarium\Core\Client\Endpoint;

/**
 * Prefetch plugin.
 *
 * This plugin can be used to create an 'endless' iterator over a complete resultset. The iterator will take care of
 * fetching the data in sets (sequential prefetching).
 */
class PrefetchIterator extends AbstractPlugin implements \Iterator, \Countable
{
    /**
     * Default options.
     *
     * @var array
     */
    protected $options = array(
        'prefetch' => 100,
    );

    /**
     * Query instance to execute.
     *
     * @var SelectQuery
     */
    protected $query;

    /**
     * Start position (offset).
     *
     * @var int
     */
    protected $start = 0;

    /**
     * Last resultset from the query instance.
     *
     * @var SelectResult
     */
    protected $result;

    /**
     * Iterator position.
     *
     * @var int
     */
    protected $position;

    /**
     * Documents from the last resultset.
     *
     * @var DocumentInterface[]
     */
    protected $documents;

    /**
     * Set prefetch option.
     *
     * @param integer $value
     *
     * @return self Provides fluent interface
     */
    public function setPrefetch($value)
    {
        $this->resetData();

        return $this->setOption('prefetch', $value);
    }

    /**
     * Get prefetch option.
     *
     * @return integer
     */
    public function getPrefetch()
    {
        return $this->getOption('prefetch');
    }

    /**
     * Set query to use for prefetching.
     *
     * @param SelectQuery $query
     *
     * @return self Provides fluent interface
     */
    public function setQuery($query)
    {
        $this->query = $query;
        $this->resetData();

        return $this;
    }

    /**
     * Get the query object used.
     *
     * @return SelectQuery
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set endpoint to use.
     *
     * This overwrites any existing endpoint
     *
     * @param string|Endpoint $endpoint
     *
     * @return self Provides fluent interface
     */
    public function setEndpoint($endpoint)
    {
        return $this->setOption('endpoint', $endpoint);
    }

    /**
     * Get endpoint setting.
     *
     * @return string|Endpoint|null
     */
    public function getEndpoint()
    {
        return $this->getOption('endpoint');
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count()
    {
        // if no results are available yet, get them now
        if (null === $this->result) {
            $this->fetchNext();
        }

        return $this->result->getNumFound();
    }

    /**
     * Iterator implementation.
     */
    public function rewind()
    {
        $this->position = 0;

        // this condition prevent useless re-fetching of data if a count is done before the iterator is used
        if ($this->start !== $this->options['prefetch']) {
            $this->start = 0;
        }
    }

    /**
     * Iterator implementation.
     *
     * @return DocumentInterface
     */
    public function current()
    {
        $adjustedIndex = $this->position % $this->options['prefetch'];

        return $this->documents[$adjustedIndex];
    }

    /**
     * Iterator implementation.
     *
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Iterator implementation.
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Iterator implementation.
     *
     * @return boolean
     */
    public function valid()
    {
        $adjustedIndex = $this->position % $this->options['prefetch'];

        // this condition prevent useless re-fetching of data if a count is done before the iterator is used
        if ($adjustedIndex === 0 && ($this->position !== 0 || null === $this->result)) {
            $this->fetchNext();
        }

        return isset($this->documents[$adjustedIndex]);
    }

    /**
     * Fetch the next set of results.
     */
    protected function fetchNext()
    {
        $this->query->setStart($this->start)->setRows($this->getPrefetch());
        $this->result = $this->client->execute($this->query, $this->getOption('endpoint'));
        $this->documents = $this->result->getDocuments();
        $this->start += $this->getPrefetch();
    }

    /**
     * Reset any cached data / position.
     */
    protected function resetData()
    {
        $this->position = null;
        $this->result = null;
        $this->documents = null;
        $this->start = 0;
    }
}

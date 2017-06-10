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

namespace Solarium\QueryType\Select\Result\Grouping;

use Solarium\QueryType\Select\Query\Query;

/**
 * Select component grouping field value group result.
 *
 * @since 2.1.0
 */
class ValueGroup implements \IteratorAggregate, \Countable
{
    /**
     * Field value.
     *
     * @var string
     */
    protected $value;

    /**
     * NumFound.
     *
     * @var int
     */
    protected $numFound;

    /**
     * Start position.
     *
     * @var int
     */
    protected $start;

    /**
     * Documents in this group.
     *
     * @var array
     */
    protected $documents;

    /**
     * Maximum score in group.
     *
     * @var float
     */
    protected $maximumScore;

    /**
     * @var Query
     */
    protected $query;

    /**
     * Constructor.
     *
     * @param string $value
     * @param int    $numFound
     * @param int    $start
     * @param array  $documents
     * @param float  $maxScore
     * @param Query  $query
     */
    public function __construct($value, $numFound, $start, $documents, $maxScore = null, $query = null)
    {
        $this->value = $value;
        $this->numFound = $numFound;
        $this->start = $start;
        $this->documents = $documents;
        $this->maximumScore = $maxScore;
        $this->query = $query;
    }

    /**
     * Get value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get numFound.
     *
     * @return int
     */
    public function getNumFound()
    {
        return $this->numFound;
    }

    /**
     * Get start.
     *
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Get maximumScore value.
     *
     * @return int
     */
    public function getMaximumScore()
    {
        return $this->maximumScore;
    }

    /**
     * Get all documents.
     *
     * @return array
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * IteratorAggregate implementation.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getDocuments());
    }

    /**
     * Countable implementation.
     *
     * @return int
     */
    public function count()
    {
        return count($this->getDocuments());
    }
}

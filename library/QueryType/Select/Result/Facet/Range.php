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

namespace Solarium\QueryType\Select\Result\Facet;

/**
 * Select range facet result.
 *
 * A multiquery facet will usually return a dataset of multiple count, in each
 * row a range as key and its count. You can access the values as an array using
 * {@link getValues()} or iterate this object.
 *
 * The extra counts 'before', 'between' and 'after' are only available if the
 * right settings for the option 'other' were used in the query.
 */
class Range extends Field
{
    /**
     * Count of all records with field values lower then lower bound of the first range.
     *
     * @var int
     */
    protected $before;

    /**
     * Count of all records with field values greater then the upper bound of the last range.
     *
     * @var int
     */
    protected $after;

    /**
     * Count all records with field values between the start and end bounds of all ranges.
     *
     * @var int
     */
    protected $between;

    /**
     * The lower bound of the ranges.
     *
     * @var string
     */
    protected $start;

    /**
     * The upper bound of all ranges.
     *
     * @var string
     */
    protected $end;

    /**
     * The gap between each range.
     *
     * @var string
     */
    protected $gap;

    /**
     * Constructor.
     *
     * @param array $values
     * @param int   $before
     * @param int   $after
     * @param int   $between
     * @param int   $start
     * @param int   $end
     * @param int   $gap
     */
    public function __construct($values, $before, $after, $between, $start, $end, $gap)
    {
        $this->values = $values;
        $this->before = $before;
        $this->after = $after;
        $this->between = $between;
        $this->start = $start;
        $this->end = $end;
        $this->gap = $gap;
    }

    /**
     * Get 'before' count.
     *
     * Count of all records with field values lower then lower bound of the first range
     * Only available if the 'other' setting was used in the query facet.
     *
     * @return int
     */
    public function getBefore()
    {
        return $this->before;
    }

    /**
     * Get 'after' count.
     *
     * Count of all records with field values greater then the upper bound of the last range
     * Only available if the 'other' setting was used in the query facet.
     *
     * @return int
     */
    public function getAfter()
    {
        return $this->after;
    }

    /**
     * Get 'between' count.
     *
     * Count all records with field values between the start and end bounds of all ranges
     * Only available if the 'other' setting was used in the query facet.
     *
     * @return int
     */
    public function getBetween()
    {
        return $this->between;
    }

    /**
     * Get 'start' value of the ranges.
     *
     * The start value specified in the query facet.
     *
     * @return string
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Get 'end' value of the ranges.
     *
     * The end value specified in the query facet
     *
     * @return string
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Get 'gap' between the start and end of each range.
     *
     * Get the gap specified in the query facet
     *
     * @return string
     */
    public function getGap()
    {
        return $this->gap;
    }
}

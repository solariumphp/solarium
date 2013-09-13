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
namespace Solarium\QueryType\Select\Result\Stats;

/**
 * Select component stats facet value
 */
class FacetValue
{
    /**
     * Facet value
     *
     * @var string
     */
    protected $value;

    /**
     * Stats data
     *
     * @var array
     */
    protected $stats;

    /**
     * Constructor
     *
     * @param string $value
     * @param array  $stats
     */
    public function __construct($value, $stats)
    {
        $this->value = $value;
        $this->stats = $stats;
    }

    /**
     * Get facet value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get min value
     *
     * @return string
     */
    public function getMin()
    {
        return $this->stats['min'];
    }

    /**
     * Get max value
     *
     * @return string
     */
    public function getMax()
    {
        return $this->stats['max'];
    }

    /**
     * Get sum value
     *
     * @return string
     */
    public function getSum()
    {
        return $this->stats['sum'];
    }

    /**
     * Get count value
     *
     * @return string
     */
    public function getCount()
    {
        return $this->stats['count'];
    }

    /**
     * Get missing value
     *
     * @return string
     */
    public function getMissing()
    {
        return $this->stats['missing'];
    }

    /**
     * Get sumOfSquares value
     *
     * @return string
     */
    public function getSumOfSquares()
    {
        return $this->stats['sumOfSquares'];
    }

    /**
     * Get mean value
     *
     * @return string
     */
    public function getMean()
    {
        return $this->stats['mean'];
    }

    /**
     * Get stddev value
     *
     * @return string
     */
    public function getStddev()
    {
        return $this->stats['stddev'];
    }

    /**
     * Get facet stats
     *
     * @return array
     */
    public function getFacets()
    {
        return $this->stats['facets'];
    }
}

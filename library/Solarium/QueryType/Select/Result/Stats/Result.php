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

namespace Solarium\QueryType\Select\Result\Stats;

/**
 * Select component stats field result item.
 */
class Result
{
    /**
     * Field name.
     *
     * @var string
     */
    protected $field;

    /**
     * Stats data.
     *
     * @var array
     */
    protected $stats;

    /**
     * Constructor.
     *
     * @param string $field
     * @param array  $stats
     */
    public function __construct($field, $stats)
    {
        $this->field = $field;
        $this->stats = $stats;
    }

    /**
     * Get field name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->field;
    }

    /**
     * Get min value.
     *
     * @return string
     */
    public function getMin()
    {
        return $this->getValue('min');
    }

    /**
     * Get max value.
     *
     * @return string
     */
    public function getMax()
    {
        return $this->getValue('max');
    }

    /**
     * Get sum value.
     *
     * @return string
     */
    public function getSum()
    {
        return $this->getValue('sum');
    }

    /**
     * Get count value.
     *
     * @return string
     */
    public function getCount()
    {
        return $this->getValue('count');
    }

    /**
     * Get missing value.
     *
     * @return string
     */
    public function getMissing()
    {
        return $this->getValue('missing');
    }

    /**
     * Get sumOfSquares value.
     *
     * @return string
     */
    public function getSumOfSquares()
    {
        return $this->getValue('sumOfSquares');
    }

    /**
     * Get mean value.
     *
     * @return string
     */
    public function getMean()
    {
        return $this->getValue('mean');
    }

    /**
     * Get stddev value.
     *
     * @return string
     */
    public function getStddev()
    {
        return $this->getValue('stddev');
    }

    /**
     * Get facet stats.
     *
     * @return array
     */
    public function getFacets()
    {
        return $this->getValue('facets');
    }
    
    /**
     * Get percentile stats.
     *
     * @return array
     */
    public function getPercentiles()
    {
        return $this->getValue('percentiles');
    }

    /**
     * Get value by name.
     *
     * @return string
     */
    protected function getValue($name)
    {
        return isset($this->stats[$name]) ? $this->stats[$name] : null;
    }    
}

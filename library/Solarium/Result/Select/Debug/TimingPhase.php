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
 * Select component debug timing phase result
 *
 * @package Solarium
 * @subpackage Result
 */
class Solarium_Result_Select_Debug_TimingPhase
    implements IteratorAggregate, Countable
{

    /**
     * Phase name
     *
     * @var string
     */
    protected $_name;

    /**
     * Phase time
     *
     * @var float
     */
    protected $_time;

    /**
     * Timing array
     *
     * @var array
     */
    protected $_timings;

    /**
     * Constructor
     *
     * @param string $name
     * @param float $time
     * @param array $timings
     * @return void
     */
    public function __construct($name, $time, $timings)
    {
        $this->_name = $name;
        $this->_time = $time;
        $this->_timings = $timings;
    }

    /**
     * Get total time
     *
     * @return float
     */
    public function getTime()
    {
        return $this->_time;
    }

    /**
     * Get a timing by key
     *
     * @param mixed $key
     * @return float|null
     */
    public function getTiming($key)
    {
        if (isset($this->_timings[$key])) {
            return $this->_timings[$key];
        } else {
            return null;
        }
    }

    /**
     * Get timings
     *
     * @return array
     */
    public function getTimings()
    {
        return $this->_timings;
    }

    /**
     * IteratorAggregate implementation
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_timings);
    }

    /**
     * Countable implementation
     *
     * @return int
     */
    public function count()
    {
        return count($this->_timings);
    }
}

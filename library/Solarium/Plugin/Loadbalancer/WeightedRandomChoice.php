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
 * Weighted random choice class
 *
 * For use in the loadbalancer plugin
 *
 * @package Solarium
 * @subpackage Plugin
 */
class Solarium_Plugin_Loadbalancer_WeightedRandomChoice
{

    /**
     * Total weight of all choices
     *
     * @var int
     */
    protected $_totalWeight = 0;

    /**
     * Choices total lookup array
     *
     * @var array
     */
    protected $_lookup = array();

    /**
     * Values lookup array
     *
     * @var array
     */
    protected $_values = array();

    /**
     * Constructor
     *
     * @param array $choices
     */
    public function __construct($choices)
    {
        $i = 0;
        foreach ($choices AS $key => $weight) {
            if ($weight <=0) throw new Solarium_Exception('Weight must be greater than zero');

            $this->_totalWeight += $weight;
            $this->_lookup[$i] = $this->_totalWeight;
            $this->_values[$i] = $key;

            $i++;
        }
    }

    /**
     * Get a (weighted) random entry
     *
     * @param array $excludes Keys to exclude
     * @return string
     */
    public function getRandom($excludes = array())
    {
        if (count($excludes) == count($this->_values)) {
            throw new Solarium_Exception('No more server entries available');
        }

        // continue until a non-excluded value is found
        // @todo optimize?
        $result = null;
        while (1) {
            $result = $this->_values[$this->_getKey()];
            if(!in_array($result, $excludes)) break;
        }

        return $result;
    }

    /**
     * Get a (weighted) random entry key
     *
     * @return int
     */
    protected function _getKey()
    {
        $random = mt_rand(1, $this->_totalWeight);
        $high = count($this->_lookup)-1;
        $low = 0;

        while ( $low < $high ) {
            $probe = (int)(($high + $low) / 2);
            if ($this->_lookup[$probe] < $random) {
                $low = $probe + 1;
            } else if ($this->_lookup[$probe] > $random) {
                $high = $probe - 1;
            } else {
                return $probe;
            }
        }

        if ($this->_lookup[$low] >= $random) {
            return $low;
        } else {
            return $low+1;
        }

    }

}
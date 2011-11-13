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
 */

class Solarium_Result_Select_Stats_ResultTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Result_Select_Stats_Result
     */
    protected $_result;

    protected $_field, $_stats;

    public function setUp()
    {
        $this->_field = 'myfield';
        $this->_stats = array(
            'min' => 'dummyMin',
            'max' => 'dummyMax',
            'sum' => 'dummySum',
            'count' => 'dummyCount',
            'missing' => 'dummyMissing',
            'sumOfSquares' => 'dummySos',
            'mean' => 'dummyMean',
            'stddev' => 'dummyStddev',
            'facets' => 'dummyFacets',
        );

        $this->_result = new Solarium_Result_Select_Stats_Result($this->_field, $this->_stats);
    }

    public function testGetName()
    {
        $this->assertEquals($this->_field, $this->_result->getName());
    }

    public function testGetMin()
    {
        $this->assertEquals($this->_stats['min'], $this->_result->getMin());
    }

    public function testGetMax()
    {
        $this->assertEquals($this->_stats['max'], $this->_result->getMax());
    }

    public function testGetSum()
    {
        $this->assertEquals($this->_stats['sum'], $this->_result->getSum());
    }

    public function testGetCount()
    {
        $this->assertEquals($this->_stats['count'], $this->_result->getCount());
    }

    public function testGetMissing()
    {
        $this->assertEquals($this->_stats['missing'], $this->_result->getMissing());
    }

    public function testGetSumOfSquares()
    {
        $this->assertEquals($this->_stats['sumOfSquares'], $this->_result->getSumOfSquares());
    }

    public function testGetMean()
    {
        $this->assertEquals($this->_stats['mean'], $this->_result->getMean());
    }

    public function testGetStddev()
    {
        $this->assertEquals($this->_stats['stddev'], $this->_result->getStddev());
    }

    public function testGetFacets()
    {
        $this->assertEquals($this->_stats['facets'], $this->_result->getFacets());
    }

}

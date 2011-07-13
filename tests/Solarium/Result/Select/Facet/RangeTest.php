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

class Solarium_Result_Select_Facet_RangeTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Result_Select_Facet_Range
     */
    protected $_facet;

    protected $_values, $_before, $_after, $_between;

    public function setUp()
    {
        $this->_values = array(
            '10.0' => 12,
            '20.0' => 5,
            '30.0' => 3,
        );
        $this->_before = 2;
        $this->_after = 4;
        $this->_between = 3;

        $this->_facet = new Solarium_Result_Select_Facet_Range($this->_values, $this->_before, $this->_after, $this->_between);
    }

    public function testGetValues()
    {
        $this->assertEquals($this->_values, $this->_facet->getValues());
    }

    public function testCount()
    {
        $this->assertEquals(count($this->_values), count($this->_facet));
    }

    public function testIterator()
    {
        $values = array();
        foreach($this->_facet AS $key => $value)
        {
            $values[$key] = $value;
        }

        $this->assertEquals($this->_values, $values);
    }

    public function testGetBefore()
    {
        $this->assertEquals($this->_before, $this->_facet->getBefore());
    }

    public function testGetAfter()
    {
        $this->assertEquals($this->_after, $this->_facet->getAfter());
    }

    public function testGetBetween()
    {
        $this->assertEquals($this->_between, $this->_facet->getBetween());
    }
    
}

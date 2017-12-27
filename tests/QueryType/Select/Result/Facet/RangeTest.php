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

namespace Solarium\Tests\QueryType\Select\Result\Facet;

use Solarium\QueryType\Select\Result\Facet\Range;

class RangeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Range
     */
    protected $facet;

    protected $values;
    protected $before;
    protected $after;
    protected $between;
    protected $start;
    protected $end;
    protected $gap;

    public function setUp()
    {
        $this->values = array(
            '10.0' => 12,
            '20.0' => 5,
            '30.0' => 3,
        );

        $this->before = 2;
        $this->after = 4;
        $this->between = 3;
        $this->start = '10.0';
        $this->end = '40.0';
        $this->gap = '10.0';

        $this->facet = new Range(
            $this->values,
            $this->before,
            $this->after,
            $this->between,
            $this->start,
            $this->end,
            $this->gap
        );
    }

    public function testGetValues()
    {
        $this->assertEquals($this->values, $this->facet->getValues());
    }

    public function testCount()
    {
        $this->assertEquals(count($this->values), count($this->facet));
    }

    public function testIterator()
    {
        $values = array();
        foreach ($this->facet as $key => $value) {
            $values[$key] = $value;
        }

        $this->assertEquals($this->values, $values);
    }

    public function testGetBefore()
    {
        $this->assertEquals($this->before, $this->facet->getBefore());
    }

    public function testGetAfter()
    {
        $this->assertEquals($this->after, $this->facet->getAfter());
    }

    public function testGetBetween()
    {
        $this->assertEquals($this->between, $this->facet->getBetween());
    }

    public function testGetStart()
    {
        $this->assertEquals($this->start, $this->facet->getStart());
    }

    public function testGetEnd()
    {
        $this->assertEquals($this->end, $this->facet->getEnd());
    }

    public function testGetGap()
    {
        $this->assertEquals($this->gap, $this->facet->getGap());
    }
}

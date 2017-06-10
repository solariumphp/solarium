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

namespace Solarium\Tests\QueryType\Select\Query\Component\Facet;

use Solarium\QueryType\Select\Query\Component\Facet\Range;
use Solarium\QueryType\Select\Query\Component\FacetSet;

class RangeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Range
     */
    protected $facet;

    public function setUp()
    {
        $this->facet = new Range;
    }

    public function testConfigMode()
    {
        $options = array(
            'key' => 'myKey',
            'exclude' => array('e1', 'e2'),
            'field' => 'content',
            'start' => 1,
            'end' => 100,
            'gap' => 10,
            'hardend' => true,
            'other' => 'all',
            'include' => 'lower',

        );

        $this->facet->setOptions($options);

        $this->assertEquals($options['key'], $this->facet->getKey());
        $this->assertEquals($options['exclude'], $this->facet->getExcludes());
        $this->assertEquals($options['field'], $this->facet->getField());
        $this->assertEquals($options['start'], $this->facet->getStart());
        $this->assertEquals($options['end'], $this->facet->getEnd());
        $this->assertEquals($options['gap'], $this->facet->getGap());
        $this->assertEquals($options['hardend'], $this->facet->getHardend());
        $this->assertEquals(array($options['other']), $this->facet->getOther());
        $this->assertEquals(array($options['include']), $this->facet->getInclude());
    }

    public function testGetType()
    {
        $this->assertEquals(
            FacetSet::FACET_RANGE,
            $this->facet->getType()
        );
    }

    public function testSetMinCount()
    {
        $this->facet->setMinCount(5);

        $this->assertEquals(5, $this->facet->getMinCount());
    }

    public function testSetAndGetField()
    {
        $this->facet->setField('price');
        $this->assertEquals('price', $this->facet->getField());
    }

    public function testSetAndGetStart()
    {
        $this->facet->setStart(1);
        $this->assertEquals(1, $this->facet->getStart());
    }

    public function testSetAndGetEnd()
    {
        $this->facet->setEnd(100);
        $this->assertEquals(100, $this->facet->getEnd());
    }

    public function testSetAndGetGap()
    {
        $this->facet->setGap(10);
        $this->assertEquals(10, $this->facet->getGap());
    }

    public function testSetAndGetHardend()
    {
        $this->facet->setHardend(true);
        $this->assertEquals(true, $this->facet->getHardend());
    }

    public function testSetAndGetOther()
    {
        $this->facet->setOther('all');
        $this->assertEquals(array('all'), $this->facet->getOther());
    }

    public function testSetAndGetOtherArray()
    {
        $this->facet->setOther(array('before', 'after'));
        $this->assertEquals(array('before', 'after'), $this->facet->getOther());
    }

    public function testSetAndGetInclude()
    {
        $this->facet->setInclude('all');
        $this->assertEquals(array('all'), $this->facet->getInclude());
    }

    public function testSetAndGetIncludeArray()
    {
        $this->facet->setInclude(array('lower', 'upper'));
        $this->assertEquals(array('lower', 'upper'), $this->facet->getInclude());
    }
}

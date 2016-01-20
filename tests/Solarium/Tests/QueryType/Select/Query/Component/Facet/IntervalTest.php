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

use Solarium\QueryType\Select\Query\Component\Facet\Interval;
use Solarium\QueryType\Select\Query\Component\FacetSet;

class IntervalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Query
     */
    protected $facet;

    public function setUp()
    {
        $this->facet = new Interval();
    }

    public function testConfigMode()
    {
        $options = array(
            'key' => 'myKey',
            'exclude' => array('e1', 'e2'),
            'set' => array('i1', 'i2'),
        );

        $this->facet->setOptions($options);

        $this->assertEquals($options['key'], $this->facet->getKey());
        $this->assertEquals($options['exclude'], $this->facet->getExcludes());
        $this->assertEquals($options['set'], $this->facet->getSet());
    }

    public function testGetType()
    {
        $this->assertEquals(
            FacetSet::FACET_INTERVAL,
            $this->facet->getType()
        );
    }

    public function testSetAndGetSet()
    {
        $this->facet->setSet('interval1,interval2');
        $this->assertEquals(array('interval1', 'interval2'), $this->facet->getSet());
    }

    public function testEmptySet()
    {
        $this->assertEquals(array(), $this->facet->getSet());
    }

    public function testSetAndGetField()
    {
        $this->facet->setField('field1');
        $this->assertEquals('field1', $this->facet->getField());
    }
}

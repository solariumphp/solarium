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

namespace Solarium\Tests\QueryType\Select\Query\Component\Stats;

use Solarium\QueryType\Select\Query\Component\Stats\Field;

class FieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Field
     */
    protected $field;

    public function setUp()
    {
        $this->field = new Field;
    }

    public function testConfigMode()
    {
        $options = array(
            'facet' => 'field1, field2',
            'pivot' => 'piv1'
        );

        $this->field->setOptions($options);
        $this->assertEquals(array('field1', 'field2'), $this->field->getFacets());
    }

    public function testSetAndGetKey()
    {
        $this->field->setKey('testkey');
        $this->assertEquals('testkey', $this->field->getKey());
    }

    public function testAddFacet()
    {
        $expectedFacets = $this->field->getFacets();
        $expectedFacets[] = 'newfacet';
        $this->field->addFacet('newfacet');
        $this->assertEquals($expectedFacets, $this->field->getFacets());
    }

    public function testClearFacets()
    {
        $this->field->addFacet('newfacet');
        $this->field->clearFacets();
        $this->assertEquals(array(), $this->field->getFacets());
    }

    public function testAddFacets()
    {
        $facets = array('facet1', 'facet2');

        $this->field->clearFacets();
        $this->field->addFacets($facets);
        $this->assertEquals($facets, $this->field->getFacets());
    }

    public function testAddFacetsAsStringWithTrim()
    {
        $this->field->clearFacets();
        $this->field->addFacets('facet1, facet2');
        $this->assertEquals(array('facet1', 'facet2'), $this->field->getFacets());
    }

    public function testRemoveFacet()
    {
        $this->field->clearFacets();
        $this->field->addFacets(array('facet1', 'facet2'));
        $this->field->removeFacet('facet1');
        $this->assertEquals(array('facet2'), $this->field->getFacets());
    }

    public function testSetFacets()
    {
        $this->field->clearFacets();
        $this->field->addFacets(array('facet1', 'facet2'));
        $this->field->setFacets(array('facet3', 'facet4'));
        $this->assertEquals(array('facet3', 'facet4'), $this->field->getFacets());
    }

    public function testAddPivot()
    {
        $expectedPivots = $this->field->getPivots();
        $expectedPivots[] = 'newpivot';
        $this->field->addPivot('newpivot');
        $this->assertEquals($expectedPivots, $this->field->getPivots());
    }

    public function testClearPivots()
    {
        $this->field->addPivot('newpivot');
        $this->field->clearPivots();
        $this->assertEquals(array(), $this->field->getPivots());
    }

    public function testAddPivots()
    {
        $pivots = array('pivot1', 'pivot2');

        $this->field->clearPivots();
        $this->field->addPivots($pivots);
        $this->assertEquals($pivots, $this->field->getPivots());
    }

    public function testAddPivotsAsStringWithTrim()
    {
        $this->field->clearPivots();
        $this->field->addPivots('pivot1, pivot2');
        $this->assertEquals(array('pivot1', 'pivot2'), $this->field->getPivots());
    }

    public function testRemovePivot()
    {
        $this->field->clearPivots();
        $this->field->addPivots(array('pivot1', 'pivot2'));
        $this->field->removePivot('pivot1');
        $this->assertEquals(array('pivot2'), $this->field->getPivots());
    }

    public function testSetPivots()
    {
        $this->field->clearPivots();
        $this->field->addPivots(array('pivot1', 'pivot2'));
        $this->field->setPivots(array('pivot3', 'pivot4'));
        $this->assertEquals(array('pivot3', 'pivot4'), $this->field->getPivots());
    }
}

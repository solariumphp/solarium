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

class Solarium_Query_Select_Component_Stats_FieldTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Query_Select_Component_Stats_Field
     */
    protected $_field;

    public function setUp()
    {
        $this->_field = new Solarium_Query_Select_Component_Stats_Field;
    }

    public function testConfigMode()
    {
        $options = array(
            'facet' => 'field1, field2',
        );

        $this->_field->setOptions($options);
        $this->assertEquals(array('field1','field2'), $this->_field->getFacets());
    }

    public function testSetAndGetKey()
    {
        $this->_field->setKey('testkey');
        $this->assertEquals('testkey', $this->_field->getKey());
    }

    public function testAddFacet()
    {
        $expectedFacets = $this->_field->getFacets();
        $expectedFacets[] = 'newfacet';
        $this->_field->addFacet('newfacet');
        $this->assertEquals($expectedFacets, $this->_field->getFacets());
    }

    public function testClearFacets()
    {
        $this->_field->addFacet('newfacet');
        $this->_field->clearFacets();
        $this->assertEquals(array(), $this->_field->getFacets());
    }

    public function testAddFacets()
    {
        $facets = array('facet1','facet2');

        $this->_field->clearFacets();
        $this->_field->addFacets($facets);
        $this->assertEquals($facets, $this->_field->getFacets());
    }

    public function testAddFacetsAsStringWithTrim()
    {
        $this->_field->clearFacets();
        $this->_field->addFacets('facet1, facet2');
        $this->assertEquals(array('facet1','facet2'), $this->_field->getFacets());
    }

    public function testRemoveFacet()
    {
        $this->_field->clearFacets();
        $this->_field->addFacets(array('facet1','facet2'));
        $this->_field->removeFacet('facet1');
        $this->assertEquals(array('facet2'), $this->_field->getFacets());
    }

    public function testSetFacets()
    {
        $this->_field->clearFacets();
        $this->_field->addFacets(array('facet1','facet2'));
        $this->_field->setFacets(array('facet3','facet4'));
        $this->assertEquals(array('facet3','facet4'), $this->_field->getFacets());
    }
}

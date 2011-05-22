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

class Solarium_Query_Select_Component_Facet_FieldTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Query_Select_Component_Facet_Field
     */
    protected $_facet;

    public function setUp()
    {
        $this->_facet = new Solarium_Query_Select_Component_Facet_Field;
    }

    public function testConfigMode()
    {
        $options = array(
            'key' => 'myKey',
            'exclude' => array('e1','e2'),
            'field' => 'text',
            'sort' => 'index',
            'limit' => 10,
            'offset' => 20,
            'mincount' => 5,
            'missing' => true,
            'method' => 'enum',
        );

        $this->_facet->setOptions($options);
        
        $this->assertEquals($options['key'], $this->_facet->getKey());
        $this->assertEquals($options['exclude'], $this->_facet->getExcludes());
        $this->assertEquals($options['field'], $this->_facet->getField());
        $this->assertEquals($options['sort'], $this->_facet->getSort());
        $this->assertEquals($options['limit'], $this->_facet->getLimit());
        $this->assertEquals($options['offset'], $this->_facet->getOffset());
        $this->assertEquals($options['mincount'], $this->_facet->getMinCount());
        $this->assertEquals($options['missing'], $this->_facet->getMissing());
        $this->assertEquals($options['method'], $this->_facet->getMethod());
    }

    public function testGetType()
    {
        $this->assertEquals(
            Solarium_Query_Select_Component_FacetSet::FACET_FIELD,
            $this->_facet->getType()
        );
    }

    public function testSetAndGetField()
    {
        $this->_facet->setField('category');
        $this->assertEquals('category', $this->_facet->getField());
    }

    public function testSetAndGetSort()
    {
        $this->_facet->setSort('index');
        $this->assertEquals('index', $this->_facet->getSort());
    }

    public function testSetAndGetPrefix()
    {
        $this->_facet->setPrefix('xyz');
        $this->assertEquals('xyz', $this->_facet->getPrefix());
    }

    public function testSetAndGetLimit()
    {
        $this->_facet->setLimit(12);
        $this->assertEquals(12, $this->_facet->getLimit());
    }

    public function testSetAndGetOffset()
    {
        $this->_facet->setOffset(40);
        $this->assertEquals(40, $this->_facet->getOffset());
    }

    public function testSetAndGetMinCount()
    {
        $this->_facet->setMincount(100);
        $this->assertEquals(100, $this->_facet->getMincount());
    }

    public function testSetAndGetMissing()
    {
        $this->_facet->setMissing(true);
        $this->assertEquals(true, $this->_facet->getMissing());
    }

    public function testSetAndGetMethod()
    {
        $this->_facet->setMethod('enum');
        $this->assertEquals('enum', $this->_facet->getMethod());
    }
}

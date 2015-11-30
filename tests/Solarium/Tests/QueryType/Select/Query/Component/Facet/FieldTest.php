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

use Solarium\QueryType\Select\Query\Component\Facet\Field;
use Solarium\QueryType\Select\Query\Component\FacetSet;

class FieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Field
     */
    protected $facet;

    public function setUp()
    {
        $this->facet = new Field;
    }

    public function testConfigMode()
    {
        $options = array(
            'key' => 'myKey',
            'exclude' => array('e1', 'e2'),
            'field' => 'text',
            'sort' => 'index',
            'limit' => 10,
            'offset' => 20,
            'mincount' => 5,
            'missing' => true,
            'method' => 'enum',
            'contains' => 'foobar',
            'containsignorecase' => true,
        );

        $this->facet->setOptions($options);

        $this->assertEquals($options['key'], $this->facet->getKey());
        $this->assertEquals($options['exclude'], $this->facet->getExcludes());
        $this->assertEquals($options['field'], $this->facet->getField());
        $this->assertEquals($options['sort'], $this->facet->getSort());
        $this->assertEquals($options['limit'], $this->facet->getLimit());
        $this->assertEquals($options['offset'], $this->facet->getOffset());
        $this->assertEquals($options['mincount'], $this->facet->getMinCount());
        $this->assertEquals($options['missing'], $this->facet->getMissing());
        $this->assertEquals($options['method'], $this->facet->getMethod());
        $this->assertEquals($options['contains'], $this->facet->getContains());
        $this->assertEquals($options['containsignorecase'], $this->facet->getContainsIgnoreCase());
    }

    public function testGetType()
    {
        $this->assertEquals(
            FacetSet::FACET_FIELD,
            $this->facet->getType()
        );
    }

    public function testSetAndGetField()
    {
        $this->facet->setField('category');
        $this->assertEquals('category', $this->facet->getField());
    }

    public function testSetAndGetSort()
    {
        $this->facet->setSort('index');
        $this->assertEquals('index', $this->facet->getSort());
    }

    public function testSetAndGetPrefix()
    {
        $this->facet->setPrefix('xyz');
        $this->assertEquals('xyz', $this->facet->getPrefix());
    }

    public function testSetAndGetLimit()
    {
        $this->facet->setLimit(12);
        $this->assertEquals(12, $this->facet->getLimit());
    }

    public function testSetAndGetOffset()
    {
        $this->facet->setOffset(40);
        $this->assertEquals(40, $this->facet->getOffset());
    }

    public function testSetAndGetMinCount()
    {
        $this->facet->setMincount(100);
        $this->assertEquals(100, $this->facet->getMincount());
    }

    public function testSetAndGetMissing()
    {
        $this->facet->setMissing(true);
        $this->assertEquals(true, $this->facet->getMissing());
    }

    public function testSetAndGetMethod()
    {
        $this->facet->setMethod('enum');
        $this->assertEquals('enum', $this->facet->getMethod());
    }

    public function testSetAndGetContains()
    {
        $this->facet->setContains('foobar');
        $this->assertEquals('foobar', $this->facet->getContains());
    }

    public function testSetAndGetContainsIgnoreCase()
    {
        $this->facet->setContainsIgnoreCase(true);
        $this->assertEquals(true, $this->facet->getContainsIgnoreCase());
    }
}

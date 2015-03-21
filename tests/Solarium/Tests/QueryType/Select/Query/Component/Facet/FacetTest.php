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

use Solarium\QueryType\Select\Query\Component\Facet\AbstractFacet;

class FacetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractFacet
     */
    protected $facet;

    public function setUp()
    {
        $this->facet = new TestFacet;
    }

    public function testConfigMode()
    {
        $this->facet->setOptions(array('key' => 'myKey', 'exclude' => array('e1', 'e2')));
        $this->assertEquals('myKey', $this->facet->getKey());
        $this->assertEquals(array('e1', 'e2'), $this->facet->getExcludes());
    }

    public function testConfigModeWithSingleValueExclude()
    {
        $this->facet->setOptions(array('exclude' => 'e1'));
        $this->assertEquals(array('e1'), $this->facet->getExcludes());
    }

    public function testSetAndGetKey()
    {
        $this->facet->setKey('testkey');
        $this->assertEquals('testkey', $this->facet->getKey());
    }

    public function testAddExclude()
    {
        $this->facet->addExclude('e1');
        $this->assertEquals(array('e1'), $this->facet->getExcludes());
    }

    public function testAddExcludes()
    {
        $this->facet->addExcludes(array('e1', 'e2'));
        $this->assertEquals(array('e1', 'e2'), $this->facet->getExcludes());
    }

    public function testRemoveExclude()
    {
        $this->facet->addExcludes(array('e1', 'e2'));
        $this->facet->removeExclude('e1');
        $this->assertEquals(array('e2'), $this->facet->getExcludes());
    }

    public function testClearExcludes()
    {
        $this->facet->addExcludes(array('e1', 'e2'));
        $this->facet->clearExcludes();
        $this->assertEquals(array(), $this->facet->getExcludes());
    }

    public function testSetExcludes()
    {
        $this->facet->addExcludes(array('e1', 'e2'));
        $this->facet->setExcludes(array('e3', 'e4'));
        $this->assertEquals(array('e3', 'e4'), $this->facet->getExcludes());
    }
}

class TestFacet extends AbstractFacet
{
    public function getType()
    {
        return 'test';
    }
}

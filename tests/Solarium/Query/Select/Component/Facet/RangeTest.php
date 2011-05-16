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

class Solarium_Query_Select_Component_Facet_RangeTest extends PHPUnit_Framework_TestCase
{

    protected $_facet;

    public function setUp()
    {
        $this->_facet = new Solarium_Query_Select_Component_Facet_Range;
    }

    public function testGetType()
    {
        $this->assertEquals(
            Solarium_Query_Select_Component_FacetSet::FACET_RANGE,
            $this->_facet->getType()
        );
    }

    public function testSetAndGetField()
    {
        $this->_facet->setField('price');
        $this->assertEquals('price', $this->_facet->getField());
    }

    public function testSetAndGetStart()
    {
        $this->_facet->setStart(1);
        $this->assertEquals(1, $this->_facet->getStart());
    }

    public function testSetAndGetEnd()
    {
        $this->_facet->setEnd(100);
        $this->assertEquals(100, $this->_facet->getEnd());
    }

    public function testSetAndGetGap()
    {
        $this->_facet->setGap(10);
        $this->assertEquals(10, $this->_facet->getGap());
    }

    public function testSetAndGetHardend()
    {
        $this->_facet->setHardend(true);
        $this->assertEquals(true, $this->_facet->getHardend());
    }

    public function testSetAndGetOther()
    {
        $this->_facet->setOther('all');
        $this->assertEquals('all', $this->_facet->getOther());
    }

    public function testSetAndGetOtherArray()
    {
        $this->_facet->setOther(array('before','after'));
        $this->assertEquals('before,after', $this->_facet->getOther());
    }

    public function testSetAndGetInclude()
    {
        $this->_facet->setInclude('all');
        $this->assertEquals('all', $this->_facet->getInclude());
    }

    public function testSetAndGetIncludeArray()
    {
        $this->_facet->setInclude(array('lower','upper'));
        $this->assertEquals('lower,upper', $this->_facet->getInclude());
    }

}

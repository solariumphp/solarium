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

class Solarium_Query_TermsTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Query_Terms
     */
    protected $_query;

    public function setUp()
    {
        $this->_query = new Solarium_Query_Terms;
    }

    public function testGetType()
    {
        $this->assertEquals(Solarium_Client::QUERYTYPE_TERMS, $this->_query->getType());
    }

    public function testSetAndGetFields()
    {
        $this->_query->setFields('fieldA,fieldB');
        $this->assertEquals('fieldA,fieldB', $this->_query->getFields());
    }

    public function testSetAndGetLowerbound()
    {
        $this->_query->setLowerbound('f');
        $this->assertEquals('f', $this->_query->getLowerbound());
    }

    public function testSetAndGetLowerboundInclude()
    {
        $this->_query->setLowerboundInclude(true);
        $this->assertEquals(true, $this->_query->getLowerboundInclude());
    }

    public function testSetAndGetMinCount()
    {
        $this->_query->setMinCount(3);
        $this->assertEquals(3, $this->_query->getMinCount());
    }

    public function testSetAndGetMaxCount()
    {
        $this->_query->setMaxCount(25);
        $this->assertEquals(25, $this->_query->getMaxCount());
    }

    public function testSetAndGetPrefix()
    {
        $this->_query->setPrefix('wat');
        $this->assertEquals('wat', $this->_query->getPrefix());
    }

    public function testSetAndGetRegex()
    {
        $this->_query->setRegex('at.*');
        $this->assertEquals('at.*', $this->_query->getRegex());
    }

    public function testSetAndGetRegexFlags()
    {
        $this->_query->setRegexFlags('case_insensitive,comments');
        $this->assertEquals('case_insensitive,comments', $this->_query->getRegexFlags());
    }

    public function testSetAndGetLimit()
    {
        $this->_query->setLimit(15);
        $this->assertEquals(15, $this->_query->getLimit());
    }

    public function testSetAndGetUpperbound()
    {
        $this->_query->setUpperbound('x');
        $this->assertEquals('x', $this->_query->getUpperbound());
    }

    public function testSetAndGetUpperboundInclude()
    {
        $this->_query->setUpperboundInclude(true);
        $this->assertEquals(true, $this->_query->getUpperboundInclude());
    }

    public function testSetAndGetRaw()
    {
        $this->_query->setRaw(false);
        $this->assertEquals(false, $this->_query->getRaw());
    }

    public function testSetAndGetSort()
    {
        $this->_query->setSort('index');
        $this->assertEquals('index', $this->_query->getSort());
    }

}
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

namespace Solarium\Tests\QueryType\Terms;

use Solarium\QueryType\Terms\Query;
use Solarium\Core\Client\Client;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Query
     */
    protected $query;

    public function setUp()
    {
        $this->query = new Query;
    }

    public function testGetType()
    {
        $this->assertEquals(Client::QUERY_TERMS, $this->query->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf('Solarium\QueryType\Terms\ResponseParser', $this->query->getResponseParser());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf('Solarium\QueryType\Terms\RequestBuilder', $this->query->getRequestBuilder());
    }

    public function testSetAndGetFields()
    {
        $this->query->setFields('fieldA,fieldB');
        $this->assertEquals(array('fieldA', 'fieldB'), $this->query->getFields());
    }

    public function testSetAndGetFieldsWithArray()
    {
        $this->query->setFields(array('fieldA', 'fieldB'));
        $this->assertEquals(array('fieldA', 'fieldB'), $this->query->getFields());
    }

    public function testSetAndGetLowerbound()
    {
        $this->query->setLowerbound('f');
        $this->assertEquals('f', $this->query->getLowerbound());
    }

    public function testSetAndGetLowerboundInclude()
    {
        $this->query->setLowerboundInclude(true);
        $this->assertEquals(true, $this->query->getLowerboundInclude());
    }

    public function testSetAndGetMinCount()
    {
        $this->query->setMinCount(3);
        $this->assertEquals(3, $this->query->getMinCount());
    }

    public function testSetAndGetMaxCount()
    {
        $this->query->setMaxCount(25);
        $this->assertEquals(25, $this->query->getMaxCount());
    }

    public function testSetAndGetPrefix()
    {
        $this->query->setPrefix('wat');
        $this->assertEquals('wat', $this->query->getPrefix());
    }

    public function testSetAndGetRegex()
    {
        $this->query->setRegex('at.*');
        $this->assertEquals('at.*', $this->query->getRegex());
    }

    public function testSetAndGetRegexFlags()
    {
        $this->query->setRegexFlags('case_insensitive,comments');
        $this->assertEquals(array('case_insensitive', 'comments'), $this->query->getRegexFlags());
    }

    public function testSetAndGetRegexFlagsWithArray()
    {
        $this->query->setRegexFlags(array('case_insensitive', 'comments'));
        $this->assertEquals(array('case_insensitive', 'comments'), $this->query->getRegexFlags());
    }

    public function testSetAndGetLimit()
    {
        $this->query->setLimit(15);
        $this->assertEquals(15, $this->query->getLimit());
    }

    public function testSetAndGetUpperbound()
    {
        $this->query->setUpperbound('x');
        $this->assertEquals('x', $this->query->getUpperbound());
    }

    public function testSetAndGetUpperboundInclude()
    {
        $this->query->setUpperboundInclude(true);
        $this->assertEquals(true, $this->query->getUpperboundInclude());
    }

    public function testSetAndGetRaw()
    {
        $this->query->setRaw(false);
        $this->assertEquals(false, $this->query->getRaw());
    }

    public function testSetAndGetSort()
    {
        $this->query->setSort('index');
        $this->assertEquals('index', $this->query->getSort());
    }
}

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

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\QueryType\Terms\Query;

class QueryTest extends TestCase
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
        $this->assertSame(Client::QUERY_TERMS, $this->query->getType());
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
        $this->assertSame(array('fieldA', 'fieldB'), $this->query->getFields());
    }

    public function testSetAndGetFieldsWithArray()
    {
        $this->query->setFields(array('fieldA', 'fieldB'));
        $this->assertSame(array('fieldA', 'fieldB'), $this->query->getFields());
    }

    public function testSetAndGetLowerbound()
    {
        $this->query->setLowerbound('f');
        $this->assertSame('f', $this->query->getLowerbound());
    }

    public function testSetAndGetLowerboundInclude()
    {
        $this->query->setLowerboundInclude(true);
        $this->assertSame(true, $this->query->getLowerboundInclude());
    }

    public function testSetAndGetMinCount()
    {
        $this->query->setMinCount(3);
        $this->assertSame(3, $this->query->getMinCount());
    }

    public function testSetAndGetMaxCount()
    {
        $this->query->setMaxCount(25);
        $this->assertSame(25, $this->query->getMaxCount());
    }

    public function testSetAndGetPrefix()
    {
        $this->query->setPrefix('wat');
        $this->assertSame('wat', $this->query->getPrefix());
    }

    public function testSetAndGetRegex()
    {
        $this->query->setRegex('at.*');
        $this->assertSame('at.*', $this->query->getRegex());
    }

    public function testSetAndGetRegexFlags()
    {
        $this->query->setRegexFlags('case_insensitive,comments');
        $this->assertSame(array('case_insensitive', 'comments'), $this->query->getRegexFlags());
    }

    public function testSetAndGetRegexFlagsWithArray()
    {
        $this->query->setRegexFlags(array('case_insensitive', 'comments'));
        $this->assertSame(array('case_insensitive', 'comments'), $this->query->getRegexFlags());
    }

    public function testSetAndGetLimit()
    {
        $this->query->setLimit(15);
        $this->assertSame(15, $this->query->getLimit());
    }

    public function testSetAndGetUpperbound()
    {
        $this->query->setUpperbound('x');
        $this->assertSame('x', $this->query->getUpperbound());
    }

    public function testSetAndGetUpperboundInclude()
    {
        $this->query->setUpperboundInclude(true);
        $this->assertSame(true, $this->query->getUpperboundInclude());
    }

    public function testSetAndGetRaw()
    {
        $this->query->setRaw(false);
        $this->assertSame(false, $this->query->getRaw());
    }

    public function testSetAndGetSort()
    {
        $this->query->setSort('index');
        $this->assertSame('index', $this->query->getSort());
    }
}

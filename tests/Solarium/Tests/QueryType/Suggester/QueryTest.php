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

namespace Solarium\Tests\QueryType\Suggester;

use Solarium\QueryType\Suggester\Query;
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
        $this->assertEquals(Client::QUERY_SUGGESTER, $this->query->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf('Solarium\QueryType\Suggester\ResponseParser', $this->query->getResponseParser());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf('Solarium\QueryType\Suggester\RequestBuilder', $this->query->getRequestBuilder());
    }

    public function testSetAndGetQuery()
    {
        $value = 'testquery';
        $this->query->setQuery($value);

        $this->assertEquals(
            $value,
            $this->query->getQuery()
        );
    }

    public function testSetAndGetDictionary()
    {
        $value = 'myDictionary';
        $this->query->setDictionary($value);

        $this->assertEquals(
            $value,
            $this->query->getDictionary()
        );
    }

    public function testSetAndGetCount()
    {
        $value = 11;
        $this->query->setCount($value);

        $this->assertEquals(
            $value,
            $this->query->getCount()
        );
    }

    public function testSetAndGetOnlyMorePopular()
    {
        $value = false;
        $this->query->setOnlyMorePopular($value);

        $this->assertEquals(
            $value,
            $this->query->getOnlyMorePopular()
        );
    }

    public function testSetAndGetCollate()
    {
        $value = false;
        $this->query->setCollate($value);

        $this->assertEquals(
            $value,
            $this->query->getCollate()
        );
    }
}

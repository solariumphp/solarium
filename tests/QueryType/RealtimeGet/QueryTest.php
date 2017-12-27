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

namespace Solarium\Tests\QueryType\RealtimeGet;

use Solarium\QueryType\RealtimeGet\Query;
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
        $this->assertEquals(Client::QUERY_REALTIME_GET, $this->query->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Select\ResponseParser\ResponseParser',
            $this->query->getResponseParser()
        );
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf('Solarium\QueryType\RealtimeGet\RequestBuilder', $this->query->getRequestBuilder());
    }

    public function testSetAndGetDocumentClass()
    {
        $this->query->setDocumentClass('MyDocument');
        $this->assertEquals('MyDocument', $this->query->getDocumentClass());
    }

    public function testGetComponents()
    {
        $this->assertEquals(array(), $this->query->getComponents());
    }

    public function testAddId()
    {
        $expectedIds = $this->query->getIds();
        $expectedIds[] = 'newid';
        $this->query->addId('newid');
        $this->assertEquals($expectedIds, $this->query->getIds());
    }

    public function testClearIds()
    {
        $this->query->addId('newid');
        $this->query->clearIds();
        $this->assertEquals(array(), $this->query->getIds());
    }

    public function testAddIds()
    {
        $ids = array('id1', 'id2');

        $this->query->clearIds();
        $this->query->addIds($ids);
        $this->assertEquals($ids, $this->query->getIds());
    }

    public function testAddIdsAsStringWithTrim()
    {
        $this->query->clearIds();
        $this->query->addIds('id1, id2');
        $this->assertEquals(array('id1', 'id2'), $this->query->getIds());
    }

    public function testRemoveId()
    {
        $this->query->clearIds();
        $this->query->addIds(array('id1', 'id2'));
        $this->query->removeId('id1');
        $this->assertEquals(array('id2'), $this->query->getIds());
    }

    public function testSetIds()
    {
        $this->query->clearIds();
        $this->query->addIds(array('id1', 'id2'));
        $this->query->setIds(array('id3', 'id4'));
        $this->assertEquals(array('id3', 'id4'), $this->query->getIds());
    }
}

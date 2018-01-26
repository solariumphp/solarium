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

namespace Solarium\Tests\Core\Query\Result;

use Solarium\Core\Client\Client;
use Solarium\Core\Client\Response;
use Solarium\Core\Query\Result\QueryType as QueryTypeResult;
use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Update\Query\Query as UpdateQuery;

class QueryTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QueryTypeDummy
     */
    protected $result;

    public function setUp()
    {
        $client = new Client;
        $query = new UpdateQuery;
        $response = new Response('{"responseHeader":{"status":1,"QTime":12}}', array('HTTP 1.1 200 OK'));
        $this->result = new QueryTypeDummy($client, $query, $response);
    }

    public function testParseResponse()
    {
        $client = new Client;
        $query = new QueryDummyTest;
        $response = new Response('{"responseHeader":{"status":1,"QTime":12}}', array('HTTP 1.1 200 OK'));
        $result = new QueryTypeDummy($client, $query, $response);

        $this->setExpectedException('Solarium\Exception\UnexpectedValueException');
        $result->parse();
    }

    public function testParseResponseInvalidQuerytype()
    {
        $this->result->parse();
    }

    public function testParseLazyLoading()
    {
        $this->assertEquals(0, $this->result->parseCount);

        $this->result->parse();
        $this->assertEquals(1, $this->result->parseCount);

        $this->result->parse();
        $this->assertEquals(1, $this->result->parseCount);
    }

    public function testMapData()
    {
        $this->result->mapData(array('dummyvar' => 'dummyvalue'));

        $this->assertEquals('dummyvalue', $this->result->getVar('dummyvar'));
    }
}

class QueryDummyTest extends SelectQuery
{
    public function getType()
    {
        return 'dummy';
    }

    public function getResponseParser()
    {
        return null;
    }
}

class QueryTypeDummy extends QueryTypeResult
{
    public $parseCount = 0;

    public function parse()
    {
        $this->parseResponse();
    }

    public function mapData($data)
    {
        $this->parseCount++;
        parent::mapData($data);
    }

    public function getVar($name)
    {
        return $this->$name;
    }
}

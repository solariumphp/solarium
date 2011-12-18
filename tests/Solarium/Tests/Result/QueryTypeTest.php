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

namespace Solarium\Tests\Result;

class QueryTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QueryTypeDummy
     */
    protected $_result;

    public function setUp()
    {
        $client = new \Solarium\Client\Client;
        $query = new \Solarium\Query\Update\Update;
        $response = new \Solarium\Client\Response('{"responseHeader":{"status":1,"QTime":12}}',array('HTTP 1.1 200 OK'));
        $this->_result = new QueryTypeDummy($client, $query, $response);
    }

    public function testParseResponse()
    {
        $client = new \Solarium\Client\Client;
        $query = new QueryDummyTest;
        $response = new \Solarium\Client\Response('{"responseHeader":{"status":1,"QTime":12}}',array('HTTP 1.1 200 OK'));
        $result = new QueryTypeDummy($client, $query, $response);

        $this->setExpectedException('Solarium\Exception');
        $result->parse();
    }

    public function testParseResponseInvalidQuerytype()
    {
        $this->_result->parse();
    }

    public function testParseLazyLoading()
    {
        $this->assertEquals(0,$this->_result->parseCount);

        $this->_result->parse();
        $this->assertEquals(1,$this->_result->parseCount);

        $this->_result->parse();
        $this->assertEquals(1,$this->_result->parseCount);
    }

    public function testMapData()
    {
        $this->_result->mapData(array('dummyvar' => 'dummyvalue'));

        $this->assertEquals('dummyvalue',$this->_result->getVar('dummyvar'));
    }
    
}

class QueryDummyTest extends \Solarium\Query\Select\Select
{
    public function getType()
    {
        return 'dummy';
    }
}

class QueryTypeDummy extends \Solarium\Result\QueryType
{

    public $parseCount = 0;

    public function parse()
    {
        $this->_parseResponse();
    }

    public function _mapData($data)
    {
        $this->parseCount++;
        parent::_mapData($data);
    }

    public function mapData($data)
    {
        $this->_mapData($data);
    }

    public function getVar($name)
    {
        return $this->{'_'.$name};
    }

}
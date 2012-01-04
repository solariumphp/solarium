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

class Solarium_Plugin_PostBigRequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Solarium_Plugin_PostBigRequest
     */
    protected $_plugin;

    /**
     * @var Solarium_Client
     */
    protected $_client;

    /**
     * @var Solarium_Query_Select
     */
    protected $query;

    public function setUp()
    {
        $this->_plugin = new Solarium_Plugin_PostBigRequest();

        $this->_client = new Solarium_Client();
        $this->_query = $this->_client->createSelect();

    }

    public function testSetAndGetMaxQueryStringLength()
    {
        $this->_plugin->setMaxQueryStringLength(512);
        $this->assertEquals(512, $this->_plugin->getMaxQueryStringLength());
    }

    public function testPostCreateRequest()
    {
        // create a very long query
        $fq = '';
        for($i=1; $i<=1000; $i++) {
           $fq .= ' OR price:'.$i;
        }
        $fq = substr($fq, 4);
        $this->_query->createFilterQuery('fq')->setQuery($fq);

        $requestOutput = $this->_client->createRequest($this->_query);
        $requestInput = clone $requestOutput;
        $this->_plugin->postCreateRequest($this->_query, $requestOutput);

        $this->assertEquals(Solarium_Client_Request::METHOD_GET, $requestInput->getMethod());
        $this->assertEquals(Solarium_Client_Request::METHOD_POST, $requestOutput->getMethod());
        $this->assertEquals($requestInput->getQueryString(), $requestOutput->getRawData());
        $this->assertEquals('', $requestOutput->getQueryString());
    }

    public function testPostCreateRequestUnalteredSmallRequest()
    {
        $requestOutput = $this->_client->createRequest($this->_query);
        $requestInput = clone $requestOutput;
        $this->_plugin->postCreateRequest($this->_query, $requestOutput);

        $this->assertEquals($requestInput, $requestOutput);
    }

    public function testPostCreateRequestUnalteredPostRequest()
    {
        $query = $this->_client->createUpdate();
        $query->addDeleteById(1);

        $requestOutput = $this->_client->createRequest($query);
        $requestInput = clone $requestOutput;
        $this->_plugin->postCreateRequest($query, $requestOutput);

        $this->assertEquals($requestInput, $requestOutput);
    }

}
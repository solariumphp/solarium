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
use Solarium\Core\Query\Result\Result;
use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\Exception\HttpException;

class ResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Result
     */
    protected $result;

    protected $client;
    protected $query;
    protected $response;
    protected $headers;

    public function setUp()
    {
        $this->client = new Client();
        $this->query = new SelectQuery();
        $this->headers = array('HTTP/1.0 304 Not Modified');
        $data = '{"responseHeader":{"status":0,"QTime":1,"params":{"wt":"json","q":"xyz"}},'.
            '"response":{"numFound":0,"start":0,"docs":[]}}';
        $this->response = new Response($data, $this->headers);

        $this->result = new Result($this->client, $this->query, $this->response);
    }

    public function testResultWithErrorResponse()
    {
        $headers = array('HTTP/1.0 404 Not Found');
        $response = new Response('Error message', $headers);

        $this->setExpectedException('Solarium\Exception\HttpException');
        new Result($this->client, $this->query, $response);
    }

    public function testExceptionGetBody()
    {
        $headers = array('HTTP/1.0 404 Not Found');
        $response = new Response('Error message', $headers);

        try {
            new Result($this->client, $this->query, $response);
        } catch (HttpException $e) {
            $this->assertEquals('Error message', $e->getBody());
        }
    }

    public function testGetResponse()
    {
        $this->assertEquals($this->response, $this->result->getResponse());
    }

    public function testGetQuery()
    {
        $this->assertEquals($this->query, $this->result->getQuery());
    }

    public function testGetData()
    {
        $data = array(
            'responseHeader' => array('status' => 0, 'QTime' => 1, 'params' => array('wt' => 'json', 'q' => 'xyz')),
            'response' => array('numFound' => 0, 'start' => 0, 'docs' => array())
        );

        $this->assertEquals($data, $this->result->getData());
    }

    public function testGetDataWithPhps()
    {
        $phpsData = 'a:2:{s:14:"responseHeader";a:3:{s:6:"status";i:0;s:5:"QTime";i:0;s:6:"params";'.
            'a:6:{s:6:"indent";s:2:"on";s:5:"start";s:1:"0";s:1:"q";s:3:"*:*";s:2:"wt";s:4:"phps";s:7:"version";'.
            's:3:"2.2";s:4:"rows";s:1:"0";}}s:8:"response";a:3:{s:8:"numFound";i:57;s:5:"start";i:0;s:4:"docs";'.
            'a:0:{}}}';
        $this->query->setResponseWriter('phps');
        $resultData = array(
            'responseHeader' => array(
                'status' => 0,
                'QTime' => 0,
                'params' => array(
                    'indent' => 'on',
                    'start' => 0,
                    'q' => '*:*',
                    'wt' => 'phps',
                    'version' => '2.2',
                    'rows' => 0,
                )
            ),
            'response' => array('numFound' => 57, 'start' => 0, 'docs' => array())
        );

        $response = new Response($phpsData, $this->headers);
        $result = new Result($this->client, $this->query, $response);

        $this->assertEquals($resultData, $result->getData());
    }

    public function testGetDataWithUnkownResponseWriter()
    {
        $this->query->setResponseWriter('asdf');
        $result = new Result($this->client, $this->query, $this->response);

        $this->setExpectedException('Solarium\Exception\RuntimeException');
        $result->getData();
    }

    public function testGetInvalidData()
    {
        $data = 'invalid';
        $this->response = new Response($data, $this->headers);
        $this->result = new Result($this->client, $this->query, $this->response);

        $this->setExpectedException('Solarium\Exception\UnexpectedValueException');
        $this->result->getData();
    }
}

<?php

namespace Solarium\Tests\Core\Query\Result;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\Core\Query\Result\Result;
use Solarium\Exception\HttpException;
use Solarium\Exception\RuntimeException;
use Solarium\Exception\UnexpectedValueException;
use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\Tests\Integration\TestClientFactory;

class ResultTest extends TestCase
{
    /**
     * @var Result
     */
    protected $result;

    protected $client;

    /**
     * @var SelectQuery
     */
    protected $query;

    protected $response;

    protected $data;

    protected $headers;

    public function setUp(): void
    {
        $this->client = TestClientFactory::createWithCurlAdapter();
        $this->query = new SelectQuery();
        $this->headers = ['HTTP/1.0 304 Not Modified'];
        $this->data = '{"responseHeader":{"status":0,"QTime":1,"params":{"wt":"json","q":"xyz"}},'.
            '"response":{"numFound":0,"start":0,"docs":[]}}';
        $this->response = new Response($this->data, $this->headers);

        $this->result = new Result($this->query, $this->response);
    }

    public function testResultWithErrorResponse()
    {
        $headers = ['HTTP/1.0 404 Not Found'];
        $response = new Response('Error message', $headers);

        $this->expectException(HttpException::class);
        new Result($this->query, $response);
    }

    public function testExceptionGetBody()
    {
        $headers = ['HTTP/1.0 404 Not Found'];
        $response = new Response('Error message', $headers);

        try {
            new Result($this->query, $response);
        } catch (HttpException $e) {
            $this->assertSame('Error message', $e->getBody());
        }
    }

    public function testGetResponse()
    {
        $this->assertSame($this->response, $this->result->getResponse());
    }

    public function testGetQuery()
    {
        $this->assertSame($this->query, $this->result->getQuery());
    }

    public function testGetData()
    {
        $data = [
            'responseHeader' => ['status' => 0, 'QTime' => 1, 'params' => ['wt' => 'json', 'q' => 'xyz']],
            'response' => ['numFound' => 0, 'start' => 0, 'docs' => []],
        ];

        $this->assertEquals($data, $this->result->getData());
    }

    public function testGetDataWithPhps()
    {
        $phpsData = 'a:2:{s:14:"responseHeader";a:3:{s:6:"status";i:0;s:5:"QTime";i:0;s:6:"params";'.
            'a:6:{s:6:"indent";s:2:"on";s:5:"start";s:1:"0";s:1:"q";s:3:"*:*";s:2:"wt";s:4:"phps";s:7:"version";'.
            's:3:"2.2";s:4:"rows";s:1:"0";}}s:8:"response";a:3:{s:8:"numFound";i:57;s:5:"start";i:0;s:4:"docs";'.
            'a:0:{}}}';
        $this->query->setResponseWriter('phps');
        $resultData = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 0,
                'params' => [
                    'indent' => 'on',
                    'start' => 0,
                    'q' => '*:*',
                    'wt' => 'phps',
                    'version' => '2.2',
                    'rows' => 0,
                ],
            ],
            'response' => ['numFound' => 57, 'start' => 0, 'docs' => []],
        ];

        $response = new Response($phpsData, $this->headers);
        $result = new Result($this->query, $response);

        $this->assertEquals($resultData, $result->getData());
    }

    public function testGetDataWithUnkownResponseWriter()
    {
        $this->query->setResponseWriter('asdf');
        $result = new Result($this->query, $this->response);

        $this->expectException(RuntimeException::class);
        $result->getData();
    }

    public function testGetInvalidData()
    {
        $data = 'invalid';
        $this->response = new Response($data, $this->headers);
        $this->result = new Result($this->query, $this->response);

        $this->expectException(UnexpectedValueException::class);
        $this->result->getData();
    }

    public function testJsonSerialize()
    {
        $this->assertJsonStringEqualsJsonString($this->data, json_encode($this->result));
    }
}

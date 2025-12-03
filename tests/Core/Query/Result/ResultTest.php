<?php

namespace Solarium\Tests\Core\Query\Result;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Response;
use Solarium\Core\Query\Result\Result;
use Solarium\Exception\HttpException;
use Solarium\Exception\RuntimeException;
use Solarium\Exception\UnexpectedValueException;
use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\Tests\Integration\TestClientFactory;

class ResultTest extends TestCase
{
    protected Result $result;

    protected Client $client;

    protected SelectQuery $query;

    protected Response $response;

    protected string $data;

    protected array $headers;

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

    public function testResultWithErrorResponse(): void
    {
        $headers = ['HTTP/1.0 404 Not Found'];
        $response = new Response('Error message', $headers);

        $this->expectException(HttpException::class);
        new Result($this->query, $response);
    }

    public function testExceptionGetBody(): void
    {
        $headers = ['HTTP/1.0 404 Not Found'];
        $response = new Response('Error message', $headers);

        try {
            new Result($this->query, $response);
        } catch (HttpException $e) {
            $this->assertSame('Error message', $e->getBody());
        }
    }

    public function testGetResponse(): void
    {
        $this->assertSame($this->response, $this->result->getResponse());
    }

    public function testGetQuery(): void
    {
        $this->assertSame($this->query, $this->result->getQuery());
    }

    public function testGetData(): void
    {
        $data = [
            'responseHeader' => ['status' => 0, 'QTime' => 1, 'params' => ['wt' => 'json', 'q' => 'xyz']],
            'response' => ['numFound' => 0, 'start' => 0, 'docs' => []],
        ];

        $this->assertEquals($data, $this->result->getData());
    }

    public function testGetDataWithPhps(): void
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

    public function testGetDataWithUnkownResponseWriter(): void
    {
        $this->query->setResponseWriter('asdf');
        $result = new Result($this->query, $this->response);

        $this->expectException(RuntimeException::class);
        $result->getData();
    }

    public function testGetInvalidJsonData(): void
    {
        $this->query->setResponseWriter($this->query::WT_JSON);

        $data = 'invalid';
        $this->response = new Response($data, $this->headers);
        $this->result = new Result($this->query, $this->response);

        $this->expectException(UnexpectedValueException::class);
        $this->result->getData();
    }

    public function testGetInvalidPhpsData(): void
    {
        set_error_handler(static function (int $errno, string $errstr): void {
            // ignore E_NOTICE or E_WARNING from unserialize() to check that we throw an exception
        }, version_compare(PHP_VERSION, '8.3.0', '>=') ? \E_WARNING : \E_NOTICE);

        $this->query->setResponseWriter($this->query::WT_PHPS);

        $data = 'invalid';
        $this->response = new Response($data, $this->headers);
        $this->result = new Result($this->query, $this->response);

        $this->expectException(UnexpectedValueException::class);
        $this->result->getData();

        restore_error_handler();
    }

    public function testJsonSerialize(): void
    {
        $this->assertJsonStringEqualsJsonString($this->data, json_encode($this->result));
    }
}

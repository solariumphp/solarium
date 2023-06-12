<?php

namespace Solarium\Tests\QueryType\Ping;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\Ping\Query;
use Solarium\QueryType\Ping\ResponseParser;
use Solarium\QueryType\Ping\Result;

class ResponseParserTest extends TestCase
{
    public function testParse()
    {
        $data = '{"status":"OK"}';

        $response = new Response($data, ['HTTP 1.1 200 OK']);
        $result = new Result(new Query(), $response);
        $parser = new ResponseParser();
        $parsed = $parser->parse($result);

        $this->assertSame('OK', $parsed['status']);
    }
}

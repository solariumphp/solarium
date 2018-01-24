<?php

namespace Solarium\Tests\QueryType\Update;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Update\ResponseParser;
use Solarium\QueryType\Update\Result;

class ResponseParserTest extends TestCase
{
    public function testParse()
    {
        $data = '{"responseHeader" : {"status":1,"QTime":15}}';

        $response = new Response($data, ['HTTP 1.1 200 OK']);
        $result = new Result(new SelectQuery(), $response);
        $parser = new ResponseParser();
        $parsed = $parser->parse($result);

        $this->assertSame(1, $parsed['status']);
        $this->assertSame(15, $parsed['queryTime']);
    }
}

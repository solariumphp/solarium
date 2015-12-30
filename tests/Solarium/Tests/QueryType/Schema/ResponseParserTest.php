<?php

namespace Solarium\Tests\QueryType\Schema;

use Solarium\Core\Client\Response;
use Solarium\QueryType\Schema\ResponseParser;
use Solarium\QueryType\Schema\Result;
use Solarium\QueryType\Schema\Query\Query as SchemaQuery;

class ResponseParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $data = '{"responseHeader" : {"status":1,"QTime":25}}';

        $response = new Response($data, array('HTTP 1.1 200 OK'));
        $result = new Result(null, new SchemaQuery(), $response);
        $parser = new ResponseParser();
        $parsed = $parser->parse($result);

        $this->assertEquals(1, $parsed['status']);
        $this->assertEquals(25, $parsed['queryTime']);
    }
}

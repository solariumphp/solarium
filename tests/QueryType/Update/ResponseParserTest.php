<?php

namespace Solarium\Tests\QueryType\Update;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\Update\Query\Query;
use Solarium\QueryType\Update\ResponseParser;
use Solarium\QueryType\Update\Result;

class ResponseParserTest extends TestCase
{
    public function testParse(): void
    {
        $data = '{}';

        $response = new Response($data, ['HTTP/1.1 200 OK']);
        $result = new Result(new Query(), $response);
        $parser = new ResponseParser();
        $parsed = $parser->parse($result);

        $this->assertSame([], $parsed);
    }
}

<?php

namespace Solarium\Tests\QueryType\Server\Api;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\Server\Api\Query;
use Solarium\QueryType\Server\Api\ResponseParser;
use Solarium\QueryType\Server\Api\Result;

class ResponseParserTest extends TestCase
{
    public function testParse()
    {
        $data = <<<'EOT'
            {
                "responseHeader":{
                    "status":0,
                    "QTime":13
                },
                "WARNING":"This response format is experimental.  It is likely to change in the future.",
                "data":{
                    "foo":"bar"
                }
            }
        EOT;

        $response = new Response($data, ['HTTP 1.1 200 OK']);
        $result = new Result(new Query(), $response);
        $parser = new ResponseParser();
        $parsed = $parser->parse($result);

        $expected = 'This response format is experimental.  It is likely to change in the future.';

        $this->assertSame($expected, $parsed['WARNING']);

        $expected = [
            'foo' => 'bar',
        ];

        $this->assertSame($expected, $parsed['data']);
    }
}

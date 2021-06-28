<?php

namespace Solarium\Tests\QueryType\ManagedResources\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\ManagedResources\Query\Stopwords as Query;
use Solarium\QueryType\ManagedResources\ResponseParser\Command as ResponseParser;
use Solarium\QueryType\ManagedResources\Result\Command as CommandResult;

class CommandTest extends TestCase
{
    public function testParse()
    {
        $data = '{ "responseHeader":{ "status":0, "QTime":1}}';

        $query = new Query();
        $result = new CommandResult($query, new Response($data, ['HTTP/1.1 200 OK']));

        $parser = new ResponseParser();

        $parsed = $parser->parse($result);

        $this->assertTrue($parsed['wasSuccessful']);
        $this->assertSame('OK', $parsed['statusMessage']);
    }
}

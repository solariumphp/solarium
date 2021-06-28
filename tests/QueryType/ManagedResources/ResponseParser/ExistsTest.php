<?php

namespace Solarium\Tests\QueryType\ManagedResources\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\ManagedResources\Query\Stopwords as Query;
use Solarium\QueryType\ManagedResources\ResponseParser\Exists as ResponseParser;
use Solarium\QueryType\ManagedResources\Result\Command as CommandResult;

class ExistsTest extends TestCase
{
    public function testParseSuccessfulResult()
    {
        $query = new Query();
        $result = new CommandResult($query, new Response('', ['HTTP/1.1 200 OK']));

        $parser = new ResponseParser();

        $parsed = $parser->parse($result);

        $this->assertTrue($parsed['wasSuccessful']);
        $this->assertSame('OK', $parsed['statusMessage']);
    }

    public function testParseUnsuccessfulResult()
    {
        $query = new Query();
        $result = new CommandResult($query, new Response('', ['HTTP/1.1 404 Not Found']));

        $parser = new ResponseParser();

        $parsed = $parser->parse($result);

        $this->assertFalse($parsed['wasSuccessful']);
        $this->assertSame('Not Found', $parsed['statusMessage']);
    }
}

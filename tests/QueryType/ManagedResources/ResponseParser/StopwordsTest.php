<?php

namespace Solarium\Tests\QueryType\ManagedResources\ResponseParser;

use Solarium\QueryType\ManagedResources\ResponseParser\Stopwords as ResponseParser;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\ManagedResources\Query\Stopwords as StopwordsQuery;
use Solarium\QueryType\ManagedResources\Result\Stopwords;

class StopwordsTest extends TestCase
{
    public function testParse()
    {
        $data = '{ "responseHeader":{ "status":0, "QTime":1 }, "wordSet":{ "initArgs":{"ignoreCase":true}, "initializedOn":"2014-03-28T20:53:53.058Z", "managedList":[ "a", "an", "and", "are" ]}}';

        $response = new Response($data, ['HTTP 1.1 200 OK']);
        $result = new Stopwords(new StopwordsQuery(), $response);
        $parser = new ResponseParser();
        $parsed = $parser->parse($result);

        $this->assertSame('200 OK', $result->getResponse()->getStatusMessage());
        $this->assertSame(0, $parsed['status']);
        $this->assertSame(1, $parsed['queryTime']);
        $this->assertSame('2014-03-28T20:53:53.058Z', $parsed['items']->getInitializedOn());
        $this->assertSame(true, $parsed['items']->isIgnoreCase());
    }
}

<?php

namespace Solarium\Tests\QueryType\ManagedResources\ResponseParser;

use Solarium\QueryType\ManagedResources\ResponseParser\Stopwords as ResponseParser;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\ManagedResources\Query\Stopwords as StopwordsQuery;
use Solarium\QueryType\ManagedResources\Result\Stopwords\WordSet;

class StopwordsTest extends TestCase
{
    public function testParse()
    {
        $data = '{ "responseHeader":{ "status":0, "QTime":1 }, "wordSet":{ "initArgs":{"ignoreCase":true}, "initializedOn":"2014-03-28T20:53:53.058Z", "managedList":[ "a", "an", "and", "are" ]}}';

        $query = new StopwordsQuery();
        $result = new WordSet($query, new Response($data, ['HTTP 1.1 200 OK']));

        $parser = new ResponseParser();

        $parsed = $parser->parse($result);

        $this->assertSame('2014-03-28T20:53:53.058Z', $parsed['initializedOn']);
        $this->assertTrue($parsed['ignoreCase']);
        $this->assertEquals([0 => 'a', 1 => 'an', 2 => 'and', 3 => 'are'], $parsed['items']);
    }
}

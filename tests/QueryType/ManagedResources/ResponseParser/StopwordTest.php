<?php

namespace Solarium\Tests\QueryType\ManagedResources\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\ManagedResources\Query\Stopwords as StopwordsQuery;
use Solarium\QueryType\ManagedResources\ResponseParser\Stopword as ResponseParser;
use Solarium\QueryType\ManagedResources\Result\Stopwords\WordSet;

class StopwordTest extends TestCase
{
    public function testParse()
    {
        $data = '{ "responseHeader":{ "status":0, "QTime":1}, "such":"such"}';

        $query = new StopwordsQuery();
        $result = new WordSet($query, new Response($data, ['HTTP/1.1 200 OK']));

        $parser = new ResponseParser();

        $parsed = $parser->parse($result);

        $this->assertEquals(['such'], $parsed['items']);
        $this->assertTrue($parsed['wasSuccessful']);
        $this->assertSame('OK', $parsed['statusMessage']);
    }
}

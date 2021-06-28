<?php

namespace Solarium\Tests\QueryType\ManagedResources\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\ManagedResources\Query\Synonyms as SynonymsQuery;
use Solarium\QueryType\ManagedResources\ResponseParser\Synonym as ResponseParser;
use Solarium\QueryType\ManagedResources\Result\Synonyms\Synonyms;
use Solarium\QueryType\ManagedResources\Result\Synonyms\SynonymMappings;

class SynonymTest extends TestCase
{
    public function testParse()
    {
        $data = '{ "responseHeader":{ "status":0, "QTime":1}, "happy":["glad", "joyful"]}';

        $query = new SynonymsQuery();
        $result = new SynonymMappings($query, new Response($data, ['HTTP/1.1 200 OK']));
        $parser = new ResponseParser();

        $parsed = $parser->parse($result);

        $synonyms =
        [
            0 => new Synonyms(
                'happy',
                [
                        0 => 'glad',
                        1 => 'joyful',
                ]
            ),
        ];

        $this->assertEquals($synonyms, $parsed['items']);
        $this->assertTrue($parsed['wasSuccessful']);
        $this->assertSame('OK', $parsed['statusMessage']);
    }
}

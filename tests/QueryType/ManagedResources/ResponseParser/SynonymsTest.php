<?php

namespace Solarium\Tests\QueryType\ManagedResources\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\ManagedResources\Query\Synonyms as SynonymsQuery;
use Solarium\QueryType\ManagedResources\ResponseParser\Synonyms as ResponseParser;
use Solarium\QueryType\ManagedResources\Result\Synonyms\Synonyms;
use Solarium\QueryType\ManagedResources\Result\Synonyms\SynonymMappings;

class SynonymsTest extends TestCase
{
    public function testParse()
    {
        $data = '{ "responseHeader":{ "status":0, "QTime":3}, "synonymMappings":{ "initArgs":{ "ignoreCase":true, "format":"solr"}, "initializedOn":"2014-12-16T22:44:05.33Z", "updatedSinceInit":"2020-02-03T00:54:53.049Z", "managedMap":{ "GB": ["GiB", "Gigabyte"], "TV": ["Television"], "happy": ["glad", "joyful"]}}}';

        $query = new SynonymsQuery();
        $result = new SynonymMappings($query, new Response($data, ['HTTP/1.1 200 OK']));
        $parser = new ResponseParser();

        $parsed = $parser->parse($result);

        $this->assertSame('2014-12-16T22:44:05.33Z', $parsed['initializedOn']);
        $this->assertSame('2020-02-03T00:54:53.049Z', $parsed['updatedSinceInit']);
        $this->assertTrue($parsed['ignoreCase']);
        $this->assertSame('solr', $parsed['format']);
        $this->assertTrue($parsed['wasSuccessful']);
        $this->assertSame('OK', $parsed['statusMessage']);

        $synonyms =
        [
            0 => new Synonyms(
                'GB',
                [
                    0 => 'GiB',
                    1 => 'Gigabyte',
                ]
            ),
            1 => new Synonyms(
                'TV',
                [
                    0 => 'Television',
                ]
            ),
            2 => new Synonyms(
                'happy',
                [
                        0 => 'glad',
                        1 => 'joyful',
                ]
            ),
        ];

        $this->assertEquals($synonyms, $parsed['items']);
    }
}

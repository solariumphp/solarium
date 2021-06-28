<?php

namespace Solarium\Tests\QueryType\ManagedResources\Result\Synonyms;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\ManagedResources\Query\Synonyms as SynonymsQuery;
use Solarium\QueryType\ManagedResources\Result\Synonyms\SynonymMappings;
use Solarium\QueryType\ManagedResources\Result\Synonyms\Synonyms;

class SynonymMappingsTest extends TestCase
{
    /** @var SynonymMappings */
    protected $synonymMappings;

    public function setUp(): void
    {
        $data = '{ "responseHeader":{ "status":0, "QTime":3}, "synonymMappings":{ "initArgs":{ "ignoreCase":true, "format":"solr"}, "initializedOn":"2014-12-16T22:44:05.33Z", "updatedSinceInit":"2020-02-03T00:54:53.049Z", "managedMap":{ "GB": ["GiB", "Gigabyte"], "TV": ["Television"], "happy": ["glad", "joyful"]}}}';

        $query = new SynonymsQuery();
        $response = new Response($data, ['HTTP/1.1 200 OK']);
        $this->synonymMappings = new SynonymMappings($query, $response);
    }

    public function testGetName()
    {
        $this->assertSame('synonymMappings', $this->synonymMappings->getName());
    }

    public function testGetItems()
    {
        $items = [
            0 => new Synonyms('GB', ['GiB', 'Gigabyte']),
            1 => new Synonyms('TV', ['Television']),
            2 => new Synonyms('happy', ['glad', 'joyful']),
        ];

        $this->assertEquals($items, $this->synonymMappings->getItems());
    }

    public function testGetIterator()
    {
        $items = [
            0 => new Synonyms('GB', ['GiB', 'Gigabyte']),
            1 => new Synonyms('TV', ['Television']),
            2 => new Synonyms('happy', ['glad', 'joyful']),
        ];

        foreach ($this->synonymMappings as $key => $value) {
            $this->assertEquals($items[$key], $value);
        }
    }

    public function testCount()
    {
        $this->assertCount(3, $this->synonymMappings);
    }

    public function testIsIgnoreCase()
    {
        $this->assertTrue($this->synonymMappings->isIgnoreCase());
    }

    public function testGetFormat()
    {
        $this->assertSame('solr', $this->synonymMappings->getFormat());
    }

    public function testGetInitializedOn()
    {
        $this->assertSame('2014-12-16T22:44:05.33Z', $this->synonymMappings->getInitializedOn());
    }

    public function testUpdatedSinceInit()
    {
        $this->assertSame('2020-02-03T00:54:53.049Z', $this->synonymMappings->getUpdatedSinceInit());
    }

    public function testGetWasSuccessful()
    {
        $this->assertTrue($this->synonymMappings->getWasSuccessful());
    }

    public function testGetStatusMessage()
    {
        $this->assertSame('OK', $this->synonymMappings->getStatusMessage());
    }
}

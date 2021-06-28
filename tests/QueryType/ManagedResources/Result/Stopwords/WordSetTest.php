<?php

namespace Solarium\Tests\QueryType\ManagedResources\Result\Stopwords;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Response;
use Solarium\QueryType\ManagedResources\Query\Stopwords as StopwordsQuery;
use Solarium\QueryType\ManagedResources\Result\Stopwords\WordSet;

class WordSetTest extends TestCase
{
    /** @var WordSet */
    protected $wordSet;

    public function setUp(): void
    {
        $data = '{ "responseHeader":{ "status":0, "QTime":1 }, "wordSet":{ "initArgs":{"ignoreCase":true}, "initializedOn":"2014-03-28T20:53:53.058Z", "updatedSinceInit":"2020-02-03T15:00:25.558Z", "managedList":[ "a", "an", "and", "are" ]}}';

        $query = new StopwordsQuery();
        $response = new Response($data, ['HTTP/1.1 200 OK']);
        $this->wordSet = new WordSet($query, $response);
    }

    public function testGetName()
    {
        $this->assertSame('wordSet', $this->wordSet->getName());
    }

    public function testGetItems()
    {
        $items = [
            0 => 'a',
            1 => 'an',
            2 => 'and',
            3 => 'are',
        ];

        $this->assertSame($items, $this->wordSet->getItems());
    }

    public function testGetIterator()
    {
        $items = [
            0 => 'a',
            1 => 'an',
            2 => 'and',
            3 => 'are',
        ];

        foreach ($this->wordSet as $key => $value) {
            $this->assertSame($items[$key], $value);
        }
    }

    public function testCount()
    {
        $this->assertCount(4, $this->wordSet);
    }

    public function testIsIgnoreCase()
    {
        $this->assertTrue($this->wordSet->isIgnoreCase());
    }

    public function testGetInitializedOn()
    {
        $this->assertSame('2014-03-28T20:53:53.058Z', $this->wordSet->getInitializedOn());
    }

    public function testUpdatedSinceInit()
    {
        $this->assertSame('2020-02-03T15:00:25.558Z', $this->wordSet->getUpdatedSinceInit());
    }

    public function testGetWasSuccessful()
    {
        $this->assertTrue($this->wordSet->getWasSuccessful());
    }

    public function testGetStatusMessage()
    {
        $this->assertSame('OK', $this->wordSet->getStatusMessage());
    }
}

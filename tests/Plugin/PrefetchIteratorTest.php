<?php

namespace Solarium\Tests\Plugin;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Plugin\PrefetchIterator;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\Result\Document;
use Solarium\QueryType\Select\Result\Result;
use Solarium\Tests\Integration\TestClientFactory;

class PrefetchIteratorTest extends TestCase
{
    /**
     * @var PrefetchIterator
     */
    protected $plugin;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Query
     */
    protected $query;

    /**
     * @var Document[]
     */
    protected $documents;

    public function setUp(): void
    {
        $this->plugin = new PrefetchIterator();

        $this->client = TestClientFactory::createWithCurlAdapter();
        $this->query = $this->client->createSelect();

        $this->documents = [
            new Document(['id' => 1, 'title' => 'doc1']),
            new Document(['id' => 2, 'title' => 'doc2']),
            new Document(['id' => 3, 'title' => 'doc3']),
            new Document(['id' => 4, 'title' => 'doc4']),
            new Document(['id' => 5, 'title' => 'doc5']),
        ];
    }

    public function testInitPlugin()
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $plugin = $client->getPlugin('prefetchiterator');

        $this->assertInstanceOf(PrefetchIterator::class, $plugin);
    }

    public function testSetAndGetPrefetch()
    {
        $this->plugin->setPrefetch(120);
        $this->assertSame(120, $this->plugin->getPrefetch());
    }

    public function testSetAndGetQuery()
    {
        $this->plugin->setQuery($this->query);
        $this->assertSame($this->query, $this->plugin->getQuery());
    }

    public function testCount()
    {
        $result = $this->getPartialResult(0, 2);
        $mockClient = $this->createMock(Client::class);
        // the query should be executed only once to get numFound
        $mockClient->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo($this->query), $this->equalTo(null))
                   ->willReturn($result);

        $this->plugin->initPlugin($mockClient, []);
        $this->plugin->setPrefetch(2);
        $this->plugin->setQuery($this->query);
        $this->assertCount(5, $this->plugin);
    }

    public function testIteratorFlow()
    {
        $resultSets = [
            $this->getPartialResult(0, 2),
            $this->getPartialResult(2, 2),
            $this->getPartialResult(4, 2),
            $this->getPartialResult(0, 2),
            $this->getPartialResult(2, 2),
            $this->getPartialResult(0, 2),
        ];

        $mockClient = $this->createMock(Client::class);
        // the query should be executed 3 times for a full iteration because 3 === (int) ceil($numFound = 5 / $prefetch = 2)
        // + 2 times to iterate through 1.5 sets after the first rewind()
        // + 1 time to start iterating through the first set after the second rewind()
        $mockClient->expects($this->exactly(6))
                   ->method('execute')
                   ->with($this->equalTo($this->query), $this->equalTo(null))
                   ->willReturnOnConsecutiveCalls(...$resultSets);

        $this->plugin->initPlugin($mockClient, []);
        $this->plugin->setPrefetch(2);
        $this->plugin->setQuery($this->query);

        // run through the entire iterator manually
        $this->assertTrue($this->plugin->valid());
        $this->assertSame(['id' => 1, 'title' => 'doc1'], $this->plugin->current()->getFields());
        $this->assertSame(0, $this->plugin->key());
        $this->plugin->next();
        $this->assertTrue($this->plugin->valid());
        $this->assertSame(['id' => 2, 'title' => 'doc2'], $this->plugin->current()->getFields());
        $this->assertSame(1, $this->plugin->key());
        $this->plugin->next();
        $this->assertTrue($this->plugin->valid());
        $this->assertSame(['id' => 3, 'title' => 'doc3'], $this->plugin->current()->getFields());
        $this->assertSame(2, $this->plugin->key());
        $this->plugin->next();
        $this->assertTrue($this->plugin->valid());
        $this->assertSame(['id' => 4, 'title' => 'doc4'], $this->plugin->current()->getFields());
        $this->assertSame(3, $this->plugin->key());
        $this->plugin->next();
        $this->assertTrue($this->plugin->valid());
        $this->assertSame(['id' => 5, 'title' => 'doc5'], $this->plugin->current()->getFields());
        $this->assertSame(4, $this->plugin->key());
        $this->plugin->next();
        $this->assertFalse($this->plugin->valid());

        // rewind at the end and partway through a fetched set
        $this->plugin->rewind();
        $this->assertTrue($this->plugin->valid());
        $this->assertSame(['id' => 1, 'title' => 'doc1'], $this->plugin->current()->getFields());
        $this->assertSame(0, $this->plugin->key());
        $this->plugin->next();
        $this->assertTrue($this->plugin->valid());
        $this->assertSame(['id' => 2, 'title' => 'doc2'], $this->plugin->current()->getFields());
        $this->assertSame(1, $this->plugin->key());
        $this->plugin->next();
        $this->assertTrue($this->plugin->valid());
        $this->assertSame(['id' => 3, 'title' => 'doc3'], $this->plugin->current()->getFields());
        $this->assertSame(2, $this->plugin->key());
        $this->plugin->rewind();
        $this->assertTrue($this->plugin->valid());
        $this->assertSame(['id' => 1, 'title' => 'doc1'], $this->plugin->current()->getFields());
        $this->assertSame(0, $this->plugin->key());
    }

    public function testIteratorEmptyResultFlow()
    {
        $result = $this->getEmptyResult();
        $mockClient = $this->createMock(Client::class);
        // the query should be executed only once because there is nothing else to fetch
        $mockClient->expects($this->exactly(1))
                   ->method('execute')
                   ->willReturn($result);

        $this->plugin->initPlugin($mockClient, []);
        $this->plugin->setQuery($this->query);

        // there is nothing to run through
        $this->assertFalse($this->plugin->valid());
        $this->plugin->rewind();
        $this->assertFalse($this->plugin->valid());
    }

    public function testIterator()
    {
        $resultSets = [
            $this->getPartialResult(0, 2),
            $this->getPartialResult(2, 2),
            $this->getPartialResult(4, 2),
        ];

        $mockQuery = $this->createMock(Query::class);
        $mockQuery->method('setRows')
                  ->with($this->equalTo(2))
                  ->willReturnSelf();
        // every time the query is executed
        $mockQuery->expects($this->exactly(3))
                  ->method('setStart')
                  ->with(
                      $this->callback(function (int $start): bool {
                          static $i = 0;
                          static $starts = [0, 2, 4];

                          return $starts[$i++] === $start;
                      })
                  )
                  ->willReturnSelf();
        $mockQuery->expects($this->never())
                  ->method('setCursorMark')
                  ->willReturnSelf();

        $mockClient = $this->createMock(Client::class);
        // the query should be executed 3 times for a full iteration because 3 === intdiv($numFound = 5 / $prefetch = 2) + 1
        $mockClient->expects($this->exactly(3))
                   ->method('execute')
                   ->with($this->equalTo($mockQuery), $this->equalTo(null))
                   ->willReturnOnConsecutiveCalls(...$resultSets);

        $this->plugin->initPlugin($mockClient, []);
        $this->plugin->setPrefetch(2);
        $this->plugin->setQuery($mockQuery);

        $results = [];
        foreach ($this->plugin as $doc) {
            $results[] = $doc;
        }

        $this->assertCount(5, $this->plugin);
        $this->assertSame($this->documents, $results);
    }

    public function testIteratorWithCursorMark()
    {
        $resultSets = [
            $this->getPartialResult(0, 2)->setNextCursorMark('AoEhMg=='),
            $this->getPartialResult(2, 2)->setNextCursorMark('AoEhNA=='),
            $this->getPartialResult(4, 2)->setNextCursorMark('AoEhNQ=='),
        ];

        $mockQuery = $this->createMock(Query::class);
        $mockQuery->method('setRows')
                  ->with($this->equalTo(2))
                  ->willReturnSelf();
        $mockQuery->expects($this->never())
                  ->method('setStart')
                  ->willReturnSelf();
        // initial manual setCursorMark('*') + every time the query is executed
        $mockQuery->expects($this->exactly(4))
                  ->method('setCursorMark')
                  ->with(
                      $this->callback(function (string $cursorMark): bool {
                          static $i = 0;
                          static $cursorMarks = ['*', '*', 'AoEhMg==', 'AoEhNA=='];

                          return $cursorMarks[$i++] === $cursorMark;
                      })
                  )
                  ->willReturnSelf();
        // won't be '*' on consecutive calls, only matters that it isn't NULL for this test
        $mockQuery->method('getCursorMark')
                  ->willReturn('*');

        $mockClient = $this->createMock(Client::class);
        // the query should be executed 3 times for a full iteration because 3 === intdiv($numFound = 5 / $prefetch = 2) + 1
        $mockClient->expects($this->exactly(3))
                   ->method('execute')
                   ->with($this->equalTo($mockQuery), $this->equalTo(null))
                   ->willReturnOnConsecutiveCalls(...$resultSets);

        $mockQuery->setCursorMark('*');

        $this->plugin->initPlugin($mockClient, []);
        $this->plugin->setPrefetch(2);
        $this->plugin->setQuery($mockQuery);

        $results = [];
        foreach ($this->plugin as $doc) {
            $results[] = $doc;
        }

        $this->assertCount(5, $this->plugin);
        $this->assertSame($this->documents, $results);
    }

    public function testIteratorExactMultiple()
    {
        // add an extra document so we hit a multiple of prefetch size 2
        $this->documents[] = new Document(['id' => 6, 'title' => 'doc6']);

        $resultSets = [
            $this->getPartialResult(0, 2),
            $this->getPartialResult(2, 2),
            $this->getPartialResult(4, 2),
            // an empty resultset is returned if you try to page beyond numFound
            $this->getEmptyResult()->setNumFound(6),
        ];

        $mockQuery = $this->createMock(Query::class);
        $mockQuery->method('setRows')
                  ->with($this->equalTo(2))
                  ->willReturnSelf();
        // every time the query is executed
        $mockQuery->expects($this->exactly(4))
                  ->method('setStart')
                  ->with(
                      $this->callback(function (int $start): bool {
                          static $i = 0;
                          static $starts = [0, 2, 4, 6];

                          return $starts[$i++] === $start;
                      })
                  )
                  ->willReturnSelf();
        $mockQuery->expects($this->never())
                  ->method('setCursorMark')
                  ->willReturnSelf();

        $mockClient = $this->createMock(Client::class);
        // the query should be executed 4 times for a full iteration because 4 === intdiv($numFound = 6 / $prefetch = 2) + 1
        $mockClient->expects($this->exactly(4))
                   ->method('execute')
                   ->with($this->equalTo($mockQuery), $this->equalTo(null))
                   ->willReturnOnConsecutiveCalls(...$resultSets);

        $this->plugin->initPlugin($mockClient, []);
        $this->plugin->setPrefetch(2);
        $this->plugin->setQuery($mockQuery);

        $results = [];
        foreach ($this->plugin as $doc) {
            $results[] = $doc;
        }

        $this->assertCount(6, $this->plugin);
        $this->assertSame($this->documents, $results);
    }

    public function testIteratorExactMultipleWithCursorMark()
    {
        // add an extra document so we hit a multiple of prefetch size 2
        $this->documents[] = new Document(['id' => 6, 'title' => 'doc6']);

        $resultSets = [
            $this->getPartialResult(0, 2)->setNextCursorMark('AoEhMg=='),
            $this->getPartialResult(2, 2)->setNextCursorMark('AoEhNA=='),
            $this->getPartialResult(4, 2)->setNextCursorMark('AoEhNg=='),
            // last cursorMark is repeated by Solr if there are no further results
            $this->getEmptyResult()->setNumFound(6)->setNextCursorMark('AoEhNg=='),
        ];

        $mockQuery = $this->createMock(Query::class);
        $mockQuery->method('setRows')
                  ->with($this->equalTo(2))
                  ->willReturnSelf();
        $mockQuery->expects($this->never())
                  ->method('setStart')
                  ->willReturnSelf();
        // initial manual setCursorMark('*') + every time the query is executed
        $mockQuery->expects($this->exactly(5))
                  ->method('setCursorMark')
                  ->with(
                      $this->callback(function (string $cursorMark): bool {
                          static $i = 0;
                          static $cursorMarks = ['*', '*', 'AoEhMg==', 'AoEhNA==', 'AoEhNg=='];

                          return $cursorMarks[$i++] === $cursorMark;
                      })
                  )
                  ->willReturnSelf();
        // won't be '*' on consecutive calls, only matters that it isn't NULL for this test
        $mockQuery->method('getCursorMark')
                  ->willReturn('*');

        $mockClient = $this->createMock(Client::class);
        // the query should be executed 4 times for a full iteration because 4 === intdiv($numFound = 6 / $prefetch = 2) + 1
        $mockClient->expects($this->exactly(4))
                   ->method('execute')
                   ->with($this->equalTo($mockQuery), $this->equalTo(null))
                   ->willReturnOnConsecutiveCalls(...$resultSets);

        $mockQuery->setCursorMark('*');

        $this->plugin->initPlugin($mockClient, []);
        $this->plugin->setPrefetch(2);
        $this->plugin->setQuery($mockQuery);

        $results = [];
        foreach ($this->plugin as $doc) {
            $results[] = $doc;
        }

        $this->assertCount(6, $this->plugin);
        $this->assertSame($this->documents, $results);
    }

    public function testIteratorEmptyResult()
    {
        $result = $this->getEmptyResult();

        $mockClient = $this->createMock(Client::class);
        // the query should be executed only once because there is nothing else to fetch
        $mockClient->expects($this->exactly(1))
                   ->method('execute')
                   ->willReturn($result);

        $this->plugin->initPlugin($mockClient, []);
        $this->plugin->setQuery($this->query);

        $results = [];
        foreach ($this->plugin as $doc) {
            $results[] = $doc;
        }

        $this->assertCount(0, $this->plugin);
        $this->assertSame([], $results);
    }

    public function testIteratorEmptyResultWithCursorMark()
    {
        $result = $this->getEmptyResult()->setNextCursorMark('*');

        $mockClient = $this->createMock(Client::class);
        // the query should be executed only once because there is nothing else to fetch
        $mockClient->expects($this->exactly(1))
                   ->method('execute')
                   ->willReturn($result);

        $this->query->setCursorMark('*');

        $this->plugin->initPlugin($mockClient, []);
        $this->plugin->setQuery($this->query);

        $results = [];
        foreach ($this->plugin as $doc) {
            $results[] = $doc;
        }

        $this->assertCount(0, $this->plugin);
        $this->assertSame([], $results);
    }

    public function testIteratorAndRewind()
    {
        $resultSets = [
            $this->getPartialResult(0, 3),
            $this->getPartialResult(3, 3),
        ];

        $mockQuery = $this->createMock(Query::class);
        $mockQuery->method('setRows')
                  ->with($this->equalTo(3))
                  ->willReturnSelf();
        // every time the query is executed
        $mockQuery->expects($this->exactly(4))
                  ->method('setStart')
                  ->with(
                      $this->callback(function (int $start): bool {
                          static $i = 0;
                          static $starts = [0, 3, 0, 3];

                          return $starts[$i++] === $start;
                      })
                  )
                  ->willReturnSelf();
        $mockQuery->expects($this->never())
                  ->method('setCursorMark')
                  ->willReturnSelf();

        $mockClient = $this->createMock(Client::class);
        // the query should be executed 2 times for a full iteration because 2 === intdiv($numFound = 5 / $prefetch = 3) + 1
        // + 2 times for another full iteration after the rewind() invoked by the second foreach
        $mockClient->expects($this->exactly(4))
                   ->method('execute')
                   ->with($this->equalTo($mockQuery), $this->equalTo(null))
                   ->willReturnOnConsecutiveCalls(...$resultSets, ...$resultSets);

        $this->plugin->initPlugin($mockClient, []);
        $this->plugin->setPrefetch(3);
        $this->plugin->setQuery($mockQuery);

        $results1 = [];
        foreach ($this->plugin as $doc) {
            $results1[] = $doc;
        }

        // the second foreach will trigger a rewind, this time include keys
        $results2 = [];
        foreach ($this->plugin as $key => $doc) {
            $results2[$key] = $doc;
        }

        $this->assertSame($this->documents, $results1);
        $this->assertSame($this->documents, $results2);
    }

    public function testIteratorAndRewindWithCursorMark()
    {
        $resultSets = [
            $this->getPartialResult(0, 3)->setNextCursorMark('AoEhMw=='),
            $this->getPartialResult(3, 3)->setNextCursorMark('AoEhNQ=='),
        ];

        $mockQuery = $this->createMock(Query::class);
        $mockQuery->method('setRows')
                  ->with($this->equalTo(3))
                  ->willReturnSelf();
        $mockQuery->expects($this->never())
                  ->method('setStart')
                  ->willReturnSelf();
        // initial manual setCursorMark('*') + every time the query is executed
        $mockQuery->expects($this->exactly(5))
                  ->method('setCursorMark')
                  ->with(
                      $this->callback(function (string $cursorMark): bool {
                          static $i = 0;
                          static $cursorMarks = ['*', '*', 'AoEhMw==', '*', 'AoEhMw=='];

                          return $cursorMarks[$i++] === $cursorMark;
                      })
                  )
                  ->willReturnSelf();
        // won't be '*' on consecutive calls, only matters that it isn't NULL for this test
        $mockQuery->method('getCursorMark')
                  ->willReturn('*');

        $mockClient = $this->createMock(Client::class);
        // the query should be executed 2 times for a full iteration because 2 === intdiv($numFound = 5 / $prefetch = 3) + 1
        // + 2 times for another full iteration after the rewind() invoked by the second foreach
        $mockClient->expects($this->exactly(4))
                   ->method('execute')
                   ->with($this->equalTo($mockQuery), $this->equalTo(null))
                   ->willReturnOnConsecutiveCalls(...$resultSets, ...$resultSets);

        $mockQuery->setCursorMark('*');

        $this->plugin->initPlugin($mockClient, []);
        $this->plugin->setPrefetch(3);
        $this->plugin->setQuery($mockQuery);

        $results1 = [];
        foreach ($this->plugin as $doc) {
            $results1[] = $doc;
        }

        // the second foreach will trigger a rewind, this time include keys
        $results2 = [];
        foreach ($this->plugin as $key => $doc) {
            $results2[$key] = $doc;
        }

        $this->assertSame($this->documents, $results1);
        $this->assertSame($this->documents, $results2);
    }

    public function testPrefetchTrumpsStartAndRows()
    {
        $result = $this->getEmptyResult();

        $mockClient = $this->createMock(Client::class);
        $mockClient->method('execute')
                   ->willReturn($result);

        $this->query->setStart(12);
        $this->query->setRows(2);

        $this->plugin->initPlugin($mockClient, []);
        $this->plugin->setPrefetch(3);
        $this->plugin->setQuery($this->query);

        // trigger a fetch
        $this->plugin->valid();

        $this->assertSame(0, $this->query->getStart());
        $this->assertSame(3, $this->query->getRows());
    }

    public function testPrefetchTrumpsRowsWithCursorMark()
    {
        $result = $this->getEmptyResult();

        $mockClient = $this->createMock(Client::class);
        $mockClient->method('execute')
                   ->willReturn($result);

        $this->query->setCursorMark('*');
        $this->query->setRows(2);

        $this->plugin->initPlugin($mockClient, []);
        $this->plugin->setPrefetch(3);
        $this->plugin->setQuery($this->query);

        // trigger a fetch
        $this->plugin->valid();

        $this->assertSame(3, $this->query->getRows());
    }

    public function testIteratorDoesntResetOnCount()
    {
        $resultSets = [
            $this->getPartialResult(0, 2),
            $this->getPartialResult(2, 2),
            $this->getPartialResult(4, 2),
        ];

        $mockClient = $this->createMock(Client::class);
        // the query should be executed 1 time to get numFound, thereby fetching the first set
        // + 2 more times to fetch the remaining sets because 2 === (int) ceil(($numFound = 5 - $prefetch = 2) / $prefetch = 2)
        $mockClient->expects($this->exactly(3))
                   ->method('execute')
                   ->with($this->equalTo($this->query), $this->equalTo(null))
                   ->willReturnOnConsecutiveCalls(...$resultSets);

        $this->plugin->initPlugin($mockClient, []);
        $this->plugin->setPrefetch(2);
        $this->plugin->setQuery($this->query);

        $this->assertCount(5, $this->plugin);

        // foreach invokes a rewind(), but doesn't cause a re-fetch of the first set
        $results = [];
        foreach ($this->plugin as $doc) {
            $results[] = $doc;
        }

        $this->assertSame($this->documents, $results);
    }

    public function testIteratorResetOnSetPrefetch()
    {
        $resultSets = [
            $this->getPartialResult(0, 2),
            $this->getPartialResult(0, 3),
            $this->getPartialResult(3, 3),
        ];

        $mockClient = $this->createMock(Client::class);
        // the query should be executed 1 time to get numFound, thereby fetching the first set of 2
        // + 2 times for a full iteration in sets of 3 after the reset caused by setPrefetch()
        $mockClient->expects($this->exactly(3))
                   ->method('execute')
                   ->with($this->equalTo($this->query), $this->equalTo(null))
                   ->willReturnOnConsecutiveCalls(...$resultSets);

        $this->plugin->initPlugin($mockClient, []);
        $this->plugin->setPrefetch(2);
        $this->plugin->setQuery($this->query);

        $this->assertCount(5, $this->plugin);

        // this should trigger a reset and the foreach will cause a second query execution
        $this->plugin->setPrefetch(3);

        $results = [];
        foreach ($this->plugin as $doc) {
            $results[] = $doc;
        }

        $this->assertSame($this->documents, $results);
    }

    public function testIteratorResetOnSetQuery()
    {
        $resultSets = [
            $this->getPartialResult(0, 3),
            $this->getPartialResult(0, 3),
            $this->getPartialResult(3, 3),
        ];

        $mockClient = $this->createMock(Client::class);
        // the query should be executed 1 time to get numFound, thereby fetching the first set
        // + 2 times for a full iteration after the reset caused by setQuery()
        $mockClient->expects($this->exactly(3))
                   ->method('execute')
                   ->with($this->equalTo($this->query), $this->equalTo(null))
                   ->willReturnOnConsecutiveCalls(...$resultSets);

        $this->plugin->initPlugin($mockClient, []);
        $this->plugin->setPrefetch(3);
        $this->plugin->setQuery($this->query);

        $this->assertCount(5, $this->plugin);

        // this should trigger a reset and the foreach will cause a second query execution
        $this->plugin->setQuery($this->query);

        $results = [];
        foreach ($this->plugin as $doc) {
            $results[] = $doc;
        }

        $this->assertSame($this->documents, $results);
    }

    public function testSetAndGetEndpointAsString()
    {
        $this->assertNull($this->plugin->getEndpoint());
        $this->plugin->setEndpoint('s1');
        $this->assertSame('s1', $this->plugin->getEndpoint());
    }

    public function testWithSpecificEndpoint()
    {
        $result = $this->getResult();
        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo($this->query), $this->equalTo('s2'))
                   ->willReturn($result);

        $this->plugin->initPlugin($mockClient, []);
        $this->plugin->setQuery($this->query)->setEndpoint('s2');
        $this->assertCount(5, $this->plugin);
    }

    public function testWithSpecificEndpointOption()
    {
        $result = $this->getResult();
        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->once())
                   ->method('execute')
                   ->with($this->equalTo($this->query), $this->equalTo('s3'))
                   ->willReturn($result);

        $this->plugin->initPlugin($mockClient, ['endpoint' => 's3']);
        $this->plugin->setQuery($this->query);
        $this->assertCount(5, $this->plugin);
    }

    public function getResult(): SelectResultDummy
    {
        $numFound = \count($this->documents);
        $docs = $this->documents;

        return new SelectResultDummy(1, 12, $numFound, $docs, []);
    }

    public function getPartialResult(int $offset, int $length): SelectResultDummy
    {
        $numFound = \count($this->documents);
        $docs = array_slice($this->documents, $offset, $length);

        return new SelectResultDummy(1, 12, $numFound, $docs, []);
    }

    public function getEmptyResult(): SelectResultDummy
    {
        $numFound = 0;
        $docs = [];

        return new SelectResultDummy(1, 2, $numFound, $docs, []);
    }
}

class SelectResultDummy extends Result
{
    protected $parsed = true;

    public function __construct($status, $queryTime, $numfound, $docs, $components)
    {
        $this->numfound = $numfound;
        $this->documents = $docs;
        $this->components = $components;
        $this->responseHeader = ['status' => $status, 'QTime' => $queryTime];
    }

    public function setNumFound(int $numfound): self
    {
        $this->numfound = $numfound;

        return $this;
    }

    public function setNextCursorMark(string $nextcursormark): self
    {
        $this->nextcursormark = $nextcursormark;

        return $this;
    }
}

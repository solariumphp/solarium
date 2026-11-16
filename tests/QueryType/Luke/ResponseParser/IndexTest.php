<?php

namespace Solarium\Tests\QueryType\Luke\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Luke\Query;
use Solarium\QueryType\Luke\ResponseParser\Index as ResponseParser;
use Solarium\QueryType\Luke\Result\Index\Index;
use Solarium\QueryType\Luke\Result\Result;
use Solarium\QueryType\Luke\Result\Shards\Shard;

class IndexTest extends TestCase
{
    use IndexDataTrait;

    public function testParseJson(): Index
    {
        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 3,
            ],
            'index' => $this->getIndexData(),
        ];

        $query = new Query();
        $query->setResponseWriter($query::WT_JSON);
        $query->setShow(Query::SHOW_INDEX);

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->any())
            ->method('getQuery')
            ->willReturn($query);
        $resultStub->expects($this->any())
            ->method('getData')
            ->willReturn($data);

        $parser = new ResponseParser();
        $result = $parser->parse($resultStub);

        $this->assertInstanceOf(Index::class, $result['indexResult']);
        $this->assertNull($result['schemaResult']);
        $this->assertNull($result['docResult']);
        $this->assertNull($result['fieldsResult']);
        $this->assertNull($result['infoResult']);
        $this->assertNull($result['shardsResult']);

        return $result['indexResult'];
    }

    /**
     * @depends testParseJson
     *
     * @deprecated Will be removed in Solarium 8
     */
    public function testParsePhps(Index $index): void
    {
        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 3,
            ],
            'index' => $this->getIndexData(),
        ];

        $query = new Query();
        @$query->setResponseWriter($query::WT_PHPS);
        $query->setShow(Query::SHOW_INDEX);

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->any())
            ->method('getQuery')
            ->willReturn($query);
        $resultStub->expects($this->any())
            ->method('getData')
            ->willReturn($data);

        $parser = new ResponseParser();
        $result = $parser->parse($resultStub);

        $this->assertEquals($index, $result['indexResult']);
        $this->assertNull($result['schemaResult']);
        $this->assertNull($result['docResult']);
        $this->assertNull($result['fieldsResult']);
        $this->assertNull($result['infoResult']);
        $this->assertNull($result['shardsResult']);
    }

    /**
     * @depends testParseJson
     */
    public function testIndex(Index $index): void
    {
        $this->assertSame(15, $index->getNumDocs());
        $this->assertSame(20, $index->getMaxDoc());
        $this->assertSame(5, $index->getDeletedDocs());
        $this->assertSame(2000, $index->getIndexHeapUsageBytes());
        $this->assertSame(6, $index->getVersion());
        $this->assertSame(1, $index->getSegmentCount());
        $this->assertFalse($index->getCurrent());
        $this->assertTrue($index->hasDeletions());
        $this->assertSame('directory info', $index->getDirectory());
        $this->assertSame('segments_3', $index->getSegmentsFile());
        $this->assertSame(200, $index->getSegmentsFileSizeInBytes());
        $this->assertSame('123456789123456789', $index->getUserData()->getCommitCommandVer());
        $this->assertSame('123456789', $index->getUserData()->getCommitTimeMSec());
        $this->assertEquals(new \DateTime('2022-01-01T20:00:15.789Z'), $index->getLastModified());
    }

    /**
     * indexHeapUsageBytes was removed in SOLR-15341 for Solr 9.
     */
    public function testParseWithoutIndexHeapUsageBytes(): void
    {
        $indexData = $this->getIndexData();
        unset($indexData['indexHeapUsageBytes']);

        $data = [
            'index' => $indexData,
            'responseHeader' => [
                'status' => 1,
                'QTime' => 13,
            ],
        ];

        $query = new Query();
        $query->setShow(Query::SHOW_INDEX);

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->any())
            ->method('getData')
            ->willReturn($data);
        $resultStub->expects($this->any())
            ->method('getQuery')
            ->willReturn($query);

        $parser = new ResponseParser();
        $result = $parser->parse($resultStub);

        $this->assertNull($result['indexResult']->getIndexHeapUsageBytes());
    }

    /**
     * userData is empty if there haven't been any commits yet.
     * lastModified is calculated from commitTimeMSec (in Solr) and will be empty too.
     */
    public function testParseWithoutUserData(): void
    {
        $indexData = $this->getIndexData();
        $indexData['userData'] = [];
        unset($indexData['lastModified']);

        $data = [
            'index' => $indexData,
            'responseHeader' => [
                'status' => 1,
                'QTime' => 13,
            ],
        ];

        $query = new Query();
        $query->setShow(Query::SHOW_INDEX);

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->any())
            ->method('getData')
            ->willReturn($data);
        $resultStub->expects($this->any())
            ->method('getQuery')
            ->willReturn($query);

        $parser = new ResponseParser();
        $result = $parser->parse($resultStub);

        $this->assertNull($result['indexResult']->getUserData()->getCommitCommandVer());
        $this->assertNull($result['indexResult']->getUserData()->getCommitTimeMSec());
        $this->assertNull($result['indexResult']->getLastModified());
    }

    /**
     * Aggregated metrics in distributed mode contain a limited number of fields.
     */
    public function testParseAggregatedMetrics(): void
    {
        $data = [
            'responseHeader' => [
                'status' => 1,
                'QTime' => 13,
            ],
            'index' => [
                'numDocs' => 15,
                'maxDoc' => 20,
                'deletedDocs' => 5,
                'segmentCount' => 1,
            ],
        ];

        $query = new Query();
        $query->setShow(Query::SHOW_INDEX);

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->any())
            ->method('getData')
            ->willReturn($data);
        $resultStub->expects($this->any())
            ->method('getQuery')
            ->willReturn($query);

        $parser = new ResponseParser();
        $result = $parser->parse($resultStub);
        $index = $result['indexResult'];

        $this->assertSame(15, $index->getNumDocs());
        $this->assertSame(20, $index->getMaxDoc());
        $this->assertSame(5, $index->getDeletedDocs());
        $this->assertNull($index->getIndexHeapUsageBytes());
        $this->assertNull($index->getVersion());
        $this->assertSame(1, $index->getSegmentCount());
        $this->assertNull($index->getCurrent());
        $this->assertNull($index->hasDeletions());
        $this->assertNull($index->getDirectory());
        $this->assertNull($index->getSegmentsFile());
        $this->assertNull($index->getSegmentsFileSizeInBytes());
        $this->assertNull($index->getUserData());
        $this->assertNull($index->getLastModified());
    }

    public function testParseShardsInfo(): void
    {
        $data = [
            'responseHeader' => [
                'status' => 1,
                'QTime' => 13,
            ],
            'index' => [
                'numDocs' => 15,
                'maxDoc' => 20,
                'deletedDocs' => 5,
                'segmentCount' => 1,
            ],
            'shards' => [
                'localhost:8983/solr/shard1' => [
                    'index' => $this->getIndexData(),
                ],
                'localhost:8983/solr/shard2' => [
                    'index' => $this->getIndexData(),
                ],
            ],
        ];

        $data['shards']['localhost:8983/solr/shard1']['index']['numDocs'] = 10;
        $data['shards']['localhost:8983/solr/shard2']['index']['numDocs'] = 5;

        $query = new Query();
        $query->setShow(Query::SHOW_INDEX);
        $query->setShardsInfo(true);

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->any())
            ->method('getData')
            ->willReturn($data);
        $resultStub->expects($this->any())
            ->method('getQuery')
            ->willReturn($query);

        $parser = new ResponseParser();
        $result = $parser->parse($resultStub);

        $this->assertInstanceOf(Index::class, $result['indexResult']);
        $this->assertNull($result['schemaResult']);
        $this->assertNull($result['docResult']);
        $this->assertNull($result['fieldsResult']);
        $this->assertNull($result['infoResult']);
        $this->assertContainsOnlyInstancesOf(Shard::class, $result['shardsResult']);

        $index = $result['indexResult'];
        $this->assertSame(15, $index->getNumDocs());
        $this->assertSame(20, $index->getMaxDoc());
        $this->assertSame(5, $index->getDeletedDocs());
        $this->assertSame(1, $index->getSegmentCount());

        $shards = $result['shardsResult'];
        $this->assertCount(2, $shards);
        $this->assertArrayHasKey('localhost:8983/solr/shard1', $shards);
        $this->assertArrayHasKey('localhost:8983/solr/shard2', $shards);

        $shard1Index = $shards['localhost:8983/solr/shard1']->getIndex();
        $this->assertInstanceOf(Index::class, $shard1Index);
        $this->assertSame(10, $shard1Index->getNumDocs());
        $this->assertSame(20, $shard1Index->getMaxDoc());
        $this->assertSame(5, $shard1Index->getDeletedDocs());
        $this->assertSame(2000, $shard1Index->getIndexHeapUsageBytes());
        $this->assertSame(6, $shard1Index->getVersion());
        $this->assertSame(1, $shard1Index->getSegmentCount());
        $this->assertFalse($shard1Index->getCurrent());
        $this->assertTrue($shard1Index->hasDeletions());
        $this->assertSame('directory info', $shard1Index->getDirectory());
        $this->assertSame('segments_3', $shard1Index->getSegmentsFile());
        $this->assertSame(200, $shard1Index->getSegmentsFileSizeInBytes());
        $this->assertSame('123456789123456789', $shard1Index->getUserData()->getCommitCommandVer());
        $this->assertSame('123456789', $shard1Index->getUserData()->getCommitTimeMSec());
        $this->assertEquals(new \DateTime('2022-01-01T20:00:15.789Z'), $shard1Index->getLastModified());

        $shard1Fields = $shards['localhost:8983/solr/shard1']->getFields();
        $this->assertNull($shard1Fields);

        $shard2Index = $shards['localhost:8983/solr/shard2']->getIndex();
        $this->assertInstanceOf(Index::class, $shard2Index);
        $this->assertSame(5, $shard2Index->getNumDocs());

        $shard2Fields = $shards['localhost:8983/solr/shard2']->getFields();
        $this->assertNull($shard2Fields);
    }
}

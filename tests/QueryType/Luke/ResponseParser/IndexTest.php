<?php

namespace Solarium\Tests\QueryType\Luke\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Luke\Query;
use Solarium\QueryType\Luke\ResponseParser\Index as ResponseParser;
use Solarium\QueryType\Luke\Result\Index\Index;
use Solarium\QueryType\Luke\Result\Result;

class IndexTest extends TestCase
{
    use IndexDataTrait;

    public function testParse(): Index
    {
        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 3,
            ],
            'index' => $this->getIndexData(),
        ];

        $query = new Query();
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

        return $result['indexResult'];
    }

    /**
     * @depends testParse
     */
    public function testIndex(Index $index)
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
    public function testParseWithoutIndexHeapUsageBytes()
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
    public function testParseWithoutUserData()
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
}

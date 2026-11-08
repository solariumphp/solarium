<?php

namespace Solarium\Tests\QueryType\Luke\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Luke\Query;
use Solarium\QueryType\Luke\ResponseParser\Fields as ResponseParser;
use Solarium\QueryType\Luke\Result\Fields\FieldInfo;
use Solarium\QueryType\Luke\Result\Index\Index;
use Solarium\QueryType\Luke\Result\Info\Info;
use Solarium\QueryType\Luke\Result\Result;
use Solarium\QueryType\Luke\Result\Shards\Shard;

class FieldsTest extends TestCase
{
    use FieldsDataTrait;
    use IndexDataTrait;
    use InfoDataTrait;

    public function testParseJson(): array
    {
        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 3,
            ],
            'index' => $this->getIndexData(),
            'fields' => $this->getFieldsJsonData(),
            'info' => $this->getInfoData(),
        ];

        $query = new Query();
        $query->setResponseWriter(Query::WT_JSON);
        $query->setShow(Query::SHOW_ALL);
        $query->setFields('*');

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
        $this->assertContainsOnlyInstancesOf(FieldInfo::class, $result['fieldsResult']);
        $this->assertInstanceOf(Info::class, $result['infoResult']);

        return $result['fieldsResult'];
    }

    /**
     * @depends testParseJson
     *
     * @deprecated Will be removed in Solarium 8
     */
    public function testParsePhps(array $fields): void
    {
        $data = [
            'responseHeader' => [
                'status' => 0,
                'QTime' => 3,
            ],
            'index' => $this->getIndexData(),
            'fields' => $this->getFieldsPhpsData(),
            'info' => $this->getInfoData(),
        ];

        $query = new Query();
        @$query->setResponseWriter(Query::WT_PHPS);
        $query->setShow(Query::SHOW_ALL);
        $query->setFields('*');

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
        $this->assertEquals($fields, $result['fieldsResult']);
        $this->assertInstanceOf(Info::class, $result['infoResult']);
        $this->assertNull($result['shardsResult']);
    }

    /**
     * @depends testParseJson
     */
    public function testName(array $fields): void
    {
        $this->assertSame('field', $fields['field']->getName());
    }

    /**
     * @depends testParseJson
     */
    public function testType(array $fields): void
    {
        $this->assertSame('type_a', $fields['field']->getType());
    }

    /**
     * @depends testParseJson
     */
    public function testSchema(array $fields): void
    {
        $schema = $fields['field']->getSchema();

        $this->assertSame('I-S-U-----OF-----l', (string) $schema);

        // flags are covered exhaustively in SchemaTest::testFieldFlags()
        $this->assertTrue($schema->isIndexed());
        $this->assertFalse($schema->isTokenized());
    }

    /**
     * @depends testParseJson
     */
    public function testDynamicBase(array $fields): void
    {
        $this->assertNull($fields['field']->getDynamicBase());
        $this->assertSame('*_field', $fields['dynamic_field']->getDynamicBase());
    }

    /**
     * @depends testParseJson
     */
    public function testIndex(array $fields): void
    {
        $index = $fields['field']->getIndex();

        $this->assertSame('ITS-------OF------', (string) $index);

        // flags are covered exhaustively in SchemaTest::testFieldFlags()
        $this->assertTrue($index->isTokenized());
        $this->assertFalse($index->isUninvertible());

        $this->assertSame('(unstored field)', $fields['field_unstored']->getIndex());

        $this->assertNull($fields['field_unindexed']->getIndex());
    }

    /**
     * @depends testParseJson
     */
    public function testDocs(array $fields): void
    {
        $this->assertSame(25, $fields['field']->getDocs());
        $this->assertNull($fields['field_unindexed']->getDocs());
    }

    /**
     * @depends testParseJson
     */
    public function testDistinct(array $fields): void
    {
        $this->assertSame(70, $fields['field']->getDistinct());
        $this->assertNull($fields['field_unindexed']->getDistinct());
    }

    /**
     * @depends testParseJson
     */
    public function testTopTerms(array $fields): void
    {
        $this->assertSame([
            'a' => 18,
            'b' => 7,
        ], $fields['field']->getTopTerms());
        $this->assertNull($fields['field_unindexed']->getTopTerms());
    }

    /**
     * @depends testParseJson
     */
    public function testHistogram(array $fields): void
    {
        $this->assertSame([
            '1' => 0,
            '2' => 1,
            '4' => 2,
        ], $fields['field']->getHistogram());
        $this->assertNull($fields['field_unindexed']->getHistogram());
    }

    /**
     * @depends testParseJson
     */
    public function testToString(array $fields): void
    {
        $this->assertSame('field', (string) $fields['field']);
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
            'fields' => $this->getFieldsJsonData(),
            'info' => $this->getInfoData(),
            'shards' => [
                'localhost:8983/solr/shard1' => [
                    'index' => $this->getIndexData(),
                ],
                'localhost:8983/solr/shard2' => [
                    'index' => $this->getIndexData(),
                    'fields' => [
                        'field1' => [
                            'topTerms' => [
                                'a',
                                18,
                                'b',
                                7,
                            ],
                            'histogram' => [
                                '1',
                                0,
                                '2',
                                1,
                                '4',
                                2,
                            ],
                            'distinct' => 70,
                        ],
                        'field2' => [
                            'topTerms' => [
                                'c',
                                12,
                                'a',
                                8,
                            ],
                            'histogram' => [
                                '1',
                                4,
                                '2',
                                1,
                            ],
                            'distinct' => 16,
                        ],
                    ],
                ],
            ],
        ];

        $data['shards']['localhost:8983/solr/shard1']['index']['numDocs'] = 10;
        $data['shards']['localhost:8983/solr/shard2']['index']['numDocs'] = 5;

        $query = new Query();
        $query->setShow(Query::SHOW_ALL);
        $query->setFields('field1,field2');
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
        $this->assertContainsOnlyInstancesOf(FieldInfo::class, $result['fieldsResult']);
        $this->assertInstanceOf(Info::class, $result['infoResult']);
        $this->assertContainsOnlyInstancesOf(Shard::class, $result['shardsResult']);

        $shards = $result['shardsResult'];
        $this->assertCount(2, $shards);
        $this->assertArrayHasKey('localhost:8983/solr/shard1', $shards);
        $this->assertArrayHasKey('localhost:8983/solr/shard2', $shards);

        $shard1Index = $shards['localhost:8983/solr/shard1']->getIndex();
        $this->assertInstanceOf(Index::class, $shard1Index);
        $this->assertSame(10, $shard1Index->getNumDocs());

        $shard1Fields = $shards['localhost:8983/solr/shard1']->getFields();
        $this->assertNull($shard1Fields);

        $shard2Index = $shards['localhost:8983/solr/shard2']->getIndex();
        $this->assertInstanceOf(Index::class, $shard2Index);
        $this->assertSame(5, $shard2Index->getNumDocs());

        $shard2Fields = $shards['localhost:8983/solr/shard2']->getFields();
        $this->assertContainsOnlyInstancesOf(FieldInfo::class, $shard2Fields);
        $this->assertCount(2, $shard2Fields);
        $this->assertArrayHasKey('field1', $shard2Fields);
        $this->assertArrayHasKey('field2', $shard2Fields);

        $shard2field1Info = $shard2Fields['field1'];
        $this->assertSame([
            'a' => 18,
            'b' => 7,
        ], $shard2field1Info->getTopTerms());
        $this->assertSame([
            '1' => 0,
            '2' => 1,
            '4' => 2,
        ], $shard2field1Info->getHistogram());
        $this->assertSame(70, $shard2field1Info->getDistinct());

        $shard2field2Info = $shard2Fields['field2'];
        $this->assertSame([
            'c' => 12,
            'a' => 8,
        ], $shard2field2Info->getTopTerms());
        $this->assertSame([
            '1' => 4,
            '2' => 1,
        ], $shard2field2Info->getHistogram());
        $this->assertSame(16, $shard2field2Info->getDistinct());
    }
}

<?php

namespace Solarium\Tests\QueryType\Analysis\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Query\Result\Result;
use Solarium\QueryType\Analysis\Query\Field as Query;
use Solarium\QueryType\Analysis\ResponseParser\Field as FieldParser;

class FieldTest extends TestCase
{
    public function testParse(): void
    {
        $data = [
            'analysis' => [
                'field_types' => [
                    'type1' => [
                        'index' => [
                            'analysisClass',
                            'string value',
                            'org.apache.solr.analysis.PatternReplaceCharFilter',
                            [
                                [
                                    'text' => 'test',
                                    'start' => 1,
                                    'end' => 23,
                                    'position' => 4,
                                    'positionHistory' => [4, 3],
                                    'type' => 'test',
                                ],
                                [
                                    'text' => 'test2',
                                    'start' => 1,
                                    'end' => 23,
                                    'position' => 4,
                                    'positionHistory' => [4, 3],
                                    'type' => 'test',
                                ],
                            ],
                        ],
                    ],
                ],
                'field_names' => [
                    'field1' => [
                        'query' => [
                            'org.apache.lucene.analysis.standard.StandardTokenizer',
                            [
                                [
                                    'text' => 'TEST',
                                    'start' => 1,
                                    'end' => 23,
                                    'position' => 1,
                                    'positionHistory' => [1],
                                    'type' => 'test',
                                ],
                            ],
                            'org.apache.lucene.analysis.core.LowerCaseFilter',
                            [
                                [
                                    'text' => 'test',
                                    'start' => 1,
                                    'end' => 23,
                                    'position' => 1,
                                    'positionHistory' => [1, 1],
                                    'type' => 'test',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'responseHeader' => [
                'status' => 1,
                'QTime' => 5,
            ],
        ];

        $query = new Query();
        $query->setResponseWriter($query::WT_JSON);

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->any())
            ->method('getQuery')
            ->willReturn($query);
        $resultStub->expects($this->any())
            ->method('getData')
            ->willReturn($data);

        $parser = new FieldParser();
        $result = $parser->parse($resultStub);

        $this->assertCount(2, $result['items']);

        $fieldTypes = $result['items'][0];
        $this->assertSame('field_types', $fieldTypes->getName());
        $this->assertCount(1, $fieldTypes->getItems());
        $this->assertSame('type1', $fieldTypes->getItems()[0]->getName());

        $indexAnalysis = $fieldTypes->getItems()[0]->getIndexAnalysis();
        $this->assertCount(2, $indexAnalysis->getItems());
        $this->assertSame('analysisClass', $indexAnalysis->getItems()[0]->getName());
        $this->assertCount(1, $indexAnalysis->getItems()[0]->getItems());
        $this->assertSame('string value', $indexAnalysis->getItems()[0]->getItems()[0]->getText());
        $this->assertSame('org.apache.solr.analysis.PatternReplaceCharFilter', $indexAnalysis->getItems()[1]->getName());
        $this->assertCount(2, $indexAnalysis->getItems()[1]->getItems());
        $this->assertSame('test', $indexAnalysis->getItems()[1]->getItems()[0]->getText());
        $this->assertSame('test2', $indexAnalysis->getItems()[1]->getItems()[1]->getText());

        $fieldNames = $result['items'][1];
        $this->assertSame('field_names', $fieldNames->getName());
        $this->assertCount(1, $fieldNames->getItems());
        $this->assertSame('field1', $fieldNames->getItems()[0]->getName());

        $queryAnalysis = $fieldNames->getItems()[0]->getQueryAnalysis();
        $this->assertCount(2, $queryAnalysis->getItems());
        $this->assertSame('org.apache.lucene.analysis.standard.StandardTokenizer', $queryAnalysis->getItems()[0]->getName());
        $this->assertCount(1, $queryAnalysis->getItems()[0]->getItems());
        $this->assertSame('TEST', $queryAnalysis->getItems()[0]->getItems()[0]->getText());
        $this->assertSame([1], $queryAnalysis->getItems()[0]->getItems()[0]->getPositionHistory());
        $this->assertSame('org.apache.lucene.analysis.core.LowerCaseFilter', $queryAnalysis->getItems()[1]->getName());
        $this->assertCount(1, $queryAnalysis->getItems()[1]->getItems());
        $this->assertSame('test', $queryAnalysis->getItems()[1]->getItems()[0]->getText());
        $this->assertSame([1, 1], $queryAnalysis->getItems()[1]->getItems()[0]->getPositionHistory());
    }

    public function testParseNoData(): void
    {
        $data = [
            'responseHeader' => [
                'status' => 1,
                'QTime' => 5,
            ],
        ];

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->once())
             ->method('getData')
             ->willReturn($data);

        $parser = new FieldParser();
        $result = $parser->parse($resultStub);

        $this->assertEquals(
            [
                'items' => [],
            ],
            $result
        );
    }
}

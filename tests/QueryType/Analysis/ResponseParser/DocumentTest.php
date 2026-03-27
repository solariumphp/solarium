<?php

namespace Solarium\Tests\QueryType\Analysis\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Query\Result\Result;
use Solarium\QueryType\Analysis\Query\Document as Query;
use Solarium\QueryType\Analysis\ResponseParser\Document as DocumentParser;

class DocumentTest extends TestCase
{
    public function testParse(): void
    {
        $data = [
            'analysis' => [
                'MA147LL' => [
                    'id' => [
                        'query' => [
                            'org.apache.solr.schema.FieldType$DefaultAnalyzer$1',
                            [
                                [
                                    'text' => 'foobar',
                                    'start' => 0,
                                    'end' => 6,
                                    'type' => 'word',
                                    'position' => 1,
                                    'positionHistory' => [1],
                                    'match' => false,
                                ],
                            ],
                        ],
                        'index' => [
                            'MA147LL' => [
                                'org.apache.solr.schema.FieldType$DefaultAnalyzer$2',
                                [
                                    [
                                        'text' => 'MA147LL',
                                        'start' => 0,
                                        'end' => 7,
                                        'type' => 'word',
                                        'position' => 1,
                                        'positionHistory' => [1],
                                        'match' => true,
                                    ],
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

        $parser = new DocumentParser();
        $result = $parser->parse($resultStub);

        $this->assertCount(1, $result['items']);

        $doc = $result['items'][0];
        $this->assertSame('MA147LL', $doc->getName());

        $fields = $doc->getItems();
        $this->assertCount(1, $fields);
        $this->assertSame('id', $fields[0]->getName());

        $queryAnalysis = $fields[0]->getQueryAnalysis();
        $this->assertCount(1, $queryAnalysis->getItems());
        $this->assertSame('org.apache.solr.schema.FieldType$DefaultAnalyzer$1', $queryAnalysis->getItems()[0]->getName());
        $this->assertCount(1, $queryAnalysis->getItems()[0]->getItems());
        $this->assertSame('foobar', $queryAnalysis->getItems()[0]->getItems()[0]->getText());
        $this->assertFalse($queryAnalysis->getItems()[0]->getItems()[0]->getMatch());

        $indexAnalysis = $fields[0]->getIndexAnalysis();
        $this->assertCount(1, $indexAnalysis->getItems());
        $this->assertSame('org.apache.solr.schema.FieldType$DefaultAnalyzer$2', $indexAnalysis->getItems()[0]->getName());
        $this->assertCount(1, $indexAnalysis->getItems()[0]->getItems());
        $this->assertSame('MA147LL', $indexAnalysis->getItems()[0]->getItems()[0]->getText());
        $this->assertTrue($indexAnalysis->getItems()[0]->getItems()[0]->getMatch());
    }
}

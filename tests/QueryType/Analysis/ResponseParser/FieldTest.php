<?php

namespace Solarium\Tests\QueryType\Analysis\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Query\Result\Result;
use Solarium\QueryType\Analysis\Query\Field as Query;
use Solarium\QueryType\Analysis\ResponseParser\Field as FieldParser;

class FieldTest extends TestCase
{
    public function testParse()
    {
        $data = [
            'analysis' => [
                'doc1' => [
                    'field1' => [
                        'type1' => [
                            [
                                'org.apache.solr.analysis.PatternReplaceCharFilter',
                                'string value',
                                'analysisClass',
                                [
                                    [
                                        'text' => 'test',
                                        'start' => 1,
                                        'end' => 23,
                                        'position' => 4,
                                        'positionHistory' => 'test',
                                        'type' => 'test',
                                    ],
                                    [
                                        'text' => 'test2',
                                        'start' => 1,
                                        'end' => 23,
                                        'position' => 4,
                                        'positionHistory' => 'test',
                                        'type' => 'test',
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

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->once())
             ->method('getData')
             ->willReturn($data);
        $resultStub->expects($this->once())
                     ->method('getQuery')
                     ->willReturn(new Query());

        $parser = new FieldParser();
        $result = $parser->parse($resultStub);

        $docs = $result['items'][0]->getItems();
        $fields = $docs[0]->getItems();
        $types = $fields[0]->getItems();
        $class1items = $types[0]->getItems();
        $class2items = $types[1]->getItems();

        $this->assertSame('string value', $class1items[0]->getText());
        $this->assertSame('test2', $class2items[1]->getText());
    }

    public function testParseNoData()
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

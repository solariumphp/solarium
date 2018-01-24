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
        $data = array(
            'analysis' => array(
                'doc1' => array(
                    'field1' => array(
                        'type1' => array(
                            array(
                                'org.apache.solr.analysis.PatternReplaceCharFilter',
                                'string value',
                                'analysisClass',
                                array(
                                    array(
                                        'text' => 'test',
                                        'start' => 1,
                                        'end' => 23,
                                        'position' => 4,
                                        'positionHistory' => 'test',
                                        'type' => 'test',
                                    ),
                                    array(
                                        'text' => 'test2',
                                        'start' => 1,
                                        'end' => 23,
                                        'position' => 4,
                                        'positionHistory' => 'test',
                                        'type' => 'test',
                                    )
                                )
                            )
                        )
                    )
                )
            ),
            'responseHeader' => array(
                'status' => 1,
                'QTime' => 5,
            )
        );

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->once())
             ->method('getData')
             ->will($this->returnValue($data));
        $resultStub->expects($this->once())
                     ->method('getQuery')
                     ->will($this->returnValue(new Query));

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
        $data = array(
            'responseHeader' => array(
                'status' => 1,
                'QTime' => 5,
            )
        );

        $resultStub = $this->createMock(Result::class);
        $resultStub->expects($this->once())
             ->method('getData')
             ->will($this->returnValue($data));

        $parser = new FieldParser();
        $result = $parser->parse($resultStub);

        $this->assertEquals(
            array(
                'status' => 1,
                'queryTime' => 5,
                'items' => array()
            ),
            $result
        );
    }
}

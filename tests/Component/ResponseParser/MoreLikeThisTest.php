<?php

namespace Solarium\Tests\Component\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ResponseParser\MoreLikeThis as Parser;
use Solarium\Component\Result\MoreLikeThis\Result;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\Result\Document;

class MoreLikeThisTest extends TestCase
{
    protected $parser;

    protected $query;

    public function setUp(): void
    {
        $this->parser = new Parser();
        $this->query = new Query();
    }

    public function testParse()
    {
        $data = [
            'moreLikeThis' => [
                'id1' => [
                    'numFound' => 12,
                    'maxScore' => 1.75,
                    'docs' => [
                        ['field1' => 'value1'],
                    ],
                ],
            ],
        ];

        $docs = [new Document(['field1' => 'value1'])];
        $expected = [
            'id1' => new Result(12, 1.75, $docs),
        ];

        $result = $this->parser->parse($this->query, null, $data);

        $this->assertEquals($expected, $result->getResults());
    }

    public function testParseNoData()
    {
        $result = $this->parser->parse($this->query, null, []);

        $this->assertEquals([], $result->getResults());
    }

    public function testParseWithoutMaxScore()
    {
        $data = [
            'moreLikeThis' => [
                'id1' => [
                    'numFound' => 12,
                    'docs' => [
                        ['field1' => 'value1'],
                    ],
                ],
            ],
        ];

        $docs = [new Document(['field1' => 'value1'])];
        $expected = [
            'id1' => new Result(12, null, $docs),
        ];

        $result = $this->parser->parse($this->query, null, $data);

        $this->assertEquals($expected, $result->getResults());
    }
}

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
            'id1' => new Result(12, 1.75, $docs, null),
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
            'id1' => new Result(12, null, $docs, null),
        ];

        $result = $this->parser->parse($this->query, null, $data);

        $this->assertEquals($expected, $result->getResults());
    }

    public function testParseInterestingTermsList()
    {
        $data = [
            'interestingTerms' => [
                'id1' => [
                    'field2:term1',
                    'field2:term2',
                ],
            ],
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
        $interestingTerms = ['field2:term1', 'field2:term2'];
        $expected = [
            'id1' => new Result(12, 1.75, $docs, $interestingTerms),
        ];

        $result = $this->parser->parse($this->query, null, $data);

        $this->assertEquals($expected, $result->getResults());
    }

    public function testParseInterestingTermsDetails()
    {
        $data = [
            'interestingTerms' => [
                'id1' => [
                    'field2:term1' => 1.0,
                    'field2:term2' => 1.84,
                ],
            ],
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
        $interestingTerms = ['field2:term1' => 1.0, 'field2:term2' => 1.84];
        $expected = [
            'id1' => new Result(12, 1.75, $docs, $interestingTerms),
        ];

        $result = $this->parser->parse($this->query, null, $data);

        $this->assertEquals($expected, $result->getResults());
    }
}

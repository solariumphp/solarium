<?php

namespace Solarium\Tests\Component\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Component\MoreLikeThis;
use Solarium\Component\ResponseParser\MoreLikeThis as Parser;
use Solarium\Component\Result\MoreLikeThis\Result;
use Solarium\Exception\UnexpectedValueException;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\Result\Document;

class MoreLikeThisTest extends TestCase
{
    protected $parser;

    protected $query;

    protected $mlt;

    public function setUp(): void
    {
        $this->parser = new Parser();
        $this->query = new Query();
        $this->mlt = new MoreLikeThis();
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

        $result = $this->parser->parse($this->query, $this->mlt, $data);

        $this->assertEquals($expected, $result->getResults());
    }

    public function testParseNoData()
    {
        $result = $this->parser->parse($this->query, $this->mlt, []);

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

        $result = $this->parser->parse($this->query, $this->mlt, $data);

        $this->assertEquals($expected, $result->getResults());
    }

    public function testParseInterestingTermsNone()
    {
        $this->mlt->setInterestingTerms('none');

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

        $result = $this->parser->parse($this->query, $this->mlt, $data);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('interestingterms is none');
        $this->assertNull($result->getInterestingTerms());
    }

    public function testParseInterestingTermsList()
    {
        $this->mlt->setInterestingTerms('list');

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

        $expected = [
            'id1' => [
                'field2:term1',
                'field2:term2',
            ],
        ];

        $result = $this->parser->parse($this->query, $this->mlt, $data);

        $this->assertEquals($expected, $result->getInterestingTerms());
    }

    public function testParseInterestingTermsDetails()
    {
        $this->mlt->setInterestingTerms('list');

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

        $expected = [
            'id1' => [
                'field2:term1' => 1.0,
                'field2:term2' => 1.84,
            ],
        ];

        $result = $this->parser->parse($this->query, $this->mlt, $data);

        $this->assertEquals($expected, $result->getInterestingTerms());
    }
}

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
    protected Parser $parser;

    protected Query $query;

    protected MoreLikeThis $mlt;

    public function setUp(): void
    {
        $this->parser = new Parser();
        $this->query = new Query();
        $this->mlt = $this->query->getMoreLikeThis();
    }

    public function testParse(): void
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

    public function testParseNoData(): void
    {
        $result = $this->parser->parse($this->query, $this->mlt, []);

        $this->assertEquals([], $result->getResults());
    }

    public function testParseWithoutMaxScore(): void
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

    public function testParseInterestingTermsNone(): void
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

    public function testParseInterestingTermsList(): void
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

    public function testParseInterestingTermsDetails(): void
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

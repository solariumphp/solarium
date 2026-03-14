<?php

namespace Solarium\Tests\Component\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ResponseParser\Suggester as Parser;
use Solarium\Component\Suggester;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Suggester\Result\Dictionary;
use Solarium\QueryType\Suggester\Result\Term;

class SuggesterTest extends TestCase
{
    protected Parser $parser;

    protected Query $query;

    protected Suggester $suggester;

    public function setUp(): void
    {
        $this->parser = new Parser();
        $this->query = new Query();
        $this->suggester = $this->query->getSuggester();
    }

    /**
     * @dataProvider providerParse
     *
     * @param mixed $data
     */
    public function testParse($data): void
    {
        $result = $this->parser->parse($this->query, $this->suggester, $data);

        $expected = new Dictionary([
            'foo' => new Term(2, [['term' => 'foo'], ['term' => 'foobar']]),
            'zoo' => new Term(1, [['term' => 'zoo keeper']]),
        ]);

        $this->assertEquals($expected, $result->getDictionary('dictionary1'));

        $expected = new Dictionary([
            'free' => new Term(2, [['term' => 'free beer'], ['term' => 'free software']]),
        ]);

        $this->assertEquals($expected, $result->getDictionary('dictionary2'));

        $allExpected = [
            new Term(2, [['term' => 'foo'], ['term' => 'foobar']]),
            new Term(1, [['term' => 'zoo keeper']]),
            new Term(2, [['term' => 'free beer'], ['term' => 'free software']]),
        ];

        $this->assertEquals($allExpected, $result->getAll());
    }

    public static function providerParse(): array
    {
        return [
            0 => [
                'data' => [
                    'suggest' => [
                        'dictionary1' => [
                            'foo' => [
                                'numFound' => 2,
                                'suggestions' => [
                                    [
                                        'term' => 'foo',
                                    ],
                                    [
                                        'term' => 'foobar',
                                    ],
                                ],
                            ],
                            'zoo' => [
                                'numFound' => 1,
                                'suggestions' => [
                                    [
                                        'term' => 'zoo keeper',
                                    ],
                                ],
                            ],
                        ],
                        'dictionary2' => [
                            'free' => [
                                'numFound' => 2,
                                'suggestions' => [
                                    [
                                        'term' => 'free beer',
                                    ],
                                    [
                                        'term' => 'free software',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function testParseNoData(): void
    {
        $result = $this->parser->parse($this->query, $this->suggester, []);

        $this->assertNull($result);
    }
}

<?php

namespace Solarium\Tests\Component\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Component\TermVector;
use Solarium\Component\ResponseParser\TermVector as Parser;
use Solarium\Component\Result\TermVector\Document;
use Solarium\Component\Result\TermVector\Field;
use Solarium\Component\Result\TermVector\Result;
use Solarium\Component\Result\TermVector\Term;
use Solarium\Component\Result\TermVector\Warnings;
use Solarium\Exception\InvalidArgumentException;
use Solarium\QueryType\Select\Query\Query;

class TermVectorTest extends TestCase
{
    protected $parser;

    protected $query;

    protected $tv;

    public function setUp(): void
    {
        $this->parser = new Parser();
        $this->query = new Query();
        $this->tv = new TermVector();
    }

    /**
     * @dataProvider expectedResultProvider
     */
    public function testParseWtJson(Result $expectedResult)
    {
        $data = [
            'termVectors' => [
                'warnings',
                [
                    'noTermVectors',
                    [
                        'fieldB',
                        'fieldC',
                    ],
                    'noPositions',
                    [
                        'fieldA',
                        'fieldD',
                    ],
                    'noOffsets',
                    [
                        'fieldA',
                        'fieldE',
                    ],
                    'noPayloads',
                    [
                        'fieldA',
                        'fieldF',
                    ],
                ],
                'key1',
                [
                    'uniqueKey',
                    'key1',
                    'fieldA',
                    [
                        'term1',
                        [
                            'tf',
                            1,
                            'df',
                            4,
                            'tf-idf',
                            0.25,
                        ],
                        'term2',
                        [
                            'tf',
                            3,
                            'df',
                            6,
                            'tf-idf',
                            0.5,
                        ],
                    ],
                    'fieldB',
                    [
                        'term3',
                        [
                            'positions',
                            [
                                'position',
                                2,
                            ],
                            'offsets',
                            [
                                'start',
                                8,
                                'end',
                                12,
                            ],
                            'payloads',
                            [
                                'payload',
                                'cGwzLTE=',
                            ],
                        ],
                        'term4',
                        [
                            'positions',
                            [
                                'position',
                                4,
                                'position',
                                6,
                            ],
                            'offsets',
                            [
                                'start',
                                15,
                                'end',
                                20,
                                'start',
                                25,
                                'end',
                                30,
                            ],
                            'payloads',
                            [
                                'payload',
                                'cGwzLTI=',
                                'payload',
                                'cGwzLTM=',
                            ],
                        ],
                    ],
                ],
                'key2',
                [
                    'uniqueKey',
                    'key2',
                ],
            ],
        ];

        $this->query->setResponseWriter($this->query::WT_JSON);

        $result = $this->parser->parse($this->query, $this->tv, $data);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @dataProvider expectedResultProvider
     */
    public function testParseWtPhps(Result $expectedResult)
    {
        $data = [
            'termVectors' => [
                'warnings' => [
                    'noTermVectors' => [
                        'fieldB',
                        'fieldC',
                    ],
                    'noPositions' => [
                        'fieldA',
                        'fieldD',
                    ],
                    'noOffsets' => [
                        'fieldA',
                        'fieldE',
                    ],
                    'noPayloads' => [
                        'fieldA',
                        'fieldF',
                    ],
                ],
                'key1' => [
                    'uniqueKey' => 'key1',
                    'fieldA' => [
                        'term1' => [
                            'tf' => 1,
                            'df' => 4,
                            'tf-idf' => 0.25,
                        ],
                        'term2' => [
                            'tf' => 3,
                            'df' => 6,
                            'tf-idf' => 0.5,
                        ],
                    ],
                    'fieldB' => [
                        'term3' => [
                            'positions' => [
                                'position' => 2,
                            ],
                            'offsets' => [
                                'start' => 8,
                                'end' => 12,
                            ],
                            'payloads' => [
                                'payload' => 'cGwzLTE=',
                            ],
                        ],
                        'term4' => [
                            'positions' => [
                                'position' => 4,
                                'position 1' => 6,
                            ],
                            'offsets' => [
                                'start' => 15,
                                'end' => 20,
                                'start 1' => 25,
                                'end 1' => 30,
                            ],
                            'payloads' => [
                                'payload' => 'cGwzLTI=',
                                'payload 1' => 'cGwzLTM=',
                            ],
                        ],
                    ],
                ],
                'key2' => [
                    'uniqueKey' => 'key2',
                ],
            ],
        ];

        $this->query->setResponseWriter($this->query::WT_PHPS);

        $result = $this->parser->parse($this->query, $this->tv, $data);

        $this->assertEquals($expectedResult, $result);
    }

    public function expectedResultProvider(): array
    {
        $result = new Result(
            [
                'key1' => new Document(
                    'key1',
                    [
                        'fieldA' => new Field(
                            'fieldA',
                            [
                                'term1' => new Term(
                                    term: 'term1',
                                    tf: 1,
                                    positions: null,
                                    offsets: null,
                                    payloads: null,
                                    df: 4,
                                    tfIdf: 0.25,
                                ),
                                'term2' => new Term(
                                    term: 'term2',
                                    tf: 3,
                                    positions: null,
                                    offsets: null,
                                    payloads: null,
                                    df: 6,
                                    tfIdf: 0.5,
                                ),
                            ]
                        ),
                        'fieldB' => new Field(
                            'fieldB',
                            [
                                'term3' => new Term(
                                    term: 'term3',
                                    tf: null,
                                    positions: [2],
                                    offsets: [['start' => 8, 'end' => 12]],
                                    payloads: ['cGwzLTE='],
                                    df: null,
                                    tfIdf: null,
                                ),
                                'term4' => new Term(
                                    term: 'term4',
                                    tf: null,
                                    positions: [4, 6],
                                    offsets: [['start' => 15, 'end' => 20], ['start' => 25, 'end' => 30]],
                                    payloads: ['cGwzLTI=', 'cGwzLTM='],
                                    df: null,
                                    tfIdf: null,
                                ),
                            ]
                        ),
                    ],
                ),
                'key2' => new Document(
                    'key2',
                    [],
                ),
            ],
            new Warnings(
                noTermVectors: ['fieldB', 'fieldC'],
                noPositions: ['fieldA', 'fieldD'],
                noOffsets: ['fieldA', 'fieldE'],
                noPayloads: ['fieldA', 'fieldF'],
            )
        );

        return [
            [$result],
        ];
    }

    /**
     * @dataProvider expectedResultAmbiguousKeysProvider
     */
    public function testParseAmbiguousKeysWtJson(Result $expectedResult)
    {
        $data = [
            'termVectors' => [
                'warnings',
                [
                    'uniqueKey',
                    [
                        'term1',
                        [
                            'tf',
                            1,
                        ],
                    ],
                ],
            ],
        ];

        $this->query->setResponseWriter($this->query::WT_JSON);

        $result = $this->parser->parse($this->query, $this->tv, $data);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @dataProvider expectedResultAmbiguousKeysProvider
     */
    public function testParseAmbiguousKeysWtPhps(Result $expectedResult)
    {
        $data = [
            'termVectors' => [
                'warnings' => [
                    'uniqueKey' => [
                        'term1' => [
                            'tf' => 1,
                        ],
                    ],
                ],
            ],
        ];

        $this->query->setResponseWriter($this->query::WT_PHPS);

        $result = $this->parser->parse($this->query, $this->tv, $data);

        $this->assertEquals($expectedResult, $result);
    }

    public function expectedResultAmbiguousKeysProvider(): array
    {
        $result = new Result(
            [
                'warnings' => new Document(
                    null,
                    [
                        'uniqueKey' => new Field(
                            'uniqueKey',
                            [
                                'term1' => new Term(
                                    term: 'term1',
                                    tf: 1,
                                    positions: null,
                                    offsets: null,
                                    payloads: null,
                                    df: null,
                                    tfIdf: null,
                                ),
                            ]
                        ),
                    ],
                ),
            ],
            null
        );

        return [
            [$result],
        ];
    }

    /**
     * @dataProvider expectedResultDoubleKeysProvider
     */
    public function testParseDoubleKeysWtJson(Result $expectedResult)
    {
        $data = [
            'termVectors' => [
                'warnings',
                [
                    'noTermVectors',
                    [
                        'fieldA',
                    ],
                ],
                'warnings',
                [
                    'uniqueKey',
                    'warnings',
                    'uniqueKey',
                    [
                        'term1',
                        [
                            'tf',
                            1,
                        ],
                    ],
                ],
            ],
        ];

        $this->query->setResponseWriter($this->query::WT_JSON);

        $result = $this->parser->parse($this->query, $this->tv, $data);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @dataProvider expectedResultDoubleKeysProvider
     */
    public function testParseDoubleKeysWtPhps(Result $expectedResult)
    {
        $data = [
            'termVectors' => [
                'warnings' => [
                    'noTermVectors' => [
                        'fieldA',
                    ],
                ],
                'warnings 1' => [
                    'uniqueKey' => 'warnings',
                    'uniqueKey 1' => [
                        'term1' => [
                            'tf' => 1,
                        ],
                    ],
                ],
            ],
        ];

        $this->query->setResponseWriter($this->query::WT_PHPS);

        $result = $this->parser->parse($this->query, $this->tv, $data);

        $this->assertEquals($expectedResult, $result);
    }

    public function expectedResultDoubleKeysProvider(): array
    {
        $result = new Result(
            [
                'warnings' => new Document(
                    'warnings',
                    [
                        'uniqueKey' => new Field(
                            'uniqueKey',
                            [
                                'term1' => new Term(
                                    term: 'term1',
                                    tf: 1,
                                    positions: null,
                                    offsets: null,
                                    payloads: null,
                                    df: null,
                                    tfIdf: null,
                                ),
                            ]
                        ),
                    ],
                ),
            ],
            new Warnings(
                noTermVectors: ['fieldA'],
                noPositions: null,
                noOffsets: null,
                noPayloads: null,
            )
        );

        return [
            [$result],
        ];
    }

    /**
     * @dataProvider expectedResultNoDocumentsProvider
     */
    public function testParseNoDocumentsWtJson(Result $expectedResult)
    {
        $data = [
            'termVectors' => [
                'warnings',
                [
                    'noTermVectors',
                    [
                        'fieldA',
                    ],
                ],
            ],
        ];

        $this->query->setResponseWriter($this->query::WT_JSON);

        $result = $this->parser->parse($this->query, $this->tv, $data);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @dataProvider expectedResultNoDocumentsProvider
     */
    public function testParseNoDocumentsWtPhps(Result $expectedResult)
    {
        $data = [
            'termVectors' => [
                'warnings' => [
                    'noTermVectors' => [
                        'fieldA',
                    ],
                ],
            ],
        ];

        $this->query->setResponseWriter($this->query::WT_PHPS);

        $result = $this->parser->parse($this->query, $this->tv, $data);

        $this->assertEquals($expectedResult, $result);
    }

    public function expectedResultNoDocumentsProvider(): array
    {
        $result = new Result(
            [],
            new Warnings(
                noTermVectors: ['fieldA'],
                noPositions: null,
                noOffsets: null,
                noPayloads: null,
            )
        );

        return [
            [$result],
        ];
    }

    public function testParseNoData()
    {
        $result = $this->parser->parse($this->query, $this->tv, []);

        $this->assertNull($result);
    }

    public function testParseNoQuery()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A valid query object needs to be provided.');
        $this->parser->parse(null, $this->tv, []);
    }
}

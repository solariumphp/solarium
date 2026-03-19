<?php

namespace Solarium\Tests\Component\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ResponseParser\Spellcheck as Parser;
use Solarium\Component\Spellcheck;
use Solarium\QueryType\Select\Query\Query;

class SpellcheckTest extends TestCase
{
    protected Parser $parser;

    protected Query $query;

    protected Spellcheck $spellcheck;

    public function setUp(): void
    {
        $this->parser = new Parser();
        $this->query = new Query();
        $this->spellcheck = $this->query->getSpellcheck();
    }

    /**
     * @dataProvider providerParseExtended
     *
     * @param mixed $data
     */
    public function testParseExtended($data): void
    {
        $result = $this->parser->parse($this->query, $this->spellcheck, $data);

        $suggestions = $result->getSuggestions();
        $this->assertFalse($result->getCorrectlySpelled());
        $this->assertEquals('dell', $suggestions[0]->getWord());
        $this->assertEquals('ultrasharp', $suggestions[1]->getWord());
        $this->assertEquals(6, $suggestions[1]->getStartOffset());
        $this->assertEquals('ultrasharp', $suggestions[2]->getWord());
        $this->assertEquals(16, $suggestions[2]->getStartOffset());
        $this->assertEquals('dell ultrasharp', $result->getCollation()->getQuery());
        $collations = $result->getCollations();
        $this->assertEquals('dell ultrasharp', $collations[0]->getQuery());
        $this->assertEquals('dell ultrasharp new', $collations[1]->getQuery());
        $this->assertEquals(
            [
                'delll' => 'dell',
                'ultrashar' => [
                    'ultrasharp',
                    'ultrasharp',
                ],
            ],
            $collations[0]->getCorrections()
        );
    }

    public static function providerParseExtended(): array
    {
        return [
            'solr4' => [
                'data' => [
                    'spellcheck' => [
                        'suggestions' => [
                            'delll',
                            [
                                'numFound' => 1,
                                'startOffset' => 0,
                                'endOffset' => 5,
                                'origFreq' => 0,
                                'suggestion' => [
                                    0 => [
                                        'word' => 'dell',
                                        'freq' => 1,
                                    ],
                                ],
                            ],
                            'ultrashar',
                            [
                                'numFound' => 1,
                                'startOffset' => 6,
                                'endOffset' => 15,
                                'origFreq' => 0,
                                'suggestion' => [
                                    0 => [
                                        'word' => 'ultrasharp',
                                        'freq' => 1,
                                    ],
                                ],
                            ],
                            'ultrashar',
                            [
                                'numFound' => 1,
                                'startOffset' => 16,
                                'endOffset' => 25,
                                'origFreq' => 0,
                                'suggestion' => [
                                    0 => [
                                        'word' => 'ultrasharp',
                                        'freq' => 1,
                                    ],
                                ],
                            ],
                            'correctlySpelled',
                            false,
                            'collation',
                            [
                                0 => 'collationQuery',
                                1 => 'dell ultrasharp',
                                2 => 'hits',
                                3 => 0,
                                4 => 'misspellingsAndCorrections',
                                5 => [
                                    0 => 'delll',
                                    1 => 'dell',
                                    2 => 'ultrashar',
                                    3 => 'ultrasharp',
                                    4 => 'ultrashar',
                                    5 => 'ultrasharp',
                                ],
                            ],
                            'collation',
                            [
                                0 => 'collationQuery',
                                1 => 'dell ultrasharp new',
                                2 => 'hits',
                                3 => 0,
                                4 => 'misspellingsAndCorrections',
                                5 => [
                                    0 => 'delll',
                                    1 => 'dell',
                                    2 => 'ultrashar',
                                    3 => 'ultrasharp',
                                    4 => 'ultrashar',
                                    5 => 'ultrasharp',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'solr5' => [
                'data' => [
                    'spellcheck' => [
                        'suggestions' => [
                            'delll',
                            [
                                'numFound' => 1,
                                'startOffset' => 0,
                                'endOffset' => 5,
                                'origFreq' => 0,
                                'suggestion' => [
                                    0 => [
                                        'word' => 'dell',
                                        'freq' => 1,
                                    ],
                                ],
                            ],
                            'ultrashar',
                            [
                                'numFound' => 1,
                                'startOffset' => 6,
                                'endOffset' => 15,
                                'origFreq' => 0,
                                'suggestion' => [
                                    0 => [
                                        'word' => 'ultrasharp',
                                        'freq' => 1,
                                    ],
                                ],
                            ],
                            'ultrashar',
                            [
                                'numFound' => 1,
                                'startOffset' => 16,
                                'endOffset' => 25,
                                'origFreq' => 0,
                                'suggestion' => [
                                    0 => [
                                        'word' => 'ultrasharp',
                                        'freq' => 1,
                                    ],
                                ],
                            ],
                        ],
                        'correctlySpelled' => false,
                        'collations' => [
                            'collation',
                            [
                                'collationQuery' => 'dell ultrasharp',
                                'hits' => 0,
                                'misspellingsAndCorrections' => [
                                    0 => 'delll',
                                    1 => 'dell',
                                    2 => 'ultrashar',
                                    3 => 'ultrasharp',
                                    4 => 'ultrashar',
                                    5 => 'ultrasharp',
                                ],
                            ],
                            'collation',
                            [
                                'collationQuery' => 'dell ultrasharp new',
                                'hits' => 0,
                                'misspellingsAndCorrections' => [
                                    0 => 'delll',
                                    1 => 'dell',
                                    2 => 'ultrashar',
                                    3 => 'ultrasharp',
                                    4 => 'ultrashar',
                                    5 => 'ultrasharp',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerParse
     *
     * @param mixed $data
     */
    public function testParse($data): void
    {
        $result = $this->parser->parse($this->query, $this->spellcheck, $data);

        $suggestions = $result->getSuggestions();
        $this->assertFalse($result->getCorrectlySpelled());
        $this->assertEquals('dell', $suggestions[0]->getWord());
        $this->assertEquals('dell ultrasharp', $result->getCollation()->getQuery());
        $collations = $result->getCollations();
        $this->assertEquals('dell ultrasharp', $collations[0]->getQuery());
        $this->assertEquals('dell ultrasharp new', $collations[1]->getQuery());
    }

    public static function providerParse(): array
    {
        return [
            'solr4' => [
                'data' => [
                    'spellcheck' => [
                        'suggestions' => [
                            0 => 'delll',
                            1 => [
                                'numFound' => 1,
                                'startOffset' => 0,
                                'endOffset' => 5,
                                'origFreq' => 0,
                                'suggestion' => [
                                    0 => 'dell',
                                ],
                            ],
                            2 => 'ultrashar',
                            3 => [
                                'numFound' => 1,
                                'startOffset' => 6,
                                'endOffset' => 15,
                                'origFreq' => 0,
                                'suggestion' => [
                                    0 => [
                                        'word' => 'ultrasharp',
                                        'freq' => 1,
                                    ],
                                ],
                            ],
                            4 => 'correctlySpelled',
                            5 => false,
                            6 => 'collation',
                            7 => 'dell ultrasharp',
                            8 => 'collation',
                            9 => 'dell ultrasharp new',
                        ],
                    ],
                ],
            ],
            'solr5' => [
                'data' => [
                    'spellcheck' => [
                        'suggestions' => [
                            0 => 'delll',
                            1 => [
                                'numFound' => 1,
                                'startOffset' => 0,
                                'endOffset' => 5,
                                'origFreq' => 0,
                                'suggestion' => [
                                    0 => 'dell',
                                ],
                            ],
                            2 => 'ultrashar',
                            3 => [
                                'numFound' => 1,
                                'startOffset' => 6,
                                'endOffset' => 15,
                                'origFreq' => 0,
                                'suggestion' => [
                                    0 => [
                                        'word' => 'ultrasharp',
                                        'freq' => 1,
                                    ],
                                ],
                            ],
                        ],
                        'correctlySpelled' => false,
                        'collations' => [
                            'collation',
                            'dell ultrasharp',
                            'collation',
                            'dell ultrasharp new',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider providerParseSingleCollation
     *
     * @param mixed $data
     */
    public function testParseSingleCollation($data): void
    {
        $result = $this->parser->parse($this->query, $this->spellcheck, $data);
        $collations = $result->getCollations();
        $this->assertEquals('dell ultrasharp', $collations[0]->getQuery());

        $words = $result->getSuggestion(1)->getWords();
        $this->assertEquals(['word' => 'ultrasharpy', 'freq' => 1], $words[1]);
    }

    public static function providerParseSingleCollation(): array
    {
        return [
            'solr4' => [
                'data' => [
                    'spellcheck' => [
                        'suggestions' => [
                            0 => 'delll',
                            1 => [
                                'numFound' => 1,
                                'startOffset' => 0,
                                'endOffset' => 5,
                                'origFreq' => 0,
                                'suggestion' => [
                                    0 => 'dell',
                                ],
                            ],
                            2 => 'ultrashar',
                            3 => [
                                'numFound' => 1,
                                'startOffset' => 6,
                                'endOffset' => 15,
                                'origFreq' => 0,
                                'suggestion' => [
                                    0 => [
                                        'word' => 'ultrasharp',
                                        'freq' => 2,
                                    ],
                                    1 => [
                                        'word' => 'ultrasharpy',
                                        'freq' => 1,
                                    ],
                                ],
                            ],
                            4 => 'correctlySpelled',
                            5 => false,
                            6 => 'collation',
                            7 => 'dell ultrasharp',
                        ],
                    ],
                ],
            ],
            'solr5' => [
                'data' => [
                    'spellcheck' => [
                        'suggestions' => [
                            0 => 'delll',
                            1 => [
                                'numFound' => 1,
                                'startOffset' => 0,
                                'endOffset' => 5,
                                'origFreq' => 0,
                                'suggestion' => [
                                    0 => 'dell',
                                ],
                            ],
                            2 => 'ultrashar',
                            3 => [
                                'numFound' => 1,
                                'startOffset' => 6,
                                'endOffset' => 15,
                                'origFreq' => 0,
                                'suggestion' => [
                                    0 => [
                                        'word' => 'ultrasharp',
                                        'freq' => 2,
                                    ],
                                    1 => [
                                        'word' => 'ultrasharpy',
                                        'freq' => 1,
                                    ],
                                ],
                            ],
                        ],
                        'correctlySpelled' => false,
                        'collations' => [
                            'collation',
                            'dell ultrasharp',
                        ],
                    ],
                ],
            ],
        ];
    }

    public function testParseNoData(): void
    {
        $result = $this->parser->parse($this->query, $this->spellcheck, []);

        $this->assertNull($result);
    }
}

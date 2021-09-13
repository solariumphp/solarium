<?php

namespace Solarium\Tests\Component\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ResponseParser\Stats as Parser;
use Solarium\Exception\InvalidArgumentException;
use Solarium\QueryType\Select\Query\Query;

class StatsTest extends TestCase
{
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var Query
     */
    protected $query;

    public function setUp(): void
    {
        $this->parser = new Parser();
        $this->query = new Query();
    }

    public function testParse()
    {
        $data = [
            'stats' => [
                'stats_fields' => [
                    'fieldA' => [
                        'min' => 3.0,
                        'mean' => 'NaN',
                        'percentiles' => [
                            '50.0',
                            3.14,
                            '90.0',
                            42.0,
                        ],
                    ],
                    'fieldB' => [
                        'min' => 4.0,
                        'facets' => [
                            'fieldC' => [
                                'value1' => [
                                    'min' => 5.0,
                                    'mean' => 'NaN',
                                    'percentiles' => [
                                        '99.0',
                                        20.5,
                                        '99.9',
                                        20.9,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'fieldD' => [
                        'min' => '2005-08-01T16:30:25Z',
                        'mean' => '2006-01-15T12:49:38.727Z',
                    ],
                ],
            ],
        ];

        $result = $this->parser->parse($this->query, null, $data);

        $this->assertEquals(3.0, $result->getResult('fieldA')->getMin());
        $this->assertEquals(4.0, $result->getResult('fieldB')->getMin());
        $this->assertNan($result->getResult('fieldA')->getMean());

        $expectedPercentiles = [
            '50.0' => 3.14,
            '90.0' => 42.0,
        ];
        $this->assertSame($expectedPercentiles, $result->getResult('fieldA')->getPercentiles());

        $facets = $result->getResult('fieldB')->getFacets();
        $this->assertEquals(5.0, $facets['fieldC']['value1']->getMin());
        $this->assertNan($facets['fieldC']['value1']->getMean());

        $expectedPercentiles = [
            '99.0' => 20.5,
            '99.9' => 20.9,
        ];
        $this->assertSame($expectedPercentiles, $facets['fieldC']['value1']->getPercentiles());

        $this->assertEquals('2005-08-01T16:30:25Z', $result->getResult('fieldD')->getMin());
        $this->assertEquals('2006-01-15T12:49:38.727Z', $result->getResult('fieldD')->getMean());
    }

    public function testParseNoData()
    {
        $result = $this->parser->parse($this->query, null, []);
        $this->assertCount(0, $result);
    }

    public function testParseNoQuery()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A valid query object needs to be provided.');
        $this->parser->parse(null, null, []);
    }
}

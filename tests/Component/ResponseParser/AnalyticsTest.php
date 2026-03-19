<?php

declare(strict_types=1);

namespace Solarium\Tests\Component\ResponseParser;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Analytics\Analytics;
use Solarium\Component\Analytics\Facet\AbstractFacet;
use Solarium\Component\Analytics\Grouping;
use Solarium\Component\ResponseParser\Analytics as Parser;
use Solarium\Component\Result\Analytics\Expression;
use Solarium\Component\Result\Analytics\Grouping as ResultGrouping;
use Solarium\Component\Result\Analytics\Result as AnalyticsResult;
use Solarium\QueryType\Select\Query\Query;

/**
 * Analytics Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class AnalyticsTest extends TestCase
{
    protected Parser $parser;

    protected Query $query;

    public function setUp(): void
    {
        $this->parser = new Parser();
        $this->query = new Query();
    }

    public function testParseData(): void
    {
        $grouping = new Grouping([
            'key' => 'geo_sales',
            'expressions' => [
                'max_sale' => 'max(sale())',
                'med_sale' => 'median(sale())',
            ],
            'facets' => [
                [
                    'key' => 'category',
                    'type' => AbstractFacet::TYPE_PIVOT,
                    'pivots' => [
                        [
                            'name' => 'country',
                            'expression' => 'country',
                        ],
                        [
                            'name' => 'state',
                            'expression' => 'fillmissing(state, fillmissing(providence, territory))',
                        ],
                        [
                            'name' => 'city',
                            'expression' => 'fillmissing(city, \'N/A\')',
                        ],
                    ],
                ],
            ],
        ]);

        $component = new Analytics();
        $component
            ->addExpression('max_sale', 'max(sale())')
            ->addExpression('med_sale', 'median(sale())')
            ->addGrouping($grouping);

        $data = [
            'analytics_response' => [
                'results' => [
                    'max_sale' => 50.0,
                    'med_sale' => 31.0,
                ],
                'groupings' => [
                    'geo_sales' => [
                        'category' => [
                            [
                                'pivot' => 'country',
                                'value' => 'usa',
                                'results' => [
                                    'max_sale' => 103.75,
                                    'med_sale' => 15.5,
                                ],
                                'children' => [
                                    [
                                        'pivot' => 'state',
                                        'value' => 'texas',
                                        'results' => [
                                            'max_sale' => 99.2,
                                            'med_sale' => 20.35,
                                        ],
                                        'children' => [
                                            [
                                                'pivot' => 'city',
                                                'value' => 'austin',
                                                'results' => [
                                                    'max_sale' => 94.34,
                                                    'med_sale' => 17.60,
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $result = $this->parser->parse($this->query, $component, $data);

        $this->assertSameSize($result->getResults(), $result);
        $this->assertSameSize($result->getIterator(), $result);
        $this->assertArrayHasKey('geo_sales', $result->getGroupings());

        $this->assertInstanceOf(AnalyticsResult::class, $result);
        $expression = $result->getResult('max_sale');

        $this->assertInstanceOf(Expression::class, $expression);
        $this->assertEquals('max(sale())', $expression->getExpression());
        $this->assertEquals(50.0, $expression->getValue());

        $grouping = $result->getGrouping('geo_sales');

        $this->assertInstanceOf(ResultGrouping::class, $grouping);

        $facets = $grouping->getFacets('category');

        $this->assertCount(1, $facets);
        $this->assertSame('country', $facets[0]->getPivot());
        $this->assertSame('usa', $facets[0]->getValue());

        $this->assertSameSize($facets[0]->getResults(), $facets[0]);
        $this->assertSameSize($facets[0]->getIterator(), $facets[0]);

        $this->assertCount(1, $facets[0]->getChildren());
        $this->assertCount(1, $facets[0]->getChildren()[0]->getChildren());
    }

    public function testParseNoData(): void
    {
        $component = new Analytics();
        $component
            ->addExpression('max_sale', 'max(sale())')
            ->addExpression('med_sale', 'median(sale())');

        $result = $this->parser->parse($this->query, $component, []);

        $this->assertNull($result);
    }

    public function testNotReturnedExpressionsAndFacets(): void
    {
        $grouping = new Grouping([
            'key' => 'geo_sales',
            'expressions' => [
                'max_sale' => 'max(sale())',
                'med_sale' => 'median(sale())',
                'min_sale' => 'min(sale())',
            ],
            'facets' => [
                [
                    'key' => 'category',
                    'type' => AbstractFacet::TYPE_PIVOT,
                    'pivots' => [
                        [
                            'name' => 'country',
                            'expression' => 'country',
                        ],
                        [
                            'name' => 'state',
                            'expression' => 'fillmissing(state, fillmissing(providence, territory))',
                        ],
                        [
                            'name' => 'city',
                            'expression' => 'fillmissing(city, \'N/A\')',
                        ],
                    ],
                ],
                [
                    'key' => 'bar',
                    'type' => AbstractFacet::TYPE_VALUE,
                ],
            ],
        ]);

        $component = new Analytics();
        $component
            ->addExpression('max_sale', 'max(sale())')
            ->addExpression('med_sale', 'median(sale())')
            ->addExpression('min_sale', 'min(sale())')
            ->addGrouping($grouping)
            ->addGrouping(new Grouping(['key' => 'foo']))
        ;

        $data = [
            'analytics_response' => [
                'results' => [
                    'max_sale' => 50.0,
                    'med_sale' => 31.0,
                ],
                'groupings' => [
                    'geo_sales' => [
                        'category' => [
                            [
                                'pivot' => 'country',
                                'value' => 'usa',
                                'results' => [
                                    'max_sale' => 103.75,
                                    'med_sale' => 15.5,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $result = $this->parser->parse($this->query, $component, $data);

        $this->assertNull($result->getResult('min_sale'));
        $this->assertNull($result->getGrouping('foo'));

        $grouping = $result->getGrouping('geo_sales');
        $this->assertNull($grouping->getFacets('bar'));
    }
}

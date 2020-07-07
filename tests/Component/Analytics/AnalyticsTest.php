<?php

declare(strict_types=1);

namespace Solarium\Tests\Component\Analytics;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Analytics\Analytics;
use Solarium\Component\Analytics\Facet\AbstractFacet;
use Solarium\Component\Analytics\Facet\QueryFacet;
use Solarium\Component\Analytics\Facet\Sort\Criterion;
use Solarium\Component\Analytics\Facet\Sort\Sort;
use Solarium\Component\Analytics\Facet\ValueFacet;
use Solarium\Component\Analytics\Grouping;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\RequestBuilder\Analytics as RequestBuilder;
use Solarium\Component\ResponseParser\Analytics as ResponseParser;

/**
 * Analytics Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class AnalyticsTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testTypeAndRequestBuilder(): void
    {
        $component = new Analytics();

        $this->assertSame(ComponentAwareQueryInterface::COMPONENT_ANALYTICS, $component->getType());
        $this->assertInstanceOf(RequestBuilder::class, $component->getRequestBuilder());
        $this->assertInstanceOf(ResponseParser::class, $component->getResponseParser());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testFunctions(): void
    {
        $options = [
            'functions' => [
                'clothing_sale()' => 'filter(mult(price,quantity),equal(category,\'Clothing\'))',
            ],
        ];

        $component = new Analytics($options);

        $this->assertSame($options['functions'], $component->getFunctions());
        $this->assertArrayNotHasKey('expressions', $component->jsonSerialize());
        $this->assertArrayNotHasKey('groupings', $component->jsonSerialize());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testExpressions(): void
    {
        $options = [
            'functions' => [
                'clothing_sale()' => 'filter(mult(price,quantity),equal(category,\'Clothing\'))',
            ],
            'expressions' => [
                'max_clothing_sale' => 'max(clothing_sale())',
                'med_clothing_sale' => 'median(clothing_sale())',
            ],
            'groupings' => [],
        ];

        $component = new Analytics($options);

        $this->assertSame($options['expressions'], $component->getExpressions());
        $this->assertSame($options['functions'], $component->getFunctions());
        $this->assertArrayNotHasKey('groupings', $component->jsonSerialize());
    }

    public function testGroupings(): void
    {
        $facetOptions = [
            'key' => 'key',
            'expression' => 'fillmissing(category,\'No Category\')',
        ];

        $groupingOptions = [
            'key' => 'sales_numbers',
            'expressions' => [
                'max_sale' => 'max(sale())',
                'med_sale' => 'median(sale())',
            ],
            'facets' => [
                new ValueFacet($facetOptions),
            ],
        ];

        $options = [
            'functions' => [
                'sale()' => 'mult(price,quantity)',
            ],
            'groupings' => [
                new Grouping($groupingOptions),
            ],
        ];

        $component = new Analytics($options);

        $this->assertSame($options['functions'], $component->getFunctions());
        $this->assertArrayNotHasKey('expressions', $component->jsonSerialize());

        $grouping = $component->getGroupings()[$groupingOptions['key']];

        $this->assertSame($groupingOptions['key'], $grouping->getKey());
        $this->assertSame($groupingOptions['expressions'], $grouping->getExpressions());
        $this->assertSame($groupingOptions['facets'][0], $grouping->getFacets()[$facetOptions['key']]);
        $this->assertArrayNotHasKey('key', $grouping->jsonSerialize());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testMapArrayToObjects(): void
    {
        $options = [
            'functions' => [
                'clothing_sale()' => 'filter(mult(price,quantity),equal(category,\'Clothing\'))',
            ],
            'expressions' => [
                'max_clothing_sale' => 'max(clothing_sale())',
                'med_clothing_sale' => 'median(clothing_sale())',
            ],
            'groupings' => [
                [
                    'key' => 'sales',
                    'expressions' => [
                        'stddev_sale' => 'stddev(sale())',
                        'min_price' => 'min(price)',
                        'max_quantity' => 'max(quantity)',
                    ],
                    'facets' => [
                        [
                            'key' => 'category',
                            'type' => AbstractFacet::TYPE_VALUE,
                            'expression' => 'fill_missing(category, \'No Category\')',
                            'sort' => [
                                'criteria' => [
                                    [
                                        'type' => Criterion::TYPE_EXPRESSION,
                                        'expression' => 'min_price',
                                        'direction' => 'ascending',
                                    ],
                                    [
                                        'type' => Criterion::TYPE_VALUE,
                                        'direction' => 'descending',
                                    ],
                                ],
                                'limit' => 10,
                            ],
                        ],
                        [
                            'key' => 'temps',
                            'type' => AbstractFacet::TYPE_QUERY,
                            'queries' => [
                                'hot' => 'temp:[90 TO *]',
                                'cold' => 'temp:[* TO 50]',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $component = new Analytics($options);
        $grouping = $component->getGroupings()['sales'];

        $this->assertInstanceOf(Grouping::class, $grouping);

        $facets = $grouping->getFacets();

        $this->assertInstanceOf(ValueFacet::class, $facets['category']);
        $this->assertInstanceOf(QueryFacet::class, $facets['temps']);

        $sort = $facets['category']->getSort();

        $this->assertInstanceOf(Sort::class, $sort);
        $this->assertInstanceOf(Criterion::class, $sort->getCriteria()[0]);
    }
}

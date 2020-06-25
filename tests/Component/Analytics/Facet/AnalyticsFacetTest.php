<?php

declare(strict_types=1);

namespace Solarium\Tests\Component\Analytics\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Analytics\Facet\AbstractFacet;
use Solarium\Component\Analytics\Facet\Pivot;
use Solarium\Component\Analytics\Facet\PivotFacet;
use Solarium\Component\Analytics\Facet\QueryFacet;
use Solarium\Component\Analytics\Facet\RangeFacet;
use Solarium\Component\Analytics\Facet\Sort\Criterion;
use Solarium\Component\Analytics\Facet\Sort\Sort;
use Solarium\Component\Analytics\Facet\ValueFacet;

/**
 * Analytics Facet Test.
 *
 * @author wicliff <wicliff.wolda@gmail.com>
 */
class AnalyticsFacetTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testValueFacet(): void
    {
        $options = [
            'key' => 'key',
            'expression' => 'fillmissing(category,\'No Category\')',
            'sort' => null,
        ];

        $facet = new ValueFacet($options);

        $this->assertSame(AbstractFacet::TYPE_VALUE, $facet->getType());

        $this->assertSame($options['key'], $facet->getKey());
        $this->assertSame($options['expression'], $facet->getExpression());
        $this->assertNull($facet->getSort());
        $this->assertArrayNotHasKey('sort', $facet->jsonSerialize());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testRangeFacet(): void
    {
        $options = [
            'key' => 'key',
            'field' => 'price',
            'start' => 0,
            'end' => 100,
            'gap' => [5, 10, 10, 25],
            'hardend' => true,
            'include' => [RangeFacet::INCLUDE_LOWER, RangeFacet::INCLUDE_UPPER],
            'others' => [],
        ];

        $facet = new RangeFacet($options);

        $this->assertSame(AbstractFacet::TYPE_RANGE, $facet->getType());

        $this->assertSame($options['key'], $facet->getKey());
        $this->assertSame($options['field'], $facet->getField());
        $this->assertSame($options['start'], $facet->getStart());
        $this->assertSame($options['end'], $facet->getEnd());
        $this->assertSame($options['gap'], $facet->getGap());
        $this->assertTrue($facet->isHardend());
        $this->assertSame($options['include'], $facet->getInclude());
        $this->assertSame($options['others'], $facet->getOthers());
        $this->assertArrayNotHasKey('others', $facet->jsonSerialize());
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testQueryFacet(): void
    {
        $options = [
            'key' => 'key',
            'queries' => [
                'high_quantity' => 'quantity:[ 5 TO 14 ] AND price:[ 100 TO * ]',
                'low_quantity' => 'quantity:[ 1 TO 4 ] AND price:[ 100 TO * ]',
            ],
        ];

        $facet = new QueryFacet($options);

        $this->assertSame(AbstractFacet::TYPE_QUERY, $facet->getType());

        $this->assertSame($options['key'], $facet->getKey());
        $this->assertSame($options['queries'], $facet->getQueries());
        $this->assertArrayNotHasKey('key', $facet->jsonSerialize());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \PHPUnit\Framework\Exception
     */
    public function testPivotFacet(): void
    {
        $pivotOptions = [
            'name' => 'state',
            'expression' => 'fillmissing(state, fillmissing(providence, territory))',
            'sort' => null,
        ];

        $options = [
            'key' => 'key',
            'pivots' => [
                new Pivot($pivotOptions),
            ],
        ];

        $facet = new PivotFacet($options);

        $this->assertSame(AbstractFacet::TYPE_PIVOT, $facet->getType());
        $this->assertSame($options['key'], $facet->getKey());
        $this->assertArrayNotHasKey('key', $facet->jsonSerialize());

        $pivot = $facet->getPivots()[0];

        $this->assertSame($pivotOptions['name'], $pivot->getName());
        $this->assertSame($pivotOptions['expression'], $pivot->getExpression());
        $this->assertNull($pivot->getSort());
        $this->assertArrayNotHasKey('sort', $pivot->jsonSerialize());
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \PHPUnit\Framework\Exception
     */
    public function testSort(): void
    {
        $criterionOptions = [
            'type' => Criterion::TYPE_EXPRESSION,
            'expression' => 'max_sale',
            'direction' => Criterion::DIRECTION_ASCENDING,
        ];

        $options = [
            'limit' => null,
            'offset' => 5,
            'criteria' => [
                new Criterion($criterionOptions),
            ],
        ];

        $sort = new Sort($options);

        $this->assertNull($sort->getLimit());
        $this->assertSame($options['offset'], $sort->getOffset());
        $this->assertArrayNotHasKey('limit', $sort->jsonSerialize());

        $criterion = $sort->getCriteria()[0];

        $this->assertSame($criterionOptions['type'], $criterion->getType());
        $this->assertSame($criterionOptions['expression'], $criterion->getExpression());
        $this->assertSame($criterionOptions['direction'], $criterion->getDirection());

        unset($criterionOptions['direction']);

        $criterion = new Criterion($criterionOptions);
        $this->assertArrayNotHasKey('direction', $criterion->jsonSerialize());
    }
}

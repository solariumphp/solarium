<?php

namespace Solarium\Tests\Component\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\Pivot;
use Solarium\Component\FacetSet;

class PivotTest extends TestCase
{
    /**
     * @var Pivot
     */
    protected $facet;

    public function setUp(): void
    {
        $this->facet = new Pivot();
    }

    public function testConfigMode(): void
    {
        $options = [
            'fields' => ['abc', 'def'],
            'pivot.mincount' => 5,
            'limit' => 12,
            'offset' => 40,
            'sort' => 'index',
            'overrequest.count' => 20,
            'overrequest.ratio' => 2.5,
        ];

        $this->facet->setOptions($options);

        $this->assertSame($options['fields'], $this->facet->getFields());
        $this->assertSame($options['pivot.mincount'], $this->facet->getPivotMinCount());
        $this->assertSame($options['limit'], $this->facet->getLimit());
        $this->assertSame($options['offset'], $this->facet->getOffset());
        $this->assertSame($options['sort'], $this->facet->getSort());
        $this->assertSame($options['overrequest.count'], $this->facet->getOverrequestCount());
        $this->assertSame($options['overrequest.ratio'], $this->facet->getOverrequestRatio());
    }

    public function testGetType(): void
    {
        $this->assertSame(
            FacetSet::FACET_PIVOT,
            $this->facet->getType()
        );
    }

    /**
     * @deprecated
     */
    public function testSetAndGetMinCount(): void
    {
        $this->facet->setMinCount(5);
        $this->assertSame(5, $this->facet->getMinCount());
    }

    public function testSetAndGetPivotMinCount(): void
    {
        $this->facet->setPivotMinCount(5);
        $this->assertSame(5, $this->facet->getPivotMinCount());
    }

    public function testSetAndGetLimit(): void
    {
        $this->facet->setLimit(12);
        $this->assertSame(12, $this->facet->getLimit());
    }

    public function testSetAndGetOffset(): void
    {
        $this->facet->setOffset(40);
        $this->assertSame(40, $this->facet->getOffset());
    }

    public function testSetAndGetSort(): void
    {
        $this->facet->setSort('index');
        $this->assertSame('index', $this->facet->getSort());
    }

    public function testSetAndGetOverrequestCount(): void
    {
        $this->facet->setOverrequestCount(20);
        $this->assertSame(20, $this->facet->getOverrequestCount());
    }

    public function testSetAndGetOverrequestRatio(): void
    {
        $this->facet->setOverrequestRatio(2.5);
        $this->assertSame(2.5, $this->facet->getOverrequestRatio());
    }

    public function testAddField(): void
    {
        $expectedFields = $this->facet->getFields();
        $expectedFields[] = 'newfield';
        $this->facet->addField('newfield');
        $this->assertSame($expectedFields, $this->facet->getFields());
    }

    public function testClearFields(): void
    {
        $this->facet->addField('newfield');
        $this->facet->clearFields();
        $this->assertSame([], $this->facet->getFields());
    }

    public function testAddFields(): void
    {
        $fields = ['field1', 'field2'];

        $this->facet->clearFields();
        $this->facet->addFields($fields);
        $this->assertSame($fields, $this->facet->getFields());
    }

    public function testAddFieldsAsStringWithTrim(): void
    {
        $this->facet->clearFields();
        $this->facet->addFields('field1, field2');
        $this->assertSame(['field1', 'field2'], $this->facet->getFields());
    }

    public function testRemoveField(): void
    {
        $this->facet->clearFields();
        $this->facet->addFields(['field1', 'field2']);
        $this->facet->removeField('field1');
        $this->assertSame(['field2'], $this->facet->getFields());
    }

    public function testSetFields(): void
    {
        $this->facet->clearFields();
        $this->facet->addFields(['field1', 'field2']);
        $this->facet->setFields(['field3', 'field4']);
        $this->assertSame(['field3', 'field4'], $this->facet->getFields());
    }

    public function testAddStat(): void
    {
        $expectedStats = $this->facet->getLocalParameters()->getStats();
        $expectedStats[] = 'newstat';
        $this->facet->addStat('newstat');
        $this->assertSame($expectedStats, $this->facet->getStats());
        $this->assertSame($expectedStats, $this->facet->getLocalParameters()->getStats());
    }

    public function testClearStats(): void
    {
        $this->facet->addStat('newstat');
        $this->facet->clearStats();
        $this->assertSame([], $this->facet->getStats());
        $this->assertSame([], $this->facet->getLocalParameters()->getStats());
    }

    public function testAddStats(): void
    {
        $stats = ['stat1', 'stat2'];

        $this->facet->clearStats();
        $this->facet->addStats($stats);
        $this->assertSame($stats, $this->facet->getStats());
        $this->assertSame($stats, $this->facet->getLocalParameters()->getStats());
    }

    public function testAddStatsAsString(): void
    {
        $this->facet->clearStats();
        $this->facet->addStats('stat1, stat2');
        $this->assertSame(['stat1', 'stat2'], $this->facet->getStats());
        $this->assertSame(['stat1', 'stat2'], $this->facet->getLocalParameters()->getStats());
    }

    public function testRemoveStat(): void
    {
        $this->facet->clearStats();
        $this->facet->addStats(['stat1', 'stat2']);
        $this->facet->removeStat('stat1');
        $this->assertSame(['stat2'], $this->facet->getStats());
        $this->assertSame(['stat2'], $this->facet->getLocalParameters()->getStats());
    }

    public function testSetStats(): void
    {
        $this->facet->clearStats();
        $this->facet->setStats(['stat1', 'stat2']);
        $this->facet->setStats(['stat3', 'stat4']);
        $this->assertSame(['stat3', 'stat4'], $this->facet->getStats());
        $this->assertSame(['stat3', 'stat4'], $this->facet->getLocalParameters()->getStats());
    }
}

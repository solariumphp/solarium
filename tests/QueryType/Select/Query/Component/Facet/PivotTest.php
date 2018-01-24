<?php

namespace Solarium\Tests\QueryType\Select\Query\Component\Facet;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Facet\Pivot;
use Solarium\Component\FacetSet;

class PivotTest extends TestCase
{
    /**
     * @var Pivot
     */
    protected $facet;

    public function setUp()
    {
        $this->facet = new Pivot();
    }

    public function testConfigMode()
    {
        $options = array(
            'fields' => array('abc', 'def'),
            'mincount' => 5,
        );

        $this->facet->setOptions($options);

        $this->assertSame($options['fields'], $this->facet->getFields());
        $this->assertSame($options['mincount'], $this->facet->getMinCount());
    }

    public function testGetType()
    {
        $this->assertSame(
            FacetSet::FACET_PIVOT,
            $this->facet->getType()
        );
    }

    public function testSetMinCount()
    {
        $this->facet->setMinCount(5);

        $this->assertSame(5, $this->facet->getMinCount());
    }

    public function testAddField()
    {
        $expectedFields = $this->facet->getFields();
        $expectedFields[] = 'newfield';
        $this->facet->addField('newfield');
        $this->assertSame($expectedFields, $this->facet->getFields());
    }

    public function testClearFields()
    {
        $this->facet->addField('newfield');
        $this->facet->clearFields();
        $this->assertSame(array(), $this->facet->getFields());
    }

    public function testAddFields()
    {
        $fields = array('field1', 'field2');

        $this->facet->clearFields();
        $this->facet->addFields($fields);
        $this->assertSame($fields, $this->facet->getFields());
    }

    public function testAddFieldsAsStringWithTrim()
    {
        $this->facet->clearFields();
        $this->facet->addFields('field1, field2');
        $this->assertSame(array('field1', 'field2'), $this->facet->getFields());
    }

    public function testRemoveField()
    {
        $this->facet->clearFields();
        $this->facet->addFields(array('field1', 'field2'));
        $this->facet->removeField('field1');
        $this->assertSame(array('field2'), $this->facet->getFields());
    }

    public function testSetFields()
    {
        $this->facet->clearFields();
        $this->facet->addFields(array('field1', 'field2'));
        $this->facet->setFields(array('field3', 'field4'));
        $this->assertSame(array('field3', 'field4'), $this->facet->getFields());
    }

    public function testAddStat()
    {
        $expectedStats = $this->facet->getStats();
        $expectedStats[] = 'newstat';
        $this->facet->addStat('newstat');
        $this->assertSame($expectedStats, $this->facet->getStats());
    }

    public function testClearStats()
    {
        $this->facet->addStat('newstat');
        $this->facet->clearStats();
        $this->assertSame(array(), $this->facet->getStats());
    }

    public function testAddStats()
    {
        $stats = array('stat1', 'stat2');

        $this->facet->clearStats();
        $this->facet->addStats($stats);
        $this->assertSame($stats, $this->facet->getStats());
    }

    public function testAddStatsAsStringWithTrim()
    {
        $this->facet->clearStats();
        $this->facet->addStats('stat1, stat2');
        $this->assertSame(array('stat1', 'stat2'), $this->facet->getStats());
    }

    public function testRemoveStat()
    {
        $this->facet->clearStats();
        $this->facet->addStats(array('stat1', 'stat2'));
        $this->facet->removeStat('stat1');
        $this->assertSame(array('stat2'), $this->facet->getStats());
    }

    public function testSetStats()
    {
        $this->facet->clearStats();
        $this->facet->addStats(array('stat1', 'stat2'));
        $this->facet->setStats(array('stat3', 'stat4'));
        $this->assertSame(array('stat3', 'stat4'), $this->facet->getStats());
    }
}

<?php

namespace Solarium\Tests\Plugin\MinimumScoreFilter;

use Solarium\Component\Grouping;
use Solarium\Plugin\MinimumScoreFilter\Query;
use Solarium\Plugin\MinimumScoreFilter\QueryGroupResult;
use Solarium\Plugin\MinimumScoreFilter\ValueGroupResult;
use Solarium\Tests\QueryType\Select\Query\AbstractQueryTestCase;

class QueryTest extends AbstractQueryTestCase
{
    public function setUp(): void
    {
        $this->query = new Query();
    }

    public function testSetAndGetFilterMode()
    {
        $this->query->setFilterMode(Query::FILTER_MODE_MARK);
        $this->assertSame(Query::FILTER_MODE_MARK, $this->query->getFilterMode());
    }

    public function testSetAndGetFilterRatio()
    {
        $this->query->setFilterRatio(0.345);
        $this->assertSame(0.345, $this->query->getFilterRatio());
    }

    public function testClearFields()
    {
        $this->query->addField('newfield');
        $this->query->clearFields();
        $this->assertSame(['score'], $this->query->getFields());
    }

    public function testSetAndGetResultClass()
    {
        $this->query->setResultClass('MyResult');
        $this->assertSame('MyResult', $this->query->getResultClass());
    }

    public function testAddFields()
    {
        $this->query->clearFields();
        $this->query->addFields(['field1', 'field2']);
        $this->assertSame(['field1', 'field2', 'score'], $this->query->getFields());
    }

    public function testRemoveField()
    {
        $this->query->clearFields();
        $this->query->addFields(['field1', 'field2']);
        $this->query->removeField('field1');
        $this->assertSame(['field2', 'score'], $this->query->getFields());
    }

    public function testSetFields()
    {
        $this->query->clearFields();
        $this->query->addFields(['field1', 'field2']);
        $this->query->setFields(['field3', 'field4']);
        $this->assertSame(['field3', 'field4', 'score'], $this->query->getFields());
    }

    public function testAddFieldsAsStringWithTrim()
    {
        $this->query->clearFields();
        $this->query->addFields('field1, field2');
        $this->assertSame(['field1', 'field2', 'score'], $this->query->getFields());
    }

    public function testGetComponentsWithGrouping()
    {
        /** @var Grouping|MockObject $mockComponent */
        $mock = $this->getMockBuilder(Grouping::class)
            ->onlyMethods(['setOption'])
            ->getMock();

        $mock->expects($this->exactly(2))
            ->method('setOption')
            ->with(
                $this->callback(function (string $option): bool {
                    static $i = 0;
                    static $options = ['resultquerygroupclass', 'resultvaluegroupclass'];

                    return $options[$i++] === $option;
                }),
                $this->callback(function (string $className): bool {
                    static $j = 0;
                    static $classNames = [QueryGroupResult::class, ValueGroupResult::class];

                    return $classNames[$j++] === $className;
                })
            );

        $this->query->setComponent(Query::COMPONENT_GROUPING, $mock);
        $this->query->getComponents();
    }
}

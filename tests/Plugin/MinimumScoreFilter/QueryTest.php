<?php

namespace Solarium\Tests\Plugin\MinimumScoreFilter;

use Solarium\Plugin\MinimumScoreFilter\Query;
use Solarium\Tests\QueryType\Select\Query\AbstractQueryTest;

class QueryTest extends AbstractQueryTest
{
    public function setUp()
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
        // Should be ignored
        $this->query->setResultClass('MyResult');
        $this->assertSame('Solarium\Plugin\MinimumScoreFilter\Result', $this->query->getResultClass());
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
}

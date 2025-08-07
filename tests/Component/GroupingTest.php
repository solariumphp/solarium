<?php

namespace Solarium\Tests\Component;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\Grouping;
use Solarium\QueryType\Select\Query\Query;

class GroupingTest extends TestCase
{
    /**
     * @var Grouping
     */
    protected $grouping;

    public function setUp(): void
    {
        $this->grouping = new Grouping();
        $this->grouping->setQueryInstance(new Query());
    }

    public function testConfigMode(): void
    {
        $options = [
            'fields' => 'field1,field2',
            'queries' => ['price:[* TO 100]', 'price:[101 TO *]'],
            'limit' => 10,
            'offset' => 30,
            'sort' => 'sortfield desc',
            'mainresult' => false,
            'numberofgroups' => true,
            'cachepercentage' => 25,
            'truncate' => false,
            'function' => 'myfunc()',
            'facet' => true,
            'format' => Grouping::FORMAT_GROUPED,
            'resultquerygroupclass' => 'MyQueryGroupClass',
            'resultvaluegroupclass' => 'MyValueGroupClass',
        ];

        $this->grouping->setOptions($options);

        $this->assertSame(['field1', 'field2'], $this->grouping->getFields());
        $this->assertSame(['price:[* TO 100]', 'price:[101 TO *]'], $this->grouping->getQueries());
        $this->assertSame(10, $this->grouping->getLimit());
        $this->assertSame(30, $this->grouping->getOffset());
        $this->assertSame('sortfield desc', $this->grouping->getSort());
        $this->assertFalse($this->grouping->getMainResult());
        $this->assertTrue($this->grouping->getNumberOfGroups());
        $this->assertSame(25, $this->grouping->getCachePercentage());
        $this->assertFalse($this->grouping->getTruncate());
        $this->assertSame('myfunc()', $this->grouping->getFunction());
        $this->assertTrue($this->grouping->getFacet());
        $this->assertSame('grouped', $this->grouping->getFormat());
        $this->assertSame('MyQueryGroupClass', $this->grouping->getResultQueryGroupClass());
        $this->assertSame('MyValueGroupClass', $this->grouping->getResultValueGroupClass());
    }

    public function testGetType(): void
    {
        $this->assertEquals(ComponentAwareQueryInterface::COMPONENT_GROUPING, $this->grouping->getType());
    }

    public function testGetResponseParser(): void
    {
        $this->assertInstanceOf(
            'Solarium\Component\ResponseParser\Grouping',
            $this->grouping->getResponseParser()
        );
    }

    public function testGetRequestBuilder(): void
    {
        $this->assertInstanceOf(
            'Solarium\Component\RequestBuilder\Grouping',
            $this->grouping->getRequestBuilder()
        );
    }

    public function testAddField(): void
    {
        $this->grouping->addField('field1');
        $this->grouping->addField('field2');
        $this->assertSame(['field1', 'field2'], $this->grouping->getFields());
    }

    public function testAddFields(): void
    {
        $this->grouping->addFields(['field1', 'field2']);
        $this->assertSame(['field1', 'field2'], $this->grouping->getFields());
    }

    public function testAddFieldsAsStringWithTrim(): void
    {
        $this->grouping->addFields('field1, field2');
        $this->assertSame(['field1', 'field2'], $this->grouping->getFields());
    }

    public function testClearFields(): void
    {
        $this->grouping->addFields(['field1', 'field2']);
        $this->grouping->clearFields();
        $this->assertSame([], $this->grouping->getFields());
    }

    public function testSetFields(): void
    {
        $this->grouping->addFields(['field1', 'field2']);
        $this->grouping->setFields(['field3', 'field4']);
        $this->assertSame(['field3', 'field4'], $this->grouping->getFields());
    }

    public function testAddQuery(): void
    {
        $this->grouping->addQuery('cat:A');
        $this->grouping->addQuery('cat:B');
        $this->assertSame(['cat:A', 'cat:B'], $this->grouping->getQueries());
    }

    public function testAddQueries(): void
    {
        $this->grouping->addQueries(['cat:A', 'cat:B']);
        $this->assertSame(['cat:A', 'cat:B'], $this->grouping->getQueries());
    }

    public function testAddQueriesAsString(): void
    {
        $this->grouping->addQueries('cat:A');
        $this->assertSame(['cat:A'], $this->grouping->getQueries());
    }

    public function testClearQueries(): void
    {
        $this->grouping->addQueries(['cat:A', 'cat:B']);
        $this->grouping->clearQueries();
        $this->assertSame([], $this->grouping->getQueries());
    }

    public function testSetQueries(): void
    {
        $this->grouping->addQueries(['cat:A', 'cat:B']);
        $this->grouping->setQueries(['cat:C', 'cat:D']);
        $this->assertSame(['cat:C', 'cat:D'], $this->grouping->getQueries());
    }

    public function testSetAndGetLimit(): void
    {
        $this->grouping->setLimit(5);
        $this->assertSame(5, $this->grouping->getLimit());
    }

    public function testSetAndGetOffset(): void
    {
        $this->grouping->setOffset(20);
        $this->assertSame(20, $this->grouping->getOffset());
    }

    public function testSetAndGetSort(): void
    {
        $this->grouping->setSort('sortfield asc');
        $this->assertSame('sortfield asc', $this->grouping->getSort());
    }

    public function testSetAndGetMainResult(): void
    {
        $this->grouping->setMainResult(true);
        $this->assertTrue($this->grouping->getMainResult());
    }

    public function testSetAndGetNumberOfGroups(): void
    {
        $this->grouping->setNumberOfGroups(true);
        $this->assertTrue($this->grouping->getNumberOfGroups());
    }

    public function testSetAndGetCachePercentage(): void
    {
        $this->grouping->setCachePercentage(50);
        $this->assertSame(50, $this->grouping->getCachePercentage());
    }

    public function testSetAndGetTruncate(): void
    {
        $this->grouping->setTruncate(true);
        $this->assertTrue($this->grouping->getTruncate());
    }

    public function testSetAndGetFunction(): void
    {
        $this->grouping->setFunction('myfunc()');
        $this->assertSame('myfunc()', $this->grouping->getFunction());
    }

    public function testSetAndGetFacet(): void
    {
        $this->grouping->setFacet(true);
        $this->assertTrue($this->grouping->getFacet());
    }

    public function testSetAndGetFormat(): void
    {
        $this->grouping->setFormat(Grouping::FORMAT_SIMPLE);
        $this->assertSame('simple', $this->grouping->getFormat());
    }

    public function testSetAndGetResultQueryGroupClass(): void
    {
        $this->grouping->setResultQueryGroupClass('MyQueryGroupClass');
        $this->assertSame('MyQueryGroupClass', $this->grouping->getResultQueryGroupClass());
    }

    public function testSetAndGetResultQueryValueClass(): void
    {
        $this->grouping->setResultValueGroupClass('MyValueGroupClass');
        $this->assertSame('MyValueGroupClass', $this->grouping->getResultValueGroupClass());
    }
}

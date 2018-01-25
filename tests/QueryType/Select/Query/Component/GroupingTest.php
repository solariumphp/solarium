<?php

namespace Solarium\Tests\QueryType\Select\Query\Component;

use PHPUnit\Framework\TestCase;
use Solarium\Component\Grouping;
use Solarium\QueryType\Select\Query\Query;

class GroupingTest extends TestCase
{
    /**
     * @var Grouping
     */
    protected $grouping;

    public function setUp()
    {
        $this->grouping = new Grouping();
    }

    public function testConfigMode()
    {
        $options = [
            'fields' => ['fieldA', 'fieldB'],
            'queries' => ['cat:3', 'cat:4'],
            'limit' => 8,
            'offset' => 1,
            'sort' => 'score desc',
            'mainresult' => false,
            'numberofgroups' => true,
            'cachepercentage' => 45,
            'truncate' => true,
            'function' => 'log(foo)',
            'format' => 'grouped',
            'facet' => 'true',
        ];

        $this->grouping->setOptions($options);

        $this->assertSame($options['fields'], $this->grouping->getFields());
        $this->assertSame($options['queries'], $this->grouping->getQueries());
        $this->assertSame($options['limit'], $this->grouping->getLimit());
        $this->assertSame($options['offset'], $this->grouping->getOffset());
        $this->assertSame($options['sort'], $this->grouping->getSort());
        $this->assertSame($options['mainresult'], $this->grouping->getMainResult());
        $this->assertSame($options['numberofgroups'], $this->grouping->getNumberOfGroups());
        $this->assertSame($options['cachepercentage'], $this->grouping->getCachePercentage());
        $this->assertSame($options['truncate'], $this->grouping->getTruncate());
        $this->assertSame($options['function'], $this->grouping->getFunction());
        $this->assertSame($options['format'], $this->grouping->getFormat());
        $this->assertSame($options['facet'], $this->grouping->getFacet());
    }

    public function testGetType()
    {
        $this->assertSame(Query::COMPONENT_GROUPING, $this->grouping->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(
            'Solarium\Component\ResponseParser\Grouping',
            $this->grouping->getResponseParser()
        );
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\Component\RequestBuilder\Grouping',
            $this->grouping->getRequestBuilder()
        );
    }

    public function testSetAndGetResultQueryGroupClass()
    {
        $value = 'classX';
        $this->grouping->setResultQueryGroupClass($value);

        $this->assertSame(
            $value,
            $this->grouping->getResultQueryGroupClass()
        );
    }

    public function testSetAndGetResultValueGroupClass()
    {
        $value = 'classY';
        $this->grouping->setResultValueGroupClass($value);

        $this->assertSame(
            $value,
            $this->grouping->getResultValueGroupClass()
        );
    }

    public function testSetAndGetFieldsSingle()
    {
        $value = 'fieldC';
        $this->grouping->setFields($value);

        $this->assertSame(
            [$value],
            $this->grouping->getFields()
        );
    }

    public function testSetAndGetFieldsCommaSeparated()
    {
        $value = 'fieldD, fieldE';
        $this->grouping->setFields($value);

        $this->assertSame(
            [
                'fieldD',
                'fieldE',
            ],
            $this->grouping->getFields()
        );
    }

    public function testSetAndGetFieldsArray()
    {
        $values = ['fieldD', 'fieldE'];
        $this->grouping->setFields($values);

        $this->assertSame(
            $values,
            $this->grouping->getFields()
        );
    }

    public function testSetAndGetQueriesSingle()
    {
        $value = 'cat:3';
        $this->grouping->setQueries($value);

        $this->assertSame(
            [$value],
            $this->grouping->getQueries()
        );
    }

    public function testSetAndGetQueriesArray()
    {
        $values = ['cat:5', 'cat:6'];
        $this->grouping->setQueries($values);

        $this->assertSame(
            $values,
            $this->grouping->getQueries()
        );
    }

    public function testSetAndGetLimit()
    {
        $value = '12';
        $this->grouping->setLimit($value);

        $this->assertSame(
            $value,
            $this->grouping->getLimit()
        );
    }

    public function testSetAndGetOffset()
    {
        $value = '2';
        $this->grouping->setOffset($value);

        $this->assertSame(
            $value,
            $this->grouping->getOffset()
        );
    }

    public function testSetAndGetSort()
    {
        $value = 'price desc';
        $this->grouping->setSort($value);

        $this->assertSame(
            $value,
            $this->grouping->getSort()
        );
    }

    public function testSetAndGetMainResult()
    {
        $value = true;
        $this->grouping->setMainResult($value);

        $this->assertSame(
            $value,
            $this->grouping->getMainResult()
        );
    }

    public function testSetAndGetNumberOfGroups()
    {
        $value = true;
        $this->grouping->setNumberOfGroups($value);

        $this->assertSame(
            $value,
            $this->grouping->getNumberOfGroups()
        );
    }

    public function testSetAndGetCachePercentage()
    {
        $value = 40;
        $this->grouping->setCachePercentage($value);

        $this->assertSame(
            $value,
            $this->grouping->getCachePercentage()
        );
    }

    public function testSetAndGetTruncate()
    {
        $value = true;
        $this->grouping->setTruncate($value);

        $this->assertSame(
            $value,
            $this->grouping->getTruncate()
        );
    }

    public function testSetAndGetFunction()
    {
        $value = 'log(foo)';
        $this->grouping->setFunction($value);

        $this->assertSame(
            $value,
            $this->grouping->getFunction()
        );
    }

    public function testSetAndGetFormat()
    {
        $value = 'grouped';
        $this->grouping->setFormat($value);

        $this->assertSame(
            $value,
            $this->grouping->getFormat()
        );
    }

    public function testSetAndGetFacet()
    {
        $value = true;
        $this->grouping->setFacet($value);

        $this->assertSame(
            $value,
            $this->grouping->getFacet()
        );
    }
}

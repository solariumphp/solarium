<?php

namespace Solarium\Tests\QueryType\Select\Query;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Select\Query\FilterQuery;

class FilterQueryTest extends TestCase
{
    protected $filterQuery;

    public function setUp()
    {
        $this->filterQuery = new FilterQuery();
    }

    public function testConfigMode()
    {
        $fq = new FilterQuery(array('tag' => array('t1', 't2'), 'key' => 'k1', 'query' => 'id:[10 TO 20]'));

        $this->assertSame(array('t1', 't2'), $fq->getTags());
        $this->assertSame('k1', $fq->getKey());
        $this->assertSame('id:[10 TO 20]', $fq->getQuery());
    }

    public function testConfigModeWithSingleValueTag()
    {
        $fq = new FilterQuery(array('tag' => 't1', 'key' => 'k1', 'query' => 'id:[10 TO 20]'));

        $this->assertSame(array('t1'), $fq->getTags());
        $this->assertSame('k1', $fq->getKey());
        $this->assertSame('id:[10 TO 20]', $fq->getQuery());
    }

    public function testSetAndGetKey()
    {
        $this->filterQuery->setKey('testkey');
        $this->assertSame('testkey', $this->filterQuery->getKey());
    }

    public function testSetAndGetQuery()
    {
        $this->filterQuery->setQuery('category:1');
        $this->assertSame('category:1', $this->filterQuery->getQuery());
    }

    public function testSetAndGetQueryWithBind()
    {
        $this->filterQuery->setQuery('id:%1%', array(678));
        $this->assertSame('id:678', $this->filterQuery->getQuery());
    }

    public function testAddTag()
    {
        $this->filterQuery->addTag('testtag');
        $this->assertSame(array('testtag'), $this->filterQuery->getTags());
    }

    public function testAddTags()
    {
        $this->filterQuery->addTags(array('t1', 't2'));
        $this->assertSame(array('t1', 't2'), $this->filterQuery->getTags());
    }

    public function testRemoveTag()
    {
        $this->filterQuery->addTags(array('t1', 't2'));
        $this->filterQuery->removeTag('t1');
        $this->assertSame(array('t2'), $this->filterQuery->getTags());
    }

    public function testClearTags()
    {
        $this->filterQuery->addTags(array('t1', 't2'));
        $this->filterQuery->clearTags();
        $this->assertSame(array(), $this->filterQuery->getTags());
    }

    public function testSetTags()
    {
        $this->filterQuery->addTags(array('t1', 't2'));
        $this->filterQuery->setTags(array('t3', 't4'));
        $this->assertSame(array('t3', 't4'), $this->filterQuery->getTags());
    }
}

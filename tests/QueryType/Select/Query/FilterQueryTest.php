<?php

namespace Solarium\Tests\QueryType\Select\Query;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Select\Query\FilterQuery;

class FilterQueryTest extends TestCase
{
    protected $filterQuery;

    public function setUp(): void
    {
        $this->filterQuery = new FilterQuery();
    }

    public function testConfigMode()
    {
        $fq = new FilterQuery(['local_tag' => ['t1', 't2'], 'key' => 'k1', 'query' => 'id:[10 TO 20]']);

        $this->assertSame(['t1', 't2'], $fq->getTags());
        $this->assertSame('k1', $fq->getKey());
        $this->assertSame('id:[10 TO 20]', $fq->getQuery());
    }

    public function testConfigModeWithSingleValueTag()
    {
        $fq = new FilterQuery(['local_tag' => 't1', 'key' => 'k1', 'query' => 'id:[10 TO 20]']);

        $this->assertSame(['t1'], $fq->getTags());
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
        $this->filterQuery->setQuery('id:%1%', [678]);
        $this->assertSame('id:678', $this->filterQuery->getQuery());
    }

    public function testAddTag()
    {
        $this->filterQuery->addTag('testtag');
        $this->assertSame(['testtag'], $this->filterQuery->getTags());
    }

    public function testAddTags()
    {
        $this->filterQuery->addTags(['t1', 't2']);
        $this->assertSame(['t1', 't2'], $this->filterQuery->getTags());
    }

    public function testRemoveTag()
    {
        $this->filterQuery->addTags(['t1', 't2']);
        $this->filterQuery->removeTag('t1');
        $this->assertSame(['t2'], $this->filterQuery->getTags());
    }

    public function testClearTags()
    {
        $this->filterQuery->addTags(['t1', 't2']);
        $this->filterQuery->clearTags();
        $this->assertSame([], $this->filterQuery->getTags());
    }

    public function testSetTags()
    {
        $this->filterQuery->addTags(['t1', 't2']);
        $this->filterQuery->setTags(['t3', 't4']);
        $this->assertSame(['t3', 't4'], $this->filterQuery->getTags());
    }

    public function testSetAndGetCache()
    {
        $this->assertTrue($this->filterQuery->getCache());
        $this->filterQuery->setCache(false);
        $this->assertFalse($this->filterQuery->getCache());
        $this->filterQuery->setCache(true);
        $this->assertTrue($this->filterQuery->getCache());
    }

    public function testSetAndGetCost()
    {
        $this->assertSame(0, $this->filterQuery->getCost());
        $this->filterQuery->setCost(123);
        $this->assertSame(123, $this->filterQuery->getCost());
        $this->filterQuery->setCost(99);
        $this->assertSame(99, $this->filterQuery->getCost());
    }
}

<?php

namespace Solarium\Tests\QueryType\Analysis\Result;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Analysis\Result\Types;

class TypesTest extends TestCase
{
    /**
     * @var Types
     */
    protected $result;

    protected $items;

    protected $name;

    public function setUp()
    {
        $this->name = 'testname';
        $this->items = [
            'index' => new TestAnalysisTypeIndexDummy(),
            'query' => new TestAnalysisTypeQueryDummy(),
        ];
        $this->result = new Types($this->name, $this->items);
    }

    public function testGetItems()
    {
        $this->assertSame($this->items, $this->result->getItems());
    }

    public function testCount()
    {
        $this->assertSame(count($this->items), count($this->result));
    }

    public function testIterator()
    {
        $lists = [];
        foreach ($this->result as $key => $list) {
            $lists[$key] = $list;
        }

        $this->assertSame($this->items, $lists);
    }

    public function testGetName()
    {
        $this->assertSame(
            $this->name,
            $this->result->getName()
        );
    }

    public function testGetIndexAnalysis()
    {
        $this->assertSame(
            $this->items['index'],
            $this->result->getIndexAnalysis()
        );
    }

    public function testGetIndexAnalysisNoData()
    {
        $items = [
            'index' => new TestAnalysisTypeInvalidDummy(),
            'query' => new TestAnalysisTypeQueryDummy(),
        ];

        $result = new Types($this->name, $items);
        $this->assertNull(
            $result->getIndexAnalysis()
        );
    }

    public function testGetQueryAnalysis()
    {
        $this->assertSame(
            $this->items['query'],
            $this->result->getQueryAnalysis()
        );
    }

    public function testGetQueryAnalysisNoData()
    {
        $items = [
            'index' => new TestAnalysisTypeIndexDummy(),
            'query' => new TestAnalysisTypeInvalidDummy(),
        ];

        $result = new Types($this->name, $items);
        $this->assertNull(
            $result->getQueryAnalysis()
        );
    }
}

class TestAnalysisTypeIndexDummy
{
    public function getName()
    {
        return 'index';
    }
}

class TestAnalysisTypeQueryDummy
{
    public function getName()
    {
        return 'query';
    }
}

class TestAnalysisTypeInvalidDummy
{
    public function getName()
    {
        return 'invalid';
    }
}

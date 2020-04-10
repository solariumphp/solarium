<?php

namespace Solarium\Tests\QueryType\Analysis\Result;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Analysis\Result\ResultList;
use Solarium\QueryType\Analysis\Result\Types;

class TypesTest extends TestCase
{
    /**
     * @var Types
     */
    protected $result;

    protected $items;

    protected $name;

    public function setUp(): void
    {
        $this->name = 'testname';
        $this->items = [
            'index' => new TestAnalysisTypeValidDummy('index', []),
            'query' => new TestAnalysisTypeValidDummy('query', []),
        ];
        $this->result = new Types($this->name, $this->items);
    }

    public function testGetItems()
    {
        $this->assertSame($this->items, $this->result->getItems());
    }

    public function testCount()
    {
        $this->assertCount(count($this->items), $this->result);
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
            'query' => new TestAnalysisTypeValidDummy('query', []),
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
            'index' => new TestAnalysisTypeValidDummy('index', []),
            'query' => new TestAnalysisTypeInvalidDummy(),
        ];

        $result = new Types($this->name, $items);
        $this->assertNull(
            $result->getQueryAnalysis()
        );
    }
}

class TestAnalysisTypeValidDummy extends ResultList
{
}

class TestAnalysisTypeInvalidDummy
{
    public function getName(): string
    {
        return 'invalid';
    }
}

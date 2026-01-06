<?php

namespace Solarium\Tests\QueryType\Analysis\Result;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Analysis\Result\ResultList;
use Solarium\QueryType\Analysis\Result\Types;

class TypesTest extends TestCase
{
    protected Types $result;

    /**
     * @var ResultList[]
     */
    protected array $items;

    protected string $name;

    public function setUp(): void
    {
        $this->name = 'testname';
        $this->items = [
            'index' => new TestAnalysisTypeValidDummy('index', []),
            'query' => new TestAnalysisTypeValidDummy('query', []),
        ];
        $this->result = new Types($this->name, $this->items);
    }

    public function testGetItems(): void
    {
        $this->assertSame($this->items, $this->result->getItems());
    }

    public function testCount(): void
    {
        $this->assertSameSize($this->items, $this->result);
    }

    public function testIterator(): void
    {
        $lists = [];
        foreach ($this->result as $key => $list) {
            $lists[$key] = $list;
        }

        $this->assertSame($this->items, $lists);
    }

    public function testGetName(): void
    {
        $this->assertSame(
            $this->name,
            $this->result->getName()
        );
    }

    public function testGetIndexAnalysis(): void
    {
        $this->assertSame(
            $this->items['index'],
            $this->result->getIndexAnalysis()
        );
    }

    public function testGetIndexAnalysisNoData(): void
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

    public function testGetQueryAnalysis(): void
    {
        $this->assertSame(
            $this->items['query'],
            $this->result->getQueryAnalysis()
        );
    }

    public function testGetQueryAnalysisNoData(): void
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

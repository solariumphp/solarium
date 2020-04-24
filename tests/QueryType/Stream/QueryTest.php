<?php

namespace Solarium\Tests\QueryType\Stream;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Stream\Query;

class QueryTest extends TestCase
{
    /**
     * @var Query
     */
    protected $query;

    public function setUp(): void
    {
        $this->query = new Query();
    }

    public function testConfigMode()
    {
        $q = new Query(['expr' => 'e1']);

        $this->assertSame('e1', $q->getExpression());
    }

    public function testSetAndGetExpression()
    {
        $this->query->setExpression('testexpression');
        $this->assertSame('testexpression', $this->query->getExpression());
    }
}

<?php

namespace Solarium\Tests\Plugin\BufferedAdd\Delete;

use PHPUnit\Framework\TestCase;
use Solarium\Plugin\BufferedAdd\AbstractDelete;
use Solarium\Plugin\BufferedAdd\Delete\Query;

class QueryTest extends TestCase
{
    /**
     * @var Query
     */
    protected $query;

    public function setUp(): void
    {
        $this->query = new Query('cat:abc');
    }

    public function testGetType()
    {
        $this->assertSame(AbstractDelete::TYPE_QUERY, $this->query->getType());
    }

    public function testGetQuery()
    {
        $this->assertSame('cat:abc', $this->query->getQuery());
    }

    public function testSetAndGetQuery()
    {
        $this->assertSame($this->query, $this->query->setQuery('cat:def'));

        $this->assertSame('cat:def', $this->query->getQuery());
    }

    public function testToString()
    {
        $this->assertSame('cat:abc', (string) $this->query);
    }
}

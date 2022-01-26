<?php

namespace Solarium\Tests\Plugin\BufferedDelete\Delete;

use PHPUnit\Framework\TestCase;
use Solarium\Plugin\BufferedDelete\AbstractDelete;
use Solarium\Plugin\BufferedDelete\Delete\Query;

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

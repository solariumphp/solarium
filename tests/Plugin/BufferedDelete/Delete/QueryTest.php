<?php

namespace Solarium\Tests\Plugin\BufferedDelete\Delete;

use PHPUnit\Framework\TestCase;
use Solarium\Plugin\BufferedDelete\DeleteInterface;
use Solarium\Plugin\BufferedDelete\Delete\Query;

class QueryTest extends TestCase
{
    protected Query $query;

    public function setUp(): void
    {
        $this->query = new Query('cat:abc');
    }

    public function testGetType(): void
    {
        $this->assertSame(DeleteInterface::TYPE_QUERY, $this->query->getType());
    }

    public function testGetQuery(): void
    {
        $this->assertSame('cat:abc', $this->query->getQuery());
    }

    public function testSetAndGetQuery(): void
    {
        $this->assertSame($this->query, $this->query->setQuery('cat:def'));

        $this->assertSame('cat:def', $this->query->getQuery());
    }

    public function testToString(): void
    {
        $this->assertSame('cat:abc', (string) $this->query);
    }
}

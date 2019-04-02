<?php

namespace Solarium\Tests\QueryType\ManagedResources\Query;

use Solarium\QueryType\ManagedResources\Query\Resources as Query;
use PHPUnit\Framework\TestCase;

class ResourcesTest extends TestCase
{
    protected $query;

    public function setUp(): void
    {
        $this->query = new Query();
    }

    public function testQuery()
    {
        $this->assertEquals('resources', $this->query->getType());
    }
}

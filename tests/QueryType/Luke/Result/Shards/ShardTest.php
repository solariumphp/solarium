<?php

namespace Solarium\Tests\QueryType\Luke\Result\Shards;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Luke\Result\Fields\FieldInfo;
use Solarium\QueryType\Luke\Result\Index\Index;
use Solarium\QueryType\Luke\Result\Shards\Shard;

class ShardTest extends TestCase
{
    protected Shard $shard;

    public function setUp(): void
    {
        $this->shard = new Shard('localhost:8983/solr/shard1');
    }

    public function testGetAddress(): void
    {
        $this->assertSame('localhost:8983/solr/shard1', $this->shard->getAddress());
    }

    public function testSetAndGetIndex(): void
    {
        $index = new Index();
        $this->assertSame($this->shard, $this->shard->setIndex($index));
        $this->assertSame($index, $this->shard->getIndex());
    }

    public function testSetAndGetFields(): void
    {
        $fields = [
            'field1' => new FieldInfo('field1'),
            'field2' => new FieldInfo('field2'),
        ];
        $this->assertSame($this->shard, $this->shard->setFields($fields));
        $this->assertSame($fields, $this->shard->getFields());

        $this->assertSame($this->shard, $this->shard->setFields(null));
        $this->assertNull($this->shard->getFields());
    }

    public function testToString(): void
    {
        $this->assertSame('localhost:8983/solr/shard1', (string) $this->shard);
    }
}

<?php

namespace Solarium\Tests\QueryType\Server\Metrics;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\QueryType\Server\Metrics\Query;
use Solarium\QueryType\Server\Metrics\RequestBuilder;
use Solarium\QueryType\Server\Metrics\ResponseParser;

class QueryTest extends TestCase
{
    protected Query $query;

    public function setUp(): void
    {
        $this->query = new Query();
    }

    public function testGetType(): void
    {
        $this->assertSame(Client::QUERY_METRICS, $this->query->getType());
    }

    public function testGetRequestBuilder(): void
    {
        $this->assertInstanceOf(RequestBuilder::class, $this->query->getRequestBuilder());
    }

    public function testGetResponseParser(): void
    {
        $this->assertInstanceOf(ResponseParser::class, $this->query->getResponseParser());
    }

    /**
     * @testWith ["name1,name2"]
     *           ["name1, name2"]
     *           [["name1", "name2"]]
     */
    public function testSetAndGetName(string|array $value): void
    {
        $this->query->setName($value);

        $this->assertSame(
            ['name1', 'name2'],
            $this->query->getName()
        );
    }

    /**
     * @testWith ["cat1,cat2"]
     *           ["cat1, cat2"]
     *           [["cat1", "cat2"]]
     */
    public function testSetAndGetCategory(string|array $value): void
    {
        $this->query->setCategory($value);

        $this->assertSame(
            ['cat1', 'cat2'],
            $this->query->getCategory()
        );
    }

    /**
     * @testWith ["core1,core2"]
     *           ["core1, core2"]
     *           [["core1", "core2"]]
     */
    public function testSetAndGetCore(string|array $value): void
    {
        $this->query->setCore($value);

        $this->assertSame(
            ['core1', 'core2'],
            $this->query->getCore()
        );
    }

    /**
     * @testWith ["coll1,coll2"]
     *           ["coll1, coll2"]
     *           [["coll1", "coll2"]]
     */
    public function testSetAndGetCollection(string|array $value): void
    {
        $this->query->setCollection($value);

        $this->assertSame(
            ['coll1', 'coll2'],
            $this->query->getCollection()
        );
    }

    /**
     * @testWith ["shard1,shard2"]
     *           ["shard1, shard2"]
     *           [["shard1", "shard2"]]
     */
    public function testSetAndGetShard(string|array $value): void
    {
        $this->query->setShard($value);

        $this->assertSame(
            ['shard1', 'shard2'],
            $this->query->getShard()
        );
    }

    /**
     * @testWith ["type1,type2"]
     *           ["type1, type2"]
     *           [["type1", "type2"]]
     */
    public function testSetAndGetReplicaType(string|array $value): void
    {
        $this->query->setReplicaType($value);

        $this->assertSame(
            ['type1', 'type2'],
            $this->query->getReplicaType()
        );
    }

    public function testConfigMode(): void
    {
        $options = [
            'handler' => 'myHandler',
            'responsewriter' => 'myWriter',
            'resultclass' => 'myResult',
            'name' => 'name1,name2',
            'category' => 'cat1,cat2',
            'core' => 'core1,core2',
            'collection' => 'coll1,coll2',
            'shard' => 'shard1,shard2',
            'replica_type' => 'type1,type2',
        ];
        $this->query->setOptions($options);

        $this->assertSame('myHandler', $this->query->getHandler());
        $this->assertSame('myWriter', $this->query->getResponseWriter());
        $this->assertSame('myResult', $this->query->getResultClass());
        $this->assertSame(['name1', 'name2'], $this->query->getName());
        $this->assertSame(['cat1', 'cat2'], $this->query->getCategory());
        $this->assertSame(['core1', 'core2'], $this->query->getCore());
        $this->assertSame(['coll1', 'coll2'], $this->query->getCollection());
        $this->assertSame(['shard1', 'shard2'], $this->query->getShard());
        $this->assertSame(['type1', 'type2'], $this->query->getReplicaType());
    }
}

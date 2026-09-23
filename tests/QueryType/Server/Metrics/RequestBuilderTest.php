<?php

namespace Solarium\Tests\QueryType\Server\Metrics;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\Server\Metrics\Query;
use Solarium\QueryType\Server\Metrics\RequestBuilder;

class RequestBuilderTest extends TestCase
{
    protected Query $query;

    protected RequestBuilder $builder;

    public function setUp(): void
    {
        $this->query = new Query();
        $this->builder = new RequestBuilder();
    }

    public function testBuild(): void
    {
        $request = $this->builder->build($this->query);

        $this->assertSame(
            Request::METHOD_GET,
            $request->getMethod()
        );

        $this->assertTrue(
            $request->getIsServerRequest()
        );

        $this->assertSame(
            'admin/metrics?wt=prometheus',
            $request->getUri()
        );
    }

    public function testBuildParams(): void
    {
        $this->query->setResponseWriter($this->query::WT_OPENMETRICS);
        $this->query->setName(['name1', 'name2']);
        $this->query->setCategory(['cat1', 'cat2']);
        $this->query->setCore(['core1', 'core2']);
        $this->query->setCollection(['coll1', 'coll2']);
        $this->query->setShard(['shard1', 'shard2']);
        $this->query->setReplicaType([
            $this->query::REPLICA_TYPE_NRT,
            $this->query::REPLICA_TYPE_PULL,
            $this->query::REPLICA_TYPE_TLOG,
        ]);

        $request = $this->builder->build($this->query);

        $this->assertSame(
            'admin/metrics?wt=openmetrics&name=name1%2Cname2&category=cat1%2Ccat2&core=core1%2Ccore2&collection=coll1%2Ccoll2&shard=shard1%2Cshard2&replica_type=NRT%2CPULL%2CTLOG',
            $request->getUri()
        );
    }
}

<?php

namespace Solarium\Tests\QueryType\Server\Collections;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\Server\Collections\Query\Query;
use Solarium\QueryType\Server\Query\RequestBuilder;

class RequestBuilderTest extends TestCase
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * @var RequestBuilder
     */
    protected $builder;

    public function setUp(): void
    {
        $this->query = new Query();
        $this->builder = new RequestBuilder();
    }

    public function testBuildParams()
    {
        $clusterStatus = $this->query->createClusterStatus();

        $this->query->setAction($clusterStatus);

        $request = $this->builder->build($this->query);

        $this->assertEquals(
            [
                'wt' => 'json',
                'json.nl' => 'flat',
                'action' => 'CLUSTERSTATUS',
            ],
            $request->getParams()
        );

        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $this->assertTrue($request->getIsServerRequest());
        $this->assertSame('admin/collections?wt=json&json.nl=flat&action=CLUSTERSTATUS', $request->getUri());
    }

    public function testCreate()
    {
        $create = $this->query->createCreate();
        $create->setName('test');
        $create->setRouterName('implicit');
        $create->setNumShards(3);
        $create->setShards('shard-x,shard-y,shard-z');
        $create->setReplicationFactor(1);
        $create->setNrtReplicas(1);
        $create->setTlogReplicas(1);
        $create->setPullReplicas(1);
        $create->setMaxShardsPerNode(10);
        $create->setCreateNodeSet('localhost:8983_solr,localhost:8984_solr');
        $create->setCreateNodeSetShuffle(true);
        $create->setCollectionConfigName('_default');
        $create->setRouterField('id');
        $create->setProperty('name', 'test');
        $create->setAutoAddReplicas(true);
        $create->setAsync(1);
        $create->setRule('');
        $create->setSnitch('');
        $create->setPolicy('');
        $create->setWaitForFinalState('');
        $create->setWithCollection('test2');
        $this->query->setAction($create);

        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $expectedUri = 'admin/collections?wt=json&json.nl=flat'.
            '&action=CREATE'.
            '&name=test'.
            '&router.name=implicit'.
            '&numShards=3'.
            '&shards=shard-x%2Cshard-y%2Cshard-z'.
            '&replicationFactor=1'.
            '&nrtReplicas=1'.
            '&tlogReplicas=1'.
            '&pullReplicas=1'.
            '&maxShardsPerNode=10'.
            '&createNodeSet=localhost%3A8983_solr%2Clocalhost%3A8984_solr'.
            '&createNodeSet.shuffle=true'.
            '&collection.configName=_default'.
            '&router.field=id'.
            '&property.name=test'.
            '&autoAddReplicas=true'.
            '&async=1'.
            '&rule='.
            '&snitch='.
            '&policy='.
            '&waitForFinalState=false'.
            '&withCollection=test2';
        $this->assertSame($expectedUri, $request->getUri());
    }

    public function testClusterStatus()
    {
        $status = $this->query->createClusterStatus();
        $status->setCollection('somecollection');
        $status->setRoute('test');
        $status->setShard('test');
        $this->query->setAction($status);

        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $expectedUri = 'admin/collections?wt=json&json.nl=flat&action=CLUSTERSTATUS&collection=somecollection&_route_=test&shard=test';
        $this->assertSame($expectedUri, $request->getUri());
    }

    public function testDelete()
    {
        $delete = $this->query->createDelete();
        $delete->setName('somecollection');
        $this->query->setAction($delete);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $expectedUri = 'admin/collections?wt=json&json.nl=flat&action=DELETE&name=somecollection';
        $this->assertSame($expectedUri, $request->getUri());
    }

    public function testReload()
    {
        $reload = $this->query->createReload();
        $reload->setName('somecollection');
        $this->query->setAction($reload);
        $request = $this->builder->build($this->query);
        $this->assertSame(Request::METHOD_GET, $request->getMethod());
        $expectedUri = 'admin/collections?wt=json&json.nl=flat&action=RELOAD&name=somecollection';
        $this->assertSame($expectedUri, $request->getUri());
    }
}

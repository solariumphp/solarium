<?php

namespace Solarium\Tests\Component\RequestBuilder;

use PHPUnit\Framework\TestCase;
use Solarium\Component\DistributedSearch as Component;
use Solarium\Component\RequestBuilder\DistributedSearch as RequestBuilder;
use Solarium\Core\Client\Request;

class DistributedSearchTest extends TestCase
{
    public function testBuildComponentWithShards()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $component = new Component();
        $component->addShard('shard1', 'localhost:8983/solr/shard1');
        $component->addShards(
            array(
                'shard2' => 'localhost:8983/solr/shard2',
                'shard3' => 'localhost:8983/solr/shard3',
            )
        );
        $component->setShardRequestHandler('dummy');

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(
            array(
                'shards.qt' => 'dummy',
                'shards' => 'localhost:8983/solr/shard1,localhost:8983/solr/shard2,localhost:8983/solr/shard3',
            ),
            $request->getParams()
        );
    }

    public function testBuildComponentWithCollections()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $url = 'localhost:8983/solr/collection';
        $component = new Component();
        $component->addCollection('collection1', $url.'1');
        $component->addCollections(
            array(
                'collection2' => $url.'2',
                'collection3' => $url.'3',
            )
        );

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(array('collection' => $url.'1,'.$url.'2,'.$url.'3'), $request->getParams());
    }

    public function testBuildComponentWithReplicas()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $url = 'localhost:8983/solr/replica';
        $component = new Component();
        $component->addReplica('replica1', $url.'1');
        $component->addReplicas(
            array(
                'replica2' => $url.'2',
                'replica3' => $url.'3',
            )
        );

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(array('shards' => $url.'1|'.$url.'2|'.$url.'3'), $request->getParams());
    }

    public function testBuildComponentWithReplicasAndShard()
    {
        $builder = new RequestBuilder();
        $request = new Request();

        $url = 'localhost:8983/solr/replica';
        $component = new Component();
        $component->addShard('shard1', 'localhost:8983/solr/shard1');

        $component->addReplicas(
            array(
                'replica2' => $url.'2',
                'replica3' => $url.'3',
            )
        );

        $request = $builder->buildComponent($component, $request);

        $this->assertEquals(array('shards' => 'localhost:8983/solr/shard1,'.$url.'2|'.$url.'3'), $request->getParams());
    }
}

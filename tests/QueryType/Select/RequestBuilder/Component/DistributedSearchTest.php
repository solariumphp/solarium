<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 */

namespace Solarium\Tests\QueryType\Select\RequestBuilder\Component;

use Solarium\QueryType\Select\RequestBuilder\Component\DistributedSearch as RequestBuilder;
use Solarium\QueryType\Select\Query\Component\DistributedSearch as Component;
use Solarium\Core\Client\Request;

class DistributedSearchTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildComponentWithShards()
    {
        $builder = new RequestBuilder;
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
        $builder = new RequestBuilder;
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

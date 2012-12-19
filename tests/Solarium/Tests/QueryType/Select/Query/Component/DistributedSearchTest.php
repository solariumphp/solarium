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

namespace Solarium\Tests\QueryType\Select\Query\Component;
use Solarium\QueryType\Select\Query\Component\DistributedSearch;
use Solarium\QueryType\Select\Query\Query;

class DistributedSearchTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DistributedSearch
     */
    protected $distributedSearch;

    public function setUp()
    {
        $this->distributedSearch = new DistributedSearch;
    }

    public function testConfigMode()
    {
        $options = array(
            'shardhandler' => 'dummyhandler',
            'shards' => array(
                'shard1' => 'localhost:8983/solr/shard1',
                'shard2' => 'localhost:8983/solr/shard2',
            )
        );

        $this->distributedSearch->setOptions($options);

        $this->assertEquals($options['shardhandler'], $this->distributedSearch->getShardRequestHandler());
        $this->assertEquals($options['shards'], $this->distributedSearch->getShards());
    }

    public function testGetType()
    {
        $this->assertEquals(
            Query::COMPONENT_DISTRIBUTEDSEARCH,
            $this->distributedSearch->getType()
        );
    }

    public function testGetResponseParser()
    {
        $this->assertEquals(null, $this->distributedSearch->getResponseParser());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf('Solarium\QueryType\Select\RequestBuilder\Component\DistributedSearch', $this->distributedSearch->getRequestBuilder());
    }

    public function testAddShard()
    {
        $this->distributedSearch->addShard('shard1', 'localhost:8983/solr/shard1');
        $shards = $this->distributedSearch->getShards();
        $this->assertEquals(
            'localhost:8983/solr/shard1',
            $shards['shard1']
        );
    }

    public function testRemoveShard()
    {
        $this->distributedSearch->addShard('shard1', 'localhost:8983/solr/shard1');
        $this->distributedSearch->removeShard('shard1');
        $shards = $this->distributedSearch->getShards();
        $this->assertFalse(isset($shards['shard1']));
    }

    public function testClearShards()
    {
        $this->distributedSearch->addShards(array(
            'shard1' => 'localhost:8983/solr/shard1',
            'shard2' => 'localhost:8983/solr/shard2',
        ));
        $this->distributedSearch->clearShards();
        $shards = $this->distributedSearch->getShards();
        $this->assertTrue(is_array($shards));
        $this->assertEquals(0, count($shards));
    }

    public function testAddShards()
    {
        $shards = array(
            'shard1' => 'localhost:8983/solr/shard1',
            'shard2' => 'localhost:8983/solr/shard2',
        );
        $this->distributedSearch->addShards($shards);
        $this->assertEquals($shards, $this->distributedSearch->getShards());
    }

    public function testSetShards()
    {
        $this->distributedSearch->addShards(array(
            'shard1' => 'localhost:8983/solr/shard1',
            'shard2' => 'localhost:8983/solr/shard2',
        ));
        $this->distributedSearch->setShards(array(
            'shard3' => 'localhost:8983/solr/shard3',
            'shard4' => 'localhost:8983/solr/shard4',
            'shard5' => 'localhost:8983/solr/shard5',
        ));
        $shards = $this->distributedSearch->getShards();
        $this->assertEquals(3, count($shards));
        $this->assertEquals(array(
            'shard3' => 'localhost:8983/solr/shard3',
            'shard4' => 'localhost:8983/solr/shard4',
            'shard5' => 'localhost:8983/solr/shard5',
        ), $shards);
    }

    public function testSetShardRequestHandler()
    {
        $this->distributedSearch->setShardRequestHandler('dummy');
        $this->assertEquals(
            'dummy',
            $this->distributedSearch->getShardRequestHandler()
        );
    }

}

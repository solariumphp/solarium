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

namespace Solarium\Tests\Plugin\Loadbalancer;

use Solarium\Core\Client\Client;
use Solarium\Plugin\Loadbalancer\Loadbalancer;
use Solarium\QueryType\Select\Query\Query as SelectQuery;
use Solarium\QueryType\Update\Query\Query as UpdateQuery;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Adapter\Http as HttpAdapter;
use Solarium\Exception\HttpException;
use Solarium\Core\Event\PreCreateRequest as PreCreateRequestEvent;
use Solarium\Core\Event\PreExecuteRequest as PreExecuteRequestEvent;

class LoadbalancerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Loadbalancer
     */
    protected $plugin;

    /**
     * @var Client
     */
    protected $client;

    public function setUp()
    {
        $this->plugin = new Loadbalancer();

        $options = array(
            'endpoint' => array(
                'server1' => array(
                    'host' => 'host1',
                ),
                'server2' => array(
                    'host' => 'host2',
                ),
            ),
        );

        $this->client = new Client($options);
        $adapter = $this->getMock('Solarium\Core\Client\Adapter\Http');
        $adapter->expects($this->any())
            ->method('execute')
            ->will($this->returnValue('dummyresult'));
        $this->client->setAdapter($adapter);
        $this->plugin->initPlugin($this->client, array());
    }

    public function testConfigMode()
    {
        $options = array(
            'endpoint' => array(
                'server1' => 10,
                'server2' => 5,
            ),
            'blockedquerytype' => array(Client::QUERY_UPDATE, Client::QUERY_MORELIKETHIS)
        );

        $this->plugin->setOptions($options);

        $this->assertEquals(
            array('server1' => 10, 'server2' => 5),
            $this->plugin->getEndpoints()
        );

        $this->assertEquals(
            array(Client::QUERY_UPDATE, Client::QUERY_MORELIKETHIS),
            $this->plugin->getBlockedQueryTypes()
        );
    }

    public function testSetAndGetFailoverEnabled()
    {
        $this->plugin->setFailoverEnabled(true);
        $this->assertEquals(true, $this->plugin->getFailoverEnabled());
    }

    public function testSetAndGetFailoverMaxRetries()
    {
        $this->plugin->setFailoverMaxRetries(16);
        $this->assertEquals(16, $this->plugin->getFailoverMaxRetries());
    }

    public function testAddEndpoint()
    {
        $this->plugin->addEndpoint('s1', 10);

        $this->assertEquals(
            array('s1' => 10),
            $this->plugin->getEndpoints()
        );
    }

    public function testAddEndpointWithObject()
    {
        $this->plugin->addEndpoint($this->client->getEndpoint('server1'), 10);

        $this->assertEquals(
            array('server1' => 10),
            $this->plugin->getEndpoints()
        );
    }

    public function testAddEndpoints()
    {
        $endpoints = array('s1' => 10, 's2' => 8);
        $this->plugin->addEndpoints($endpoints);

        $this->assertEquals(
            $endpoints,
            $this->plugin->getEndpoints()
        );
    }

    public function testAddEndpointWithDuplicateKey()
    {
        $this->plugin->addEndpoint('s1', 10);

        $this->setExpectedException('Solarium\Exception\InvalidArgumentException');
        $this->plugin->addEndpoint('s1', 20);
    }

    public function testClearEndpoints()
    {
        $this->plugin->addEndpoint('s1', 10);
        $this->plugin->clearEndpoints();
        $this->assertEquals(array(), $this->plugin->getEndpoints());
    }

    public function testRemoveEndpoint()
    {
        $endpoints = array(
            's1' => 10,
            's2' => 20,
        );

        $this->plugin->addEndpoints($endpoints);
        $this->plugin->removeEndpoint('s1');

        $this->assertEquals(
            array('s2' => 20),
            $this->plugin->getEndpoints()
        );
    }

    public function testRemoveEndpointWithObject()
    {
        $endpoints = array(
            'server1' => 10,
            'server2' => 20,
        );

        $this->plugin->addEndpoints($endpoints);
        $this->plugin->removeEndpoint($this->client->getEndpoint('server1'));

        $this->assertEquals(
            array('server2' => 20),
            $this->plugin->getEndpoints()
        );
    }

    public function testSetEndpoints()
    {
        $endpoints1 = array(
            's1' => 10,
            's2' => 20,
        );

        $endpoints2 = array(
            's3' => 50,
            's4' => 30,
        );

        $this->plugin->addEndpoints($endpoints1);
        $this->plugin->setEndpoints($endpoints2);

        $this->assertEquals(
            $endpoints2,
            $this->plugin->getEndpoints()
        );
    }

    public function testSetAndGetForcedEndpointForNextQuery()
    {
        $endpoints1 = array(
            's1' => 10,
            's2' => 20,
        );
        $this->plugin->addEndpoints($endpoints1);

        $this->plugin->setForcedEndpointForNextQuery('s2');
        $this->assertEquals('s2', $this->plugin->getForcedEndpointForNextQuery());
    }

    public function testSetAndGetForcedEndpointForNextQueryWithObject()
    {
        $endpoints1 = array(
            'server1' => 10,
            'server2' => 20,
        );
        $this->plugin->addEndpoints($endpoints1);

        $this->plugin->setForcedEndpointForNextQuery($this->client->getEndpoint('server2'));
        $this->assertEquals('server2', $this->plugin->getForcedEndpointForNextQuery());
    }

    public function testSetForcedEndpointForNextQueryWithInvalidKey()
    {
        $endpoints1 = array(
            's1' => 10,
            's2' => 20,
        );
        $this->plugin->addEndpoints($endpoints1);

        $this->setExpectedException('Solarium\Exception\OutOfBoundsException');
        $this->plugin->setForcedEndpointForNextQuery('s3');
    }

    public function testAddBlockedQueryType()
    {
        $this->plugin->addBlockedQueryType('type1');
        $this->plugin->addBlockedQueryType('type2');

        $this->assertEquals(
            array(Client::QUERY_UPDATE, 'type1', 'type2'),
            $this->plugin->getBlockedQueryTypes()
        );
    }

    public function testClearBlockedQueryTypes()
    {
        $this->plugin->addBlockedQueryType('type1');
        $this->plugin->addBlockedQueryType('type2');
        $this->plugin->clearBlockedQueryTypes();
        $this->assertEquals(array(), $this->plugin->getBlockedQueryTypes());
    }

    public function testAddBlockedQueryTypes()
    {
        $blockedQueryTypes = array('type1', 'type2', 'type3');

        $this->plugin->clearBlockedQueryTypes();
        $this->plugin->addBlockedQueryTypes($blockedQueryTypes);
        $this->assertEquals($blockedQueryTypes, $this->plugin->getBlockedQueryTypes());
    }

    public function testRemoveBlockedQueryType()
    {
        $blockedQueryTypes = array('type1', 'type2', 'type3');

        $this->plugin->clearBlockedQueryTypes();
        $this->plugin->addBlockedQueryTypes($blockedQueryTypes);
        $this->plugin->removeBlockedQueryType('type2');

        $this->assertEquals(
            array('type1', 'type3'),
            $this->plugin->getBlockedQueryTypes()
        );
    }

    public function testSetBlockedQueryTypes()
    {
        $blockedQueryTypes = array('type1', 'type2', 'type3');

        $this->plugin->setBlockedQueryTypes($blockedQueryTypes);

        $this->assertEquals(
            $blockedQueryTypes,
            $this->plugin->getBlockedQueryTypes()
        );
    }

    public function testPreExecuteRequestWithForcedEndpoint()
    {
        $endpoints = array(
           'server1' => 100,
           'server2' => 1,
        );
        $query = new SelectQuery();
        $request = new Request();

        $this->plugin->setEndpoints($endpoints);
        $this->plugin->setForcedEndpointForNextQuery('server2');

        $event = new PreCreateRequestEvent($query);
        $this->plugin->preCreateRequest($event);

        $event = new PreExecuteRequestEvent($request, new Endpoint);
        $this->plugin->preExecuteRequest($event);

        $this->assertEquals(
            'server2',
            $this->plugin->getLastEndpoint()
        );
    }

    public function testDefaultEndpointRestore()
    {
        $originalHost = $this->client->getEndpoint()->getHost();
        $endpoints = array(
           'server1' => 100,
           'server2' => 1,
        );
        $request = new Request();

        $this->plugin->setEndpoints($endpoints);
        $this->plugin->setForcedEndpointForNextQuery('server2');

        $query = new SelectQuery();
        $event = new PreCreateRequestEvent($query);
        $this->plugin->preCreateRequest($event);

        $event = new PreExecuteRequestEvent($request, new Endpoint);
        $this->plugin->preExecuteRequest($event);

        $this->assertEquals(
            'server2',
            $this->plugin->getLastEndpoint()
        );

        $query = new SelectQuery(); // this is a blocked querytype that should trigger a restore
        $event = new PreCreateRequestEvent($query);
        $this->plugin->preCreateRequest($event);

        $event = new PreExecuteRequestEvent($request, new Endpoint);
        $this->plugin->preExecuteRequest($event);

        $this->assertEquals(
            $originalHost,
            $this->client->getEndpoint()->getHost()
        );
    }

    public function testBlockedQueryTypeNotLoadbalanced()
    {
        $originalHost = $this->client->getEndpoint()->getHost();
        $endpoints = array(
           'server1' => 100,
           'server2' => 1,
        );
        $this->plugin->setEndpoints($endpoints);
        $request = new Request();

        $query = new UpdateQuery(); // this is a blocked querytype that should not be loadbalanced
        $event = new PreCreateRequestEvent($query);
        $this->plugin->preCreateRequest($event);

        $event = new PreExecuteRequestEvent($request, new Endpoint);
        $this->plugin->preExecuteRequest($event);

        $this->assertEquals(
            $originalHost,
            $this->client->getEndpoint()->getHost()
        );

        $this->assertEquals(
            null,
            $this->plugin->getLastEndpoint()
        );
    }

    public function testLoadbalancerRandomizing()
    {
        $endpoints = array(
           'server1' => 1,
           'server2' => 1,
        );
        $this->plugin->setEndpoints($endpoints);
        $request = new Request();

        $query = new SelectQuery();
        $event = new PreCreateRequestEvent($query);
        $this->plugin->preCreateRequest($event);

        $event = new PreExecuteRequestEvent($request, new Endpoint);
        $this->plugin->preExecuteRequest($event);

        $this->assertTrue(
            in_array($this->plugin->getLastEndpoint(), array('server1', 'server2'))
        );
    }

    public function testFailover()
    {
        $this->plugin = new TestLoadbalancer(); // special loadbalancer that returns endpoints in fixed order
        $this->client->setAdapter(new TestAdapterForFailover()); // set special mock that fails once
        $this->plugin->initPlugin($this->client, array());

        $request = new Request();
        $endpoints = array(
           'server1' => 1,
           'server2' => 1,
        );
        $this->plugin->setEndpoints($endpoints);
        $this->plugin->setFailoverEnabled(true);

        $query = new SelectQuery();
        $event = new PreCreateRequestEvent($query);
        $this->plugin->preCreateRequest($event);

        $event = new PreExecuteRequestEvent($request, new Endpoint);
        $this->plugin->preExecuteRequest($event);

        $this->assertEquals(
            'server2',
            $this->plugin->getLastEndpoint()
        );
    }

    public function testFailoverMaxRetries()
    {
        $this->plugin = new TestLoadbalancer(); // special loadbalancer that returns endpoints in fixed order

        $adapter = new TestAdapterForFailover();
        $adapter->setFailCount(10);
        $this->client->setAdapter($adapter); // set special mock that fails for all endpoints
        $this->plugin->initPlugin($this->client, array());

        $request = new Request();
        $endpoints = array(
           'server1' => 1,
           'server2' => 1,
        );
        $this->plugin->setEndpoints($endpoints);
        $this->plugin->setFailoverEnabled(true);

        $query = new SelectQuery();
        $event = new PreCreateRequestEvent($query);
        $this->plugin->preCreateRequest($event);

        $this->setExpectedException(
            'Solarium\Exception\RuntimeException',
            'Maximum number of loadbalancer retries reached'
        );

        $event = new PreExecuteRequestEvent($request, new Endpoint);
        $this->plugin->preExecuteRequest($event);
    }
}

class TestLoadbalancer extends Loadbalancer
{
    protected $counter = 0;

    /**
     * Get options array for a randomized endpoint
     *
     * @return array
     */
    protected function getRandomEndpoint()
    {
        $this->counter++;
        $endpointKey = 'server'.$this->counter;

        $this->endpointExcludes[] = $endpointKey;
        $this->lastEndpoint = $endpointKey;

        return $this->client->getEndpoint($endpointKey);
    }
}

class TestAdapterForFailover extends HttpAdapter
{
    protected $counter = 0;

    protected $failCount = 1;

    public function setFailCount($count)
    {
        $this->failCount = $count;
    }

    public function execute($request, $endpoint)
    {
        $this->counter++;
        if ($this->counter <= $this->failCount) {
            throw new HttpException('failover exception');
        }

        return 'dummyvalue';
    }
}

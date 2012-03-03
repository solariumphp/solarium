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
use Solarium\Query\Select\Query\Query as SelectQuery;
use Solarium\Query\Update\Query\Query as UpdateQuery;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Adapter\Http as HttpAdapter;
use Solarium\Core\Client\HttpException;

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

    protected $serverOptions = array('host' => 'nonexistinghostname');

    public function setUp()
    {
        $this->plugin = new Loadbalancer();

        $this->client = new Client();
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
            'server' => array(
                'server1' => array(
                    'options' => array(
                        'host' => 'host1'
                    ),
                    'weight' => 10,
                ),
                'server2' => array(
                    'options' => array(
                        'host' => 'host2'
                    ),
                    'weight' => 5,
                ),
            ),
            'blockedquerytype' => array(Client::QUERY_UPDATE, Client::QUERY_MORELIKETHIS)
        );

        $this->plugin->setOptions($options);

        $this->assertEquals(
            array(
                'server1' => array(
                    'options' => array('host' => 'host1'),
                    'weight' => 10,
                ),
                'server2' => array(
                    'options' => array('host' => 'host2'),
                    'weight' => 5,
                )
            ),
            $this->plugin->getServers()
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

    public function testAddServer()
    {
        $this->plugin->addServer('s1', $this->serverOptions, 10);

        $this->assertEquals(
            array('s1' =>
                array(
                    'options' => $this->serverOptions,
                    'weight' => 10,
                )
            ),
            $this->plugin->getServers()
        );
    }

    public function testAddServerWithDuplicateKey()
    {
        $this->plugin->addServer('s1', $this->serverOptions, 10);

        $this->setExpectedException('Solarium\Core\Exception');
        $this->plugin->addServer('s1', $this->serverOptions, 20);
    }

    public function testGetServer()
    {
        $this->plugin->addServer('s1', $this->serverOptions, 10);

        $this->assertEquals(
            array('options' => $this->serverOptions, 'weight' => 10),
            $this->plugin->getServer('s1')
        );
    }

    public function testGetInvalidServer()
    {
        $this->plugin->addServer('s1', $this->serverOptions, 10);

        $this->setExpectedException('Solarium\Core\Exception');
        $this->plugin->getServer('s2');
    }

    public function testClearServers()
    {
        $this->plugin->addServer('s1', $this->serverOptions, 10);
        $this->plugin->clearServers();
        $this->assertEquals(array(), $this->plugin->getServers());
    }

    public function testAddServers()
    {
        $servers = array(
            's1' => array('options' => $this->serverOptions, 'weight' => 10),
            's2' => array('options' => $this->serverOptions, 'weight' => 20),
        );

        $this->plugin->addServers($servers);
        $this->assertEquals($servers, $this->plugin->getServers());
    }

    public function testRemoveServer()
    {
        $servers = array(
            's1' => array('options' => $this->serverOptions, 'weight' => 10),
            's2' => array('options' => $this->serverOptions, 'weight' => 20),
        );

        $this->plugin->addServers($servers);
        $this->plugin->removeServer('s1');

        $this->assertEquals(
            array('s2' => array('options' => $this->serverOptions, 'weight' => 20)),
            $this->plugin->getServers()
        );
    }

    public function testSetServers()
    {
        $servers1 = array(
            's1' => array('options' => $this->serverOptions, 'weight' => 10),
            's2' => array('options' => $this->serverOptions, 'weight' => 20),
        );

        $servers2 = array(
            's3' => array('options' => $this->serverOptions, 'weight' => 50),
            's4' => array('options' => $this->serverOptions, 'weight' => 30),
        );

        $this->plugin->addServers($servers1);
        $this->plugin->setServers($servers2);

        $this->assertEquals(
            $servers2,
            $this->plugin->getServers()
        );
    }

    public function testSetAndGetForcedServerForNextQuery()
    {
        $servers1 = array(
            's1' => array('options' => $this->serverOptions, 'weight' => 10),
            's2' => array('options' => $this->serverOptions, 'weight' => 20),
        );
        $this->plugin->addServers($servers1);

        $this->plugin->setForcedServerForNextQuery('s2');
        $this->assertEquals('s2', $this->plugin->getForcedServerForNextQuery());
    }

    public function testSetForcedServerForNextQueryWithInvalidKey()
    {
        $servers1 = array(
            's1' => array('options' => $this->serverOptions, 'weight' => 10),
            's2' => array('options' => $this->serverOptions, 'weight' => 20),
        );
        $this->plugin->addServers($servers1);

        $this->setExpectedException('Solarium\Core\Exception');
        $this->plugin->setForcedServerForNextQuery('s3');
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

    public function testPreExecuteRequestWithForcedServer()
    {
        $servers = array(
           's1' => array('options' => $this->serverOptions, 'weight' => 100),
           's2' => array('options' => $this->serverOptions, 'weight' => 1),
        );
        $query = new SelectQuery();
        $request = new Request();

        $this->plugin->setServers($servers);
        $this->plugin->setForcedServerForNextQuery('s2');
        $this->plugin->preCreateRequest($query);
        $this->plugin->preExecuteRequest($request);

        $this->assertEquals(
            's2',
            $this->plugin->getLastServerKey()
        );
    }

    public function testAdapterPresetRestore()
    {
        $originalHost = $this->client->getAdapter()->getHost();
        $servers = array(
           's1' => array('options' => $this->serverOptions, 'weight' => 100),
           's2' => array('options' => $this->serverOptions, 'weight' => 1),
        );
        $request = new Request();

        $this->plugin->setServers($servers);
        $this->plugin->setForcedServerForNextQuery('s2');

        $query = new SelectQuery();
        $this->plugin->preCreateRequest($query);
        $this->plugin->preExecuteRequest($request);

        $this->assertEquals(
            's2',
            $this->plugin->getLastServerKey()
        );

        $query = new SelectQuery(); // this is a blocked querytype that should trigger a restore
        $this->plugin->preCreateRequest($query);
        $this->plugin->preExecuteRequest($request);

        $this->assertEquals(
            $originalHost,
            $this->client->getAdapter()->getHost()
        );
    }

    public function testBlockedQueryTypeNotLoadbalanced()
    {
        $originalHost = $this->client->getAdapter()->getHost();
        $servers = array(
           's1' => array('options' => $this->serverOptions, 'weight' => 100),
           's2' => array('options' => $this->serverOptions, 'weight' => 1),
        );
        $this->plugin->setServers($servers);
        $request = new Request();

        $query = new UpdateQuery(); // this is a blocked querytype that should not be loadbalanced
        $this->plugin->preCreateRequest($query);
        $this->plugin->preExecuteRequest($request);

        $this->assertEquals(
            $originalHost,
            $this->client->getAdapter()->getHost()
        );

        $this->assertEquals(
            null,
            $this->plugin->getLastServerKey()
        );
    }

    public function testLoadbalancerRandomizing()
    {
        $servers = array(
           's1' => array('options' => $this->serverOptions, 'weight' => 1),
           's2' => array('options' => $this->serverOptions, 'weight' => 1),
        );
        $this->plugin->setServers($servers);
        $request = new Request();

        $query = new SelectQuery(); //
        $this->plugin->preCreateRequest($query);
        $this->plugin->preExecuteRequest($request);

        $this->assertTrue(
            in_array($this->plugin->getLastServerKey(), array('s1','s2'))
        );
    }

    public function testFailover()
    {
        $this->plugin = new TestLoadbalancer(); // special loadbalancer that returns servers in fixed order
        $this->client = new Client();
        $this->client->setAdapter(new TestAdapterForFailover()); // set special mock that fails once
        $this->plugin->initPlugin($this->client, array());

        $request = new Request();
        $servers = array(
           's1' => array('options' => $this->serverOptions, 'weight' => 1),
           's2' => array('options' => $this->serverOptions, 'weight' => 1),
        );
        $this->plugin->setServers($servers);
        $this->plugin->setFailoverEnabled(true);

        $query = new SelectQuery();
        $this->plugin->preCreateRequest($query);
        $this->plugin->preExecuteRequest($request);

        $this->assertEquals(
            's2',
            $this->plugin->getLastServerKey()
        );
    }

    public function testFailoverMaxRetries()
    {
        $this->plugin = new TestLoadbalancer(); // special loadbalancer that returns servers in fixed order
        $this->client = new Client();
        $adapter = new TestAdapterForFailover();
        $adapter->setFailCount(10);
        $this->client->setAdapter($adapter); // set special mock that fails for all servers
        $this->plugin->initPlugin($this->client, array());

        $request = new Request();
        $servers = array(
           's1' => array('options' => $this->serverOptions, 'weight' => 1),
           's2' => array('options' => $this->serverOptions, 'weight' => 1),
        );
        $this->plugin->setServers($servers);
        $this->plugin->setFailoverEnabled(true);

        $query = new SelectQuery();
        $this->plugin->preCreateRequest($query);

        $this->setExpectedException('Solarium\Core\Exception', 'Maximum number of loadbalancer retries reached');

        $this->plugin->preExecuteRequest($request);
    }



}

class TestLoadbalancer extends Loadbalancer{

    protected $counter = 0;

    /**
     * Get options array for a randomized server
     *
     * @return array
     */
    protected function getRandomServerOptions()
    {
        $this->counter++;
        $serverKey = 's'.$this->counter;

        $this->serverExcludes[] = $serverKey;
        $this->lastServerKey = $serverKey;
        return $this->servers[$serverKey]['options'];
    }

}

class TestAdapterForFailover extends HttpAdapter{

    protected $counter = 0;

    protected $failCount = 1;

    public function setFailCount($count)
    {
        $this->failCount = $count;
    }

    public function execute($request)
    {
        $this->counter++;
        if($this->counter <= $this->failCount) {
            throw new HttpException('failover exception');
        }

        return 'dummyvalue';
    }

}
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

class Solarium_Plugin_LoadbalancerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Solarium_Plugin_Loadbalancer
     */
    protected $_plugin;

    /**
     * @var Solarium_Client
     */
    protected $_client;

    protected $_serverOptions = array('host' => 'nonexistinghostname');

    public function setUp()
    {
        $this->_plugin = new Solarium_Plugin_Loadbalancer();

        $this->_client = new Solarium_Client();
        $adapter = $this->getMock('Solarium_Client_Adapter_Http');
        $adapter->expects($this->any())
            ->method('execute')
            ->will($this->returnValue('dummyresult'));
        $this->_client->setAdapter($adapter);
        $this->_plugin->init($this->_client, array());
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
            'blockedquerytype' => array(Solarium_Client::QUERYTYPE_UPDATE, Solarium_Client::QUERYTYPE_MORELIKETHIS)
        );

        $this->_plugin->setOptions($options);

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
            $this->_plugin->getServers()
        );

        $this->assertEquals(
            array(Solarium_Client::QUERYTYPE_UPDATE, Solarium_Client::QUERYTYPE_MORELIKETHIS),
            $this->_plugin->getBlockedQueryTypes()
        );
    }

    public function testSetAndGetFailoverEnabled()
    {
        $this->_plugin->setFailoverEnabled(true);
        $this->assertEquals(true, $this->_plugin->getFailoverEnabled());
    }

    public function testSetAndGetFailoverMaxRetries()
    {
        $this->_plugin->setFailoverMaxRetries(16);
        $this->assertEquals(16, $this->_plugin->getFailoverMaxRetries());
    }

    public function testAddServer()
    {
        $this->_plugin->addServer('s1', $this->_serverOptions, 10);

        $this->assertEquals(
            array('s1' =>
                array(
                    'options' => $this->_serverOptions,
                    'weight' => 10,
                )
            ),
            $this->_plugin->getServers()
        );
    }

    public function testAddServerWithDuplicateKey()
    {
        $this->_plugin->addServer('s1', $this->_serverOptions, 10);

        $this->setExpectedException('Solarium_Exception');
        $this->_plugin->addServer('s1', $this->_serverOptions, 20);
    }

    public function testGetServer()
    {
        $this->_plugin->addServer('s1', $this->_serverOptions, 10);

        $this->assertEquals(
            array('options' => $this->_serverOptions, 'weight' => 10),
            $this->_plugin->getServer('s1')
        );
    }

    public function testGetInvalidServer()
    {
        $this->_plugin->addServer('s1', $this->_serverOptions, 10);

        $this->setExpectedException('Solarium_Exception');
        $this->_plugin->getServer('s2');
    }

    public function testClearServers()
    {
        $this->_plugin->addServer('s1', $this->_serverOptions, 10);
        $this->_plugin->clearServers();
        $this->assertEquals(array(), $this->_plugin->getServers());
    }

    public function testAddServers()
    {
        $servers = array(
            's1' => array('options' => $this->_serverOptions, 'weight' => 10),
            's2' => array('options' => $this->_serverOptions, 'weight' => 20),
        );

        $this->_plugin->addServers($servers);
        $this->assertEquals($servers, $this->_plugin->getServers());
    }

    public function testRemoveServer()
    {
        $servers = array(
            's1' => array('options' => $this->_serverOptions, 'weight' => 10),
            's2' => array('options' => $this->_serverOptions, 'weight' => 20),
        );

        $this->_plugin->addServers($servers);
        $this->_plugin->removeServer('s1');

        $this->assertEquals(
            array('s2' => array('options' => $this->_serverOptions, 'weight' => 20)),
            $this->_plugin->getServers()
        );
    }

    public function testSetServers()
    {
        $servers1 = array(
            's1' => array('options' => $this->_serverOptions, 'weight' => 10),
            's2' => array('options' => $this->_serverOptions, 'weight' => 20),
        );

        $servers2 = array(
            's3' => array('options' => $this->_serverOptions, 'weight' => 50),
            's4' => array('options' => $this->_serverOptions, 'weight' => 30),
        );

        $this->_plugin->addServers($servers1);
        $this->_plugin->setServers($servers2);

        $this->assertEquals(
            $servers2,
            $this->_plugin->getServers()
        );
    }

    public function testSetAndGetForcedServerForNextQuery()
    {
        $servers1 = array(
            's1' => array('options' => $this->_serverOptions, 'weight' => 10),
            's2' => array('options' => $this->_serverOptions, 'weight' => 20),
        );
        $this->_plugin->addServers($servers1);

        $this->_plugin->setForcedServerForNextQuery('s2');
        $this->assertEquals('s2', $this->_plugin->getForcedServerForNextQuery());
    }

    public function testSetForcedServerForNextQueryWithInvalidKey()
    {
        $servers1 = array(
            's1' => array('options' => $this->_serverOptions, 'weight' => 10),
            's2' => array('options' => $this->_serverOptions, 'weight' => 20),
        );
        $this->_plugin->addServers($servers1);

        $this->setExpectedException('Solarium_Exception');
        $this->_plugin->setForcedServerForNextQuery('s3');
    }

    public function testAddBlockedQueryType()
    {
        $this->_plugin->addBlockedQueryType('type1');
        $this->_plugin->addBlockedQueryType('type2');

        $this->assertEquals(
            array(Solarium_Client::QUERYTYPE_UPDATE, 'type1', 'type2'),
            $this->_plugin->getBlockedQueryTypes()
        );
    }

    public function testClearBlockedQueryTypes()
    {
        $this->_plugin->addBlockedQueryType('type1');
        $this->_plugin->addBlockedQueryType('type2');
        $this->_plugin->clearBlockedQueryTypes();
        $this->assertEquals(array(), $this->_plugin->getBlockedQueryTypes());
    }

    public function testAddBlockedQueryTypes()
    {
        $blockedQueryTypes = array('type1', 'type2', 'type3');

        $this->_plugin->clearBlockedQueryTypes();
        $this->_plugin->addBlockedQueryTypes($blockedQueryTypes);
        $this->assertEquals($blockedQueryTypes, $this->_plugin->getBlockedQueryTypes());
    }

    public function testRemoveBlockedQueryType()
    {
        $blockedQueryTypes = array('type1', 'type2', 'type3');

        $this->_plugin->clearBlockedQueryTypes();
        $this->_plugin->addBlockedQueryTypes($blockedQueryTypes);
        $this->_plugin->removeBlockedQueryType('type2');

        $this->assertEquals(
            array('type1', 'type3'),
            $this->_plugin->getBlockedQueryTypes()
        );
    }

    public function testSetBlockedQueryTypes()
    {
        $blockedQueryTypes = array('type1', 'type2', 'type3');

        $this->_plugin->setBlockedQueryTypes($blockedQueryTypes);

        $this->assertEquals(
            $blockedQueryTypes,
            $this->_plugin->getBlockedQueryTypes()
        );
    }

    public function testPreExecuteRequestWithForcedServer()
    {
        $servers = array(
           's1' => array('options' => $this->_serverOptions, 'weight' => 100),
           's2' => array('options' => $this->_serverOptions, 'weight' => 1),
        );
        $query = new Solarium_Query_Select();
        $request = new Solarium_Client_Request();

        $this->_plugin->setServers($servers);
        $this->_plugin->setForcedServerForNextQuery('s2');
        $this->_plugin->preCreateRequest($query);
        $this->_plugin->preExecuteRequest($request);

        $this->assertEquals(
            's2',
            $this->_plugin->getLastServerKey()
        );
    }

    public function testAdapterPresetRestore()
    {
        $originalHost = $this->_client->getAdapter()->getHost();
        $servers = array(
           's1' => array('options' => $this->_serverOptions, 'weight' => 100),
           's2' => array('options' => $this->_serverOptions, 'weight' => 1),
        );
        $request = new Solarium_Client_Request();

        $this->_plugin->setServers($servers);
        $this->_plugin->setForcedServerForNextQuery('s2');

        $query = new Solarium_Query_Select();
        $this->_plugin->preCreateRequest($query);
        $this->_plugin->preExecuteRequest($request);

        $this->assertEquals(
            's2',
            $this->_plugin->getLastServerKey()
        );

        $query = new Solarium_Query_Update(); // this is a blocked querytype that should trigger a restore
        $this->_plugin->preCreateRequest($query);
        $this->_plugin->preExecuteRequest($request);

        $this->assertEquals(
            $originalHost,
            $this->_client->getAdapter()->getHost()
        );
    }

    public function testBlockedQueryTypeNotLoadbalanced()
    {
        $originalHost = $this->_client->getAdapter()->getHost();
        $servers = array(
           's1' => array('options' => $this->_serverOptions, 'weight' => 100),
           's2' => array('options' => $this->_serverOptions, 'weight' => 1),
        );
        $this->_plugin->setServers($servers);
        $request = new Solarium_Client_Request();

        $query = new Solarium_Query_Update(); // this is a blocked querytype that should not be loadbalanced
        $this->_plugin->preCreateRequest($query);
        $this->_plugin->preExecuteRequest($request);

        $this->assertEquals(
            $originalHost,
            $this->_client->getAdapter()->getHost()
        );

        $this->assertEquals(
            null,
            $this->_plugin->getLastServerKey()
        );
    }

    public function testLoadbalancerRandomizing()
    {
        $servers = array(
           's1' => array('options' => $this->_serverOptions, 'weight' => 1),
           's2' => array('options' => $this->_serverOptions, 'weight' => 1),
        );
        $this->_plugin->setServers($servers);
        $request = new Solarium_Client_Request();

        $query = new Solarium_Query_Select(); //
        $this->_plugin->preCreateRequest($query);
        $this->_plugin->preExecuteRequest($request);

        $this->assertTrue(
            in_array($this->_plugin->getLastServerKey(), array('s1','s2'))
        );
    }

    public function testFailover()
    {
        $this->_plugin = new TestLoadbalancer(); // special loadbalancer that returns servers in fixed order
        $this->_client = new Solarium_Client();
        $this->_client->setAdapter(new TestAdapterForFailover()); // set special mock that fails once
        $this->_plugin->init($this->_client, array());

        $request = new Solarium_Client_Request();
        $servers = array(
           's1' => array('options' => $this->_serverOptions, 'weight' => 1),
           's2' => array('options' => $this->_serverOptions, 'weight' => 1),
        );
        $this->_plugin->setServers($servers);
        $this->_plugin->setFailoverEnabled(true);

        $query = new Solarium_Query_Select();
        $this->_plugin->preCreateRequest($query);
        $this->_plugin->preExecuteRequest($request);

        $this->assertEquals(
            's2',
            $this->_plugin->getLastServerKey()
        );
    }

    public function testFailoverMaxRetries()
    {
        $this->_plugin = new TestLoadbalancer(); // special loadbalancer that returns servers in fixed order
        $this->_client = new Solarium_Client();
        $adapter = new TestAdapterForFailover();
        $adapter->setFailCount(10);
        $this->_client->setAdapter($adapter); // set special mock that fails for all servers
        $this->_plugin->init($this->_client, array());

        $request = new Solarium_Client_Request();
        $servers = array(
           's1' => array('options' => $this->_serverOptions, 'weight' => 1),
           's2' => array('options' => $this->_serverOptions, 'weight' => 1),
        );
        $this->_plugin->setServers($servers);
        $this->_plugin->setFailoverEnabled(true);

        $query = new Solarium_Query_Select();
        $this->_plugin->preCreateRequest($query);

        $this->setExpectedException('Solarium_Exception', 'Maximum number of loadbalancer retries reached');

        $this->_plugin->preExecuteRequest($request);
    }



}

class TestLoadbalancer extends Solarium_Plugin_Loadbalancer{

    protected $_counter = 0;

    /**
     * Get options array for a randomized server
     *
     * @return array
     */
    protected function _getRandomServerOptions()
    {
        $this->_counter++;
        $serverKey = 's'.$this->_counter;

        $this->_serverExcludes[] = $serverKey;
        $this->_lastServerKey = $serverKey;
        return $this->_servers[$serverKey]['options'];
    }

}

class TestAdapterForFailover extends Solarium_Client_Adapter_Http{

    protected $_counter = 0;

    protected $_failCount = 1;

    public function setFailCount($count)
    {
        $this->_failCount = $count;
    }

    public function execute($request)
    {
        $this->_counter++;
        if($this->_counter <= $this->_failCount) {
            throw new Solarium_Client_HttpException('failover exception');
        }

        return 'dummyvalue';
    }

}
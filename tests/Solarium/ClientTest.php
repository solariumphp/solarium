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

class Solarium_ClientTest extends PHPUnit_Framework_TestCase
{

    public function testSetAndGetHost()
    {
        $client = new Solarium_Client();
        $client->setHost('myhost');
        $this->assertEquals('myhost', $client->getHost());
    }
    
    public function testSetAndGetPort()
    {
        $client = new Solarium_Client();
        $client->setPort(8080);
        $this->assertEquals(8080, $client->getPort());
    }
    
    public function testSetAndGetPath()
    {
        $client = new Solarium_Client();
        $client->setPath('/mysolr');
        $this->assertEquals('/mysolr', $client->getPath());
    }

    public function testSetAndGetPathWithTrailingSlash()
    {
        $client = new Solarium_Client();
        $client->setPath('/mysolr/');
        $this->assertEquals('/mysolr', $client->getPath());
    }
    
    public function testSetAndGetCore()
    {
        $client = new Solarium_Client();
        $client->setCore('core1');
        $this->assertEquals('core1', $client->getCore());
    }

    public function testGetAdapterWithDefaultAdapter()
    {
        $client = new Solarium_Client();
        $defaultAdapter = $client->getOption('adapter');
        $adapter = $client->getAdapter();
        $this->assertThat($adapter, $this->isInstanceOf($defaultAdapter));
    }

    public function testGetAdapterWithString()
    {
        $adapterClass = 'MyAdapter';
        $client = new Solarium_Client();
        $client->setAdapter($adapterClass);
        $this->assertThat($client->getAdapter(), $this->isInstanceOf($adapterClass));
    }
    
    public function testGetAdapterWithObject()
    {
        $adapterClass = 'MyAdapter';
        $client = new Solarium_Client();
        $client->setAdapter(new $adapterClass);
        $this->assertThat($client->getAdapter(), $this->isInstanceOf($adapterClass));
    }

    public function testOptionForwardingToAdapter()
    {
        $client = new Solarium_Client();
        $options = $client->getOptions();

        // initialising at adapter creation
        $observer = $this->getMock('Solarium_Client_Adapter_Http', array('setOptions'));
        $observer->expects($this->once())
                 ->method('setOptions')
                 ->with($this->equalTo($options));
        $client->setAdapter($observer);
    }

    public function testOptionForwardingToAdapterAfterChange()
    {
        $newHostValue = 'myCustomHost';

        $client = new Solarium_Client;
        $options = $client->getOptions();
        $options['host'] = $newHostValue;

        $observer = $this->getMock('Solarium_Client_Adapter_Http', array('setOptions'));
        $observer->expects($this->at(1))
                 ->method('setOptions')
                 ->with($this->equalTo($options));
        
        $client->setAdapter($observer);
        $client->setHost($newHostValue); // this change should trigger a new adapter->setOptions call 
    }

    public function testSelect()
    {
        $client = new Solarium_Client;
        $query = new Solarium_Query_Select;

        // initialising at adapter creation
        $observer = $this->getMock('Solarium_Client_Adapter_Http', array('select'));
        $observer->expects($this->once())
                 ->method('select')
                 ->with($this->equalTo($query));

        $client->setAdapter($observer);
        $client->select($query);
    }

    public function testPing()
    {
        $client = new Solarium_Client;
        $query = new Solarium_Query_Ping;

        // initialising at adapter creation
        $observer = $this->getMock('Solarium_Client_Adapter_Http', array('ping'));
        $observer->expects($this->once())
                 ->method('ping')
                 ->with($this->equalTo($query));

        $client->setAdapter($observer);
        $client->ping($query);
    }

    public function testUpdate()
    {
        $client = new Solarium_Client;
        $query = new Solarium_Query_Update;

        // initialising at adapter creation
        $observer = $this->getMock('Solarium_Client_Adapter_Http', array('update'));
        $observer->expects($this->once())
                 ->method('update')
                 ->with($this->equalTo($query));

        $client->setAdapter($observer);
        $client->update($query);
    }
    
}

class MyAdapter extends Solarium_Client_Adapter_Http{

    public function select($query)
    {
    }

    public function ping($query)
    {
    }

    public function update($query)
    {
    }

}
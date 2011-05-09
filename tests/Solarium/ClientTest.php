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

}

class MyAdapter extends Solarium_Client_Adapter_Http{

    public function execute($request)
    {
        $response = new Solarium_Client_Response('{}', array('HTTP/1.1 200 OK'));
        return $response;
    }
}

class myConfig{

    protected $_options;

    public function __construct($options)
    {
        $this->_options = $options;
    }

    public function toArray()
    {
        return $this->_options;
    }
}
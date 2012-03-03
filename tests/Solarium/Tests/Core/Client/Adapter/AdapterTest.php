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

namespace Solarium\Tests\Core\Client\Adapter;
use Solarium\Core\Client\Adapter\Adapter;
use Solarium\Core\Client\Request;
use Solarium\Core\Exception;
use Solarium\Core\Client\HttpException;

class AdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TestAdapter
     */
    protected $adapter;

    public function setUp()
    {
        $this->adapter = new TestAdapter();
    }

    public function testConfigMode()
    {
        $options = array(
            'host'    => '192.168.0.1',
            'port'    => 123,
            'path'    => '/mysolr/',
            'core'    => 'mycore',
            'timeout' => 3,
        );
        $this->adapter->setOptions($options);

        $options['path'] = '/mysolr'; //expected trimming of trailing slash

        $this->assertEquals($options, $this->adapter->getOptions());
    }

    public function testSetAndGetHost()
    {
        $this->adapter->setHost('myhost');
        $this->assertEquals('myhost', $this->adapter->getHost());
    }

    public function testSetAndGetPort()
    {
        $this->adapter->setPort(8080);
        $this->assertEquals(8080, $this->adapter->getPort());
    }

    public function testSetAndGetPath()
    {
        $this->adapter->setPath('/mysolr');
        $this->assertEquals('/mysolr', $this->adapter->getPath());
    }

    public function testSetAndGetPathWithTrailingSlash()
    {
        $this->adapter->setPath('/mysolr/');
        $this->assertEquals('/mysolr', $this->adapter->getPath());
    }

    public function testSetAndGetCore()
    {
        $this->adapter->setCore('core1');
        $this->assertEquals('core1', $this->adapter->getCore());
    }

    public function testSetAndGetTimeout()
    {
        $this->adapter->setTimeout(7);
        $this->assertEquals(7, $this->adapter->getTimeout());
    }

    public function testGetBaseUri()
    {
        $this->adapter->setHost('myserver')->setPath('/mypath')->setPort(123);

        $this->assertEquals('http://myserver:123/mypath/', $this->adapter->getBaseUri());
    }

    public function testGetBaseUriWithCore()
    {
        $this->adapter->setHost('myserver')->setPath('/mypath')->setPort(123)->setCore('mycore');

        $this->assertEquals('http://myserver:123/mypath/mycore/', $this->adapter->getBaseUri());
    }

}

class TestAdapter extends Adapter
{

    public function execute($request)
    {

    }

}
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

namespace Solarium\Tests\Core\Client;

use Solarium\Core\Client\Endpoint;

class EndpointTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Endpoint
     */
    protected $endpoint;

    public function setUp()
    {
        $this->endpoint = new Endpoint();
    }

    public function testConfigMode()
    {
        $options = array(
            'scheme' => 'http',
            'host' => '192.168.0.1',
            'port' => 123,
            'path' => '/mysolr/',
            'core' => 'mycore',
            'timeout' => 3,
            'username' => 'x',
            'password' => 'y',
        );
        $this->endpoint->setOptions($options);

        $options['path'] = '/mysolr'; //expected trimming of trailing slash

        $this->assertEquals($options, $this->endpoint->getOptions());
    }

    public function testSetAndGetHost()
    {
        $this->endpoint->setHost('myhost');
        $this->assertEquals('myhost', $this->endpoint->getHost());
    }

    public function testSetAndGetPort()
    {
        $this->endpoint->setPort(8080);
        $this->assertEquals(8080, $this->endpoint->getPort());
    }

    public function testSetAndGetPath()
    {
        $this->endpoint->setPath('/mysolr');
        $this->assertEquals('/mysolr', $this->endpoint->getPath());
    }

    public function testSetAndGetPathWithTrailingSlash()
    {
        $this->endpoint->setPath('/mysolr/');
        $this->assertEquals('/mysolr', $this->endpoint->getPath());
    }

    public function testSetAndGetCore()
    {
        $this->endpoint->setCore('core1');
        $this->assertEquals('core1', $this->endpoint->getCore());
    }

    public function testSetAndGetTimeout()
    {
        $this->endpoint->setTimeout(7);
        $this->assertEquals(7, $this->endpoint->getTimeout());
    }

    public function testSetAndGetScheme()
    {
        $this->endpoint->setScheme('https');
        $this->assertEquals('https', $this->endpoint->getScheme());
    }

    public function testGetBaseUri()
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setPort(123);

        $this->assertEquals('http://myserver:123/mypath/', $this->endpoint->getBaseUri());
    }

    public function testGetBaseUriWithHttps()
    {
        $this->endpoint->setScheme('https')->setHost('myserver')->setPath('/mypath')->setPort(123);

        $this->assertEquals('https://myserver:123/mypath/', $this->endpoint->getBaseUri());
    }

    public function testGetBaseUriWithCore()
    {
        $this->endpoint->setHost('myserver')->setPath('/mypath')->setPort(123)->setCore('mycore');

        $this->assertEquals('http://myserver:123/mypath/mycore/', $this->endpoint->getBaseUri());
    }

    public function testGetAndSetAuthentication()
    {
        $user = 'someone';
        $pass = 'S0M3p455';

        $this->endpoint->setAuthentication($user, $pass);

        $this->assertEquals(
            array(
                'username' => $user,
                'password' => $pass,
            ),
            $this->endpoint->getAuthentication()
        );
    }

    public function testToString()
    {
        $options = array(
            'host' => '192.168.0.1',
            'port' => 123,
            'path' => '/mysolr/',
            'core' => 'mycore',
            'timeout' => 3,
            'username' => 'x',
            'password' => 'y',
        );
        $this->endpoint->setOptions($options);

        $endpoint = <<<EOF
Solarium\Core\Client\Endpoint::__toString
base uri: http://192.168.0.1:123/mysolr/mycore/
host: 192.168.0.1
port: 123
path: /mysolr
core: mycore
timeout: 3
authentication: Array
(
    [username] => x
    [password] => y
)

EOF;

        $this->assertEquals($endpoint, (string) $this->endpoint);
    }
}

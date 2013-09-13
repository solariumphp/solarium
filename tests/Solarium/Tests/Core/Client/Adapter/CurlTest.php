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

use Solarium\Core\Client\Adapter\Curl as CurlAdapter;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Exception;

class CurlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CurlAdapter
     */
    protected $adapter;

    public function setUp()
    {
        if (!function_exists('curl_init')) {
            $this->markTestSkipped('Curl not available, skipping Curl adapter tests');
        }

        $this->adapter = new CurlAdapter();
    }

    public function testCheck()
    {
        $data = 'data';
        $headers = array('X-dummy: data');
        $handler = curl_init();

        // this should be ok, no exception
        $this->adapter->check($data, $headers, $handler);

        $data = '';
        $headers = array();

        $this->setExpectedException('Solarium\Exception\HttpException');
        $this->adapter->check($data, $headers, $handler);

        curl_close($handler);
    }

    public function testExecute()
    {
        $headers = array('HTTP/1.0 200 OK');
        $body = 'data';
        $data = array($body, $headers);

        $request = new Request();
        $endpoint = new Endpoint();

        $mock = $this->getMock('Solarium\Core\Client\Adapter\Curl', array('getData'));
        $mock->expects($this->once())
                 ->method('getData')
                 ->with($request, $endpoint)
                 ->will($this->returnValue($data));

        $response = $mock->execute($request, $endpoint);

        $this->assertEquals($data, $response);
    }
}

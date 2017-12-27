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

use Solarium\Core\Client\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Request
     */
    protected $request;

    public function setup()
    {
        $this->request = new Request;
    }

    public function testConfigMode()
    {
        $options = array(
            'method' => Request::METHOD_POST,
            'handler' => 'myHandler',
            'param' => array(
                'param1' => 1,
                'param2' => 'test',
            ),
            'rawdata' => 'raw post data here',
            'header' => array(
                'myHeader1' => 'X-myHeader1: value1',
                'myHeader2' => 'X-myHeader2: value2',
            ),
            'authentication' => array(
                'username' => 'testuser',
                'password' => 'testpass',
            ),
            'file' => __FILE__,
        );
        $this->request->setOptions($options);

        $this->assertEquals(
            $options['method'],
            $this->request->getMethod()
        );

        $this->assertEquals(
            $options['handler'],
            $this->request->getHandler()
        );

        $this->assertEquals(
            $options['rawdata'],
            $this->request->getRawData()
        );

        $this->assertEquals(
            $options['param'],
            $this->request->getParams()
        );

        $this->assertEquals(
            array(
                $options['header']['myHeader1'],
                $options['header']['myHeader2']
            ),
            $this->request->getHeaders()
        );

        $this->assertEquals(
            array(
                'username' => $options['authentication']['username'],
                'password' => $options['authentication']['password'],
            ),
            $this->request->getAuthentication()
        );

        $this->assertEquals(
            $options['file'],
            $this->request->getFileUpload()
        );
    }

    public function testGetDefaultMethod()
    {
        $this->assertEquals(
            Request::METHOD_GET,
            $this->request->getMethod()
        );
    }

    public function testSetAndGetMethod()
    {
        $this->request->setMethod(Request::METHOD_POST);

        $this->assertEquals(
            Request::METHOD_POST,
            $this->request->getMethod()
        );
    }

    public function testSetAndGetHandler()
    {
        $this->request->setHandler('myhandler');

        $this->assertEquals(
            'myhandler',
            $this->request->getHandler()
        );
    }

    public function testSetAndGetParams()
    {
        $params = array(
            'param1' => 1,
            'param2' => 2,
        );

        $this->request->setParams($params);

        $this->assertEquals(
            $params,
            $this->request->getParams()
        );
    }

    public function testSetAndGetParam()
    {
        $params = array(
            'param1' => 1,
            'param2' => 2,
        );

        $this->request->setParams($params);

        $this->assertEquals(
            2,
            $this->request->getParam('param2')
        );
    }

    public function testGetInvalidParam()
    {
        $this->assertEquals(
            null,
            $this->request->getParam('invalidname')
        );
    }

    public function testAddParam()
    {
        $params = array(
            'param1' => 1,
            'param2' => 2,
        );

        $this->request->setParams($params);
        $this->request->addParam('param3', 3);

        $params['param3'] = 3;

        $this->assertEquals(
            $params,
            $this->request->getParams()
        );
    }

    public function testAddParamBoolean()
    {
        $params = array(
            'param1' => true,
            'param2' => false,
        );

        $this->request->addParams($params);

        $this->assertEquals(
            array(
                'param1' => 'true',
                'param2' => 'false',
            ),
            $this->request->getParams()
        );
    }

    public function testAddParamMultivalue()
    {
        $params = array(
            'param1' => 1,
        );

        $this->request->setParams($params);
        $this->request->addParam('param2', 2);
        $this->request->addParam('param2', 3);

        $params['param2'] = array(2, 3);

        $this->assertEquals(
            $params,
            $this->request->getParams()
        );
    }

    public function testAddParamNoValue()
    {
        $params = array(
            'param1' => 1,
            'param2' => 2,
            'param3' => 3,
        );

        $this->request->setParams($params);
        $this->request->addParam('param2', ''); // this should add an empty value to param2
        $this->request->addParam('param3', '', true); // this should overwrite param2 with an empty value
        $this->request->addParam('param4', ''); // this should add an empty param (for instance "q=" in dismax)
        $this->request->addParam('param5', null); // this param should be ignored

        $this->assertEquals(
            array(
                'param1' => 1,
                'param2' => array(2, ''),
                'param3' => '',
                'param4' => '',
            ),
            $this->request->getParams()
        );
    }

    public function testAddParamOverwrite()
    {
        $params = array(
            'param1' => 1,
        );

        $this->request->setParams($params);
        $this->request->addParam('param1', 2, true);

        $this->assertEquals(
            array('param1' => 2),
            $this->request->getParams()
        );
    }

    public function testAddParams()
    {
        $params = array(
            'param1' => 1,
        );

        $extraParams = array(
            'param1' => 2,
            'param2' => 3,
        );

        $this->request->setParams($params);
        $this->request->addParams($extraParams);

        $this->assertEquals(
            array(
                'param1' => array(1, 2),
                'param2' => 3,
            ),
            $this->request->getParams()
        );
    }

    public function testAddParamsOverwrite()
    {
        $params = array(
            'param1' => 1,
        );

        $extraParams = array(
            'param1' => 2,
            'param2' => 3,
        );

        $this->request->setParams($params);
        $this->request->addParams($extraParams, true);

        $this->assertEquals(
            array(
                'param1' => 2,
                'param2' => 3,
            ),
            $this->request->getParams()
        );
    }

    public function testRemoveParam()
    {
        $params = array(
            'param1' => 1,
            'param2' => 2,
        );

        $this->request->setParams($params);
        $this->request->removeParam('param2');

        $this->assertEquals(
            array('param1' => 1),
            $this->request->getParams()
        );
    }

    public function testClearParams()
    {
        $params = array(
            'param1' => 1,
            'param2' => 2,
        );

        $this->request->setParams($params);
        $this->request->clearParams();

        $this->assertEquals(
            array(),
            $this->request->getParams()
        );
    }

    public function testGetAndSetRawData()
    {
        $data = '1234567890';
        $this->request->setRawData($data);

        $this->assertEquals(
            $data,
            $this->request->getRawData()
        );
    }

    public function testSetAndGetHeaders()
    {
        $headers = array(
            'User-Agent: My Agent',
            'Cache-Control: no-cache'
        );
        $this->request->setHeaders($headers);

        $this->assertEquals(
            $headers,
            $this->request->getHeaders()
        );
    }

    public function testAddHeader()
    {
        $headers = array(
            'User-Agent: My Agent',

        );

        $this->request->setHeaders($headers);
        $this->request->addHeader('Cache-Control: no-cache');

        $headers[] = 'Cache-Control: no-cache';

        $this->assertEquals(
            $headers,
            $this->request->getHeaders()
        );
    }

    public function testAddHeaders()
    {
        $headers = array(
            'User-Agent: My Agent',

        );

        $extraHeaders = array(
            'Cache-Control: no-cache',
            'X-custom: 123',
        );

        $this->request->setHeaders($headers);
        $this->request->addHeaders($extraHeaders);

        $this->assertEquals(
            array_merge($headers, $extraHeaders),
            $this->request->getHeaders()
        );
    }

    public function testClearHeaders()
    {
        $headers = array(
            'User-Agent: My Agent',
            'Cache-Control: no-cache'
        );

        $this->request->setHeaders($headers);

        $this->assertEquals(
            $headers,
            $this->request->getHeaders()
        );

        $this->request->clearHeaders();

        $this->assertEquals(
            array(),
            $this->request->getHeaders()
        );
    }

    public function testGetUri()
    {
        $this->assertEquals(
            '?',
            $this->request->getUri()
        );
    }

    public function testGetUriWithHandlerAndParams()
    {
        $params = array(
            'param1' => 1,
            'param2' => array(2, 3),
        );

        $this->request->setHandler('myHandler');
        $this->request->addParams($params);

        $this->assertEquals(
            'myHandler?param1=1&param2=2&param2=3',
            $this->request->getUri()
        );
    }

    public function testToString()
    {
        $options = array(
            'method' => Request::METHOD_POST,
            'handler' => '/myHandler',
            'param' => array(
                'param1' => 1,
                'param2' => 'test content',
            ),
            'rawdata' => 'post data',
            'header' => array(
                'myHeader1' => 'X-myHeader1: value1',
                'myHeader2' => 'X-myHeader2: value2',
            ),
            'authentication' => array(
                'username' => 'testuser',
                'password' => 'testpass',
            ),
            'file' => __FILE__,
        );
        $this->request->setOptions($options);

        $request = <<<EOF
Solarium\Core\Client\Request::__toString
method: POST
header: Array
(
    [0] => X-myHeader1: value1
    [1] => X-myHeader2: value2
)
authentication: Array
(
    [username] => testuser
    [password] => testpass
)
resource: /myHandler?param1=1&param2=test+content
resource urldecoded: /myHandler?param1=1&param2=test content
raw data: post data
EOF;
        $request .= PHP_EOL.'file upload: '.__FILE__.PHP_EOL;

        $this->assertEquals($request, (string) $this->request);
    }

    public function testGetAndSetAuthentication()
    {
        $user = 'someone';
        $pass = 'S0M3p455';

        $this->request->setAuthentication($user, $pass);

        $this->assertEquals(
            array(
                'username' => $user,
                'password' => $pass,
            ),
            $this->request->getAuthentication()
        );
    }

    public function testSetAndGetFileUpload()
    {
        $this->request->setFileUpload(__FILE__);
        $this->assertEquals(
            __FILE__,
            $this->request->getFileUpload()
        );
    }

    public function testSetAndGetFileUploadWithInvalidFile()
    {
        $this->setExpectedException('Solarium\Exception\RuntimeException');
        $this->request->setFileUpload('invalid-filename.dummy');
    }
}

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

class Solarium_Client_RequestTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Client_Request
     */
    protected $_request;

    public function setup()
    {
        $this->_request = new Solarium_Client_Request;
    }

    public function testConfigMode()
    {
        $options = array(
            'method'   => Solarium_Client_Request::METHOD_POST,
            'handler'  => 'myHandler',
            'param'    => array(
                'param1' => 1,
                'param2' => 'test',
            ),
            'rawdata'  => 'raw post data here',
            'header'   => array(
                'myHeader1' => 'X-myHeader1: value1',
                'myHeader2' => 'X-myHeader2: value2',
            ),
        );
        $this->_request->setOptions($options);

        $this->assertEquals(
            $options['method'],
            $this->_request->getMethod()
        );

        $this->assertEquals(
            $options['handler'],
            $this->_request->getHandler()
        );

        $this->assertEquals(
            $options['rawdata'],
            $this->_request->getRawData()
        );

        $this->assertEquals(
            $options['param'],
            $this->_request->getParams()
        );

        $this->assertEquals(
            array(
                $options['header']['myHeader1'],
                $options['header']['myHeader2']
            ),
            $this->_request->getHeaders()
        );
    }

    public function testGetDefaultMethod()
    {
        $this->assertEquals(
            Solarium_Client_Request::METHOD_GET,
            $this->_request->getMethod()
        );
    }

    public function testSetAndGetMethod()
    {
        $this->_request->setMethod(Solarium_Client_Request::METHOD_POST);

        $this->assertEquals(
            Solarium_Client_Request::METHOD_POST,
            $this->_request->getMethod()
        );
    }

    public function testSetAndGetHandler()
    {
        $this->_request->setHandler('myhandler');

        $this->assertEquals(
            'myhandler',
            $this->_request->getHandler()
        );
    }

    public function testSetAndGetParams()
    {
        $params = array(
            'param1' => 1,
            'param2' => 2,
        );

        $this->_request->setParams($params);

        $this->assertEquals(
            $params,
            $this->_request->getParams()
        );
    }

    public function testSetAndGetParam()
    {
        $params = array(
            'param1' => 1,
            'param2' => 2,
        );

        $this->_request->setParams($params);

        $this->assertEquals(
            2,
            $this->_request->getParam('param2')
        );
    }

    public function testGetInvalidParam()
    {
        $this->assertEquals(
            null,
            $this->_request->getParam('invalidname')
        );
    }

    public function testAddParam()
    {
        $params = array(
            'param1' => 1,
            'param2' => 2,
        );

        $this->_request->setParams($params);
        $this->_request->addParam('param3', 3);

        $params['param3'] = 3;

        $this->assertEquals(
            $params,
            $this->_request->getParams()
        );
    }

    public function testAddParamBoolean()
    {
        $params = array(
            'param1' => true,
            'param2' => false,
        );

        $this->_request->addParams($params);

        $this->assertEquals(
            array(
                'param1' => 'true',
                'param2' => 'false',
            ),
            $this->_request->getParams()
        );
    }

    public function testAddParamMultivalue()
    {
        $params = array(
            'param1' => 1,
        );

        $this->_request->setParams($params);
        $this->_request->addParam('param2', 2);
        $this->_request->addParam('param2', 3);

        $params['param2'] = array(2, 3);

        $this->assertEquals(
            $params,
            $this->_request->getParams()
        );
    }

    public function testAddParamNoValue()
    {
        $params = array(
            'param1' => 1,
            'param2' => 2,
            'param3' => 3,
        );

        $this->_request->setParams($params);
        $this->_request->addParam('param2', ''); // this should add an empty value to param2
        $this->_request->addParam('param3', '' , true); // this should overwrite param2 with an empty value
        $this->_request->addParam('param4', ''); // this should add an empty param (for instance "q=" in dismax)
        $this->_request->addParam('param5', null); // this param should be ignored

        $this->assertEquals(
            array(
                'param1' => 1,
                'param2' => array(2,''),
                'param3' => '',
                'param4' => '',
            ),
            $this->_request->getParams()
        );
    }

    public function testAddParamOverwrite()
    {
        $params = array(
            'param1' => 1,
        );

        $this->_request->setParams($params);
        $this->_request->addParam('param1', 2, true);


        $this->assertEquals(
            array('param1' => 2),
            $this->_request->getParams()
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

        $this->_request->setParams($params);
        $this->_request->addParams($extraParams);


        $this->assertEquals(
            array(
                'param1' => array(1,2),
                'param2' => 3,
            ),
            $this->_request->getParams()
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

        $this->_request->setParams($params);
        $this->_request->addParams($extraParams, true);


        $this->assertEquals(
            array(
                'param1' => 2,
                'param2' => 3,
            ),
            $this->_request->getParams()
        );
    }

    public function testRemoveParam()
    {
        $params = array(
            'param1' => 1,
            'param2' => 2,
        );

        $this->_request->setParams($params);
        $this->_request->removeParam('param2');

        $this->assertEquals(
            array('param1' => 1),
            $this->_request->getParams()
        );
    }

    public function testClearParams()
    {
        $params = array(
            'param1' => 1,
            'param2' => 2,
        );

        $this->_request->setParams($params);
        $this->_request->clearParams();

        $this->assertEquals(
            array(),
            $this->_request->getParams()
        );
    }

    public function testGetAndSetRawData()
    {
        $data = '1234567890';
        $this->_request->setRawData($data);

        $this->assertEquals(
            $data,
            $this->_request->getRawData()
        );
    }

    public function testSetAndGetHeaders()
    {
        $headers = array(
            'User-Agent: My Agent',
            'Cache-Control: no-cache'
        );
        $this->_request->setHeaders($headers);

        $this->assertEquals(
            $headers,
            $this->_request->getHeaders()
        );
    }

    public function testAddHeader()
    {
        $headers = array(
            'User-Agent: My Agent',

        );

        $this->_request->setHeaders($headers);
        $this->_request->addHeader('Cache-Control: no-cache');

        $headers[] = 'Cache-Control: no-cache';

        $this->assertEquals(
            $headers,
            $this->_request->getHeaders()
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

        $this->_request->setHeaders($headers);
        $this->_request->addHeaders($extraHeaders);

        $this->assertEquals(
            array_merge($headers, $extraHeaders),
            $this->_request->getHeaders()
        );
    }

    public function testClearHeaders()
    {
        $headers = array(
            'User-Agent: My Agent',
            'Cache-Control: no-cache'
        );

        $this->_request->setHeaders($headers);

        $this->assertEquals(
            $headers,
            $this->_request->getHeaders()
        );

        $this->_request->clearHeaders();

        $this->assertEquals(
            array(),
            $this->_request->getHeaders()
        );
    }

    public function testGetUri()
    {
        $this->assertEquals(
            '?',
            $this->_request->getUri()
        );
    }

    public function testGetUriWithHandlerAndParams()
    {
        $params = array(
            'param1' => 1,
            'param2' => array(2,3),
        );

        $this->_request->setHandler('myHandler');
        $this->_request->addParams($params);

        $this->assertEquals(
            'myHandler?param1=1&param2=2&param2=3',
            $this->_request->getUri()
        );
    }

    public function testToString()
    {
        $options = array(
            'method'   => Solarium_Client_Request::METHOD_POST,
            'handler'  => '/myHandler',
            'param'    => array(
                'param1' => 1,
                'param2' => 'test content',
            ),
            'rawdata'  => 'post data',
            'header'   => array(
                'myHeader1' => 'X-myHeader1: value1',
                'myHeader2' => 'X-myHeader2: value2',
            ),
        );
        $this->_request->setOptions($options);

        $this->assertEquals(
'Solarium_Client_Request::toString
method: POST
header: Array
(
    [0] => X-myHeader1: value1
    [1] => X-myHeader2: value2
)
resource: /myHandler?param1=1&param2=test+content
resource urldecoded: /myHandler?param1=1&param2=test content
raw data: post data
',
            (string)$this->_request
        );
    }

}
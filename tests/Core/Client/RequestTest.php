<?php

namespace Solarium\Tests\Core\Client;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\Exception\RuntimeException;

class RequestTest extends TestCase
{
    /**
     * @var Request
     */
    protected $request;

    public function setUp(): void
    {
        $this->request = new Request();
    }

    public function testConfigMode()
    {
        $options = [
            'handler' => 'myHandler',
            'method' => Request::METHOD_POST,
            'contenttype' => 'example/test',
            'contenttypeparams' => ['param' => 'value'],
            'param' => [
                'param1' => 1,
                'param2' => 'test',
            ],
            'rawdata' => 'raw post data here',
            'header' => [
                'myHeader1' => 'X-myHeader1: value1',
                'myHeader2' => 'X-myHeader2: value2',
            ],
            'authentication' => [
                'username' => 'testuser',
                'password' => 'testpass',
            ],
            'file' => __FILE__,
        ];
        $this->request->setOptions($options);

        $this->assertSame(
            $options['handler'],
            $this->request->getHandler()
        );

        $this->assertSame(
            $options['method'],
            $this->request->getMethod()
        );

        $this->assertSame(
            $options['contenttype'],
            $this->request->getContentType()
        );

        $this->assertSame(
            $options['contenttypeparams'],
            $this->request->getContentTypeParams()
        );

        $this->assertSame(
            $options['rawdata'],
            $this->request->getRawData()
        );

        $this->assertSame(
            $options['file'],
            $this->request->getFileUpload()
        );

        $this->assertSame(
            [
                $options['header']['myHeader1'],
                $options['header']['myHeader2'],
                'Content-Type: example/test; param=value',
            ],
            $this->request->getHeaders()
        );

        $this->assertSame(
            [
                'username' => $options['authentication']['username'],
                'password' => $options['authentication']['password'],
            ],
            $this->request->getAuthentication()
        );

        $this->assertSame(
            $options['param'],
            $this->request->getParams()
        );
    }

    public function testToString()
    {
        $options = [
            'handler' => '/myHandler',
            'method' => Request::METHOD_POST,
            'contenttype' => 'example/test',
            'contenttypeparams' => ['param1' => 'value1', 'param2' => 'value2'],
            'param' => [
                'param1' => 1,
                'param2' => 'test content',
            ],
            'rawdata' => 'post data',
            'header' => [
                'myHeader1' => 'X-myHeader1: value1',
                'myHeader2' => 'X-myHeader2: value2',
            ],
            'authentication' => [
                'username' => 'testuser',
                'password' => 'testpass',
            ],
            'file' => __FILE__,
        ];
        $this->request->setOptions($options);

        $request = <<<EOF
Solarium\Core\Client\Request::__toString
method: POST
header: Array
(
    [0] => X-myHeader1: value1
    [1] => X-myHeader2: value2
    [2] => Content-Type: example/test; param1=value1; param2=value2
)
authentication: Array
(
    [username] => testuser
    [password] => testpass
)
resource: /myHandler?param1=1&param2=test+content
resource urldecoded: /myHandler?param1=1&param2=test content
raw data: post data
file upload: %s

EOF;

        $this->assertSame(sprintf($request, __FILE__), (string) $this->request);
    }

    public function testSetAndGetHandler()
    {
        $this->request->setHandler('myhandler');

        $this->assertSame(
            'myhandler',
            $this->request->getHandler()
        );
    }

    public function testGetDefaultMethod()
    {
        $this->assertSame(
            Request::METHOD_GET,
            $this->request->getMethod()
        );
    }

    public function testSetAndGetMethod()
    {
        $this->request->setMethod(Request::METHOD_POST);

        $this->assertSame(
            Request::METHOD_POST,
            $this->request->getMethod()
        );
    }

    public function testSetAndGetContentType()
    {
        $this->request->setContentType('example/test');

        $this->assertSame(
            'example/test',
            $this->request->getContentType()
        );

        $this->assertNull(
            $this->request->getContentTypeParams()
        );
    }

    public function testSetContentTypeWithParams()
    {
        $this->request->setContentType('example/params', ['param' => 'value']);

        $this->assertSame(
            'example/params',
            $this->request->getContentType()
        );

        $this->assertSame(
            ['param' => 'value'],
            $this->request->getContentTypeParams()
        );
    }

    public function testSetContentTypeWithParamsOverridesParams()
    {
        $this->request->setContentTypeParams(['param' => 'value']);
        $this->request->setContentType('example/params', ['newparam' => 'newvalue']);

        $this->assertSame(
            'example/params',
            $this->request->getContentType()
        );

        $this->assertSame(
            ['newparam' => 'newvalue'],
            $this->request->getContentTypeParams()
        );
    }

    /**
     * Test that we don't lose the parameters if they are set before the Content-Type.
     */
    public function testSetContentTypeWithoutParamsDoesntOverrideParams()
    {
        $this->request->setContentTypeParams(['param' => 'value']);
        $this->request->setContentType('example/params');

        $this->assertSame(
            'example/params',
            $this->request->getContentType()
        );

        $this->assertSame(
            ['param' => 'value'],
            $this->request->getContentTypeParams()
        );
    }

    public function testSetAndGetContentTypeParams()
    {
        $this->request->setContentTypeParams(['param' => 'value']);

        $this->assertSame(
            ['param' => 'value'],
            $this->request->getContentTypeParams()
        );
    }

    public function testSetAndGetRawData()
    {
        $data = '1234567890';
        $this->request->setRawData($data);

        $this->assertSame(
            $data,
            $this->request->getRawData()
        );
    }

    public function testSetAndGetFileUpload()
    {
        $this->request->setFileUpload(__FILE__);
        $this->assertSame(
            __FILE__,
            $this->request->getFileUpload()
        );
    }

    public function testSetAndGetFileUploadWithInvalidFile()
    {
        $this->expectException(RuntimeException::class);
        $this->request->setFileUpload('invalid-filename.dummy');
    }

    public function testSetAndGetFileUploadWithResourceInReadMode()
    {
        $file = fopen(__FILE__, 'r');
        $this->request->setFileUpload($file);
        $this->assertSame(
            $file,
            $this->request->getFileUpload()
        );
        fclose($file);
    }

    public function testSetAndGetFileUploadWithResourceInReadWriteMode()
    {
        // we use append lest we overwrite __FILE__
        $file = fopen(__FILE__, 'a+');
        $this->request->setFileUpload($file);
        $this->assertSame(
            $file,
            $this->request->getFileUpload()
        );
        fclose($file);
    }

    public function testSetAndGetFileUploadWithResourceInWriteMode()
    {
        // we use append lest we overwrite __FILE__
        $file = fopen(__FILE__, 'a');
        $this->expectException(RuntimeException::class);
        $this->request->setFileUpload($file);
        fclose($file);
    }

    public function testSetAndGetHeaders()
    {
        $headers = [
            'User-Agent: My Agent',
            'Cache-Control: no-cache',
        ];
        $this->request->setHeaders($headers);

        $this->assertSame(
            $headers,
            $this->request->getHeaders()
        );
    }

    public function testGetHeadersUnique()
    {
        $headers = [
            'X-MyHeader: value',
            'X-MyHeader: value',
        ];
        $this->request->setHeaders($headers);

        $this->assertSame(
            [
                'X-MyHeader: value',
            ],
            $this->request->getHeaders()
        );
    }

    public function testGetHeadersWithContentTypeAndContentTypeParams()
    {
        $this->request->addParam('ie', 'us-ascii');
        $this->request->setContentType('example/test');
        $this->request->setContentTypeParams(['param1' => 'value1', 'param2' => 'value2']);

        $this->assertSame(
            [
                'Content-Type: example/test; param1=value1; param2=value2',
            ],
            $this->request->getHeaders()
        );
    }

    public function testGetHeadersWithContentTypeWithoutContentTypeParams()
    {
        $this->request->addParam('ie', 'us-ascii');
        $this->request->setContentType('example/test');

        $this->assertSame(
            [
                'Content-Type: example/test; charset=us-ascii',
            ],
            $this->request->getHeaders()
        );
    }

    public function testGetHeadersWithContentTypeWithoutContentTypeParamsWithDefaultCharset()
    {
        $this->request->setContentType('example/test');

        $this->assertSame(
            [
                'Content-Type: example/test; charset=utf-8',
            ],
            $this->request->getHeaders()
        );
    }

    public function testGetHeadersWithContentTypeWithEmptyContentTypeParams()
    {
        $this->request->addParam('ie', 'us-ascii');
        $this->request->setContentType('example/test');
        $this->request->setContentTypeParams([]);

        $this->assertSame(
            [
                'Content-Type: example/test',
            ],
            $this->request->getHeaders()
        );
    }

    /**
     * Test that a raw Content-Type header always takes precedence over the 'contenttype' option.
     */
    public function testGetHeadersWithRawContentTypeHeaderAndContentType()
    {
        $this->request->addHeader('Content-Type: example/raw');
        $this->request->setContentType('example/option');

        $this->assertSame(
            [
                'Content-Type: example/raw',
            ],
            $this->request->getHeaders()
        );
    }

    /**
     * Test that a raw Content-Type header always takes precedence over the 'contenttype' option.
     */
    public function testGetHeadersWithContentTypeAndRawContentTypeHeader()
    {
        $this->request->setContentType('example/option');
        $this->request->addHeader('Content-Type: example/raw');

        $this->assertSame(
            [
                'Content-Type: example/raw',
            ],
            $this->request->getHeaders()
        );
    }

    public function testAddHeader()
    {
        $headers = [
            'User-Agent: My Agent',
        ];

        $this->request->setHeaders($headers);
        $this->request->addHeader('Cache-Control: no-cache');

        $headers[] = 'Cache-Control: no-cache';

        $this->assertSame(
            $headers,
            $this->request->getHeaders()
        );
    }

    public function testAddHeaders()
    {
        $headers = [
            'User-Agent: My Agent',
        ];

        $extraHeaders = [
            'Cache-Control: no-cache',
            'X-custom: 123',
        ];

        $this->request->setHeaders($headers);
        $this->request->addHeaders($extraHeaders);

        $this->assertSame(
            array_merge($headers, $extraHeaders),
            $this->request->getHeaders()
        );
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testReplaceHeaders(): void
    {
        $original = 'Content-Type: application/xml';
        $replacement = 'Content-Type: application/json';

        $this->request->replaceOrAddHeader($original);

        $this->assertSame($original, $this->request->getHeader('Content-Type'));

        $this->request->replaceOrAddHeader($replacement);

        $this->assertSame($replacement, $this->request->getHeader('Content-Type'));
    }

    public function testClearHeaders()
    {
        $headers = [
            'User-Agent: My Agent',
            'Cache-Control: no-cache',
        ];

        $this->request->setHeaders($headers);

        $this->assertSame(
            $headers,
            $this->request->getHeaders()
        );

        $this->request->clearHeaders();

        $this->assertSame(
            [],
            $this->request->getHeaders()
        );
    }

    public function testGetUri()
    {
        $this->assertSame(
            '',
            $this->request->getUri()
        );
    }

    public function testGetUriWithHandler()
    {
        $this->request->setHandler('myHandler');
        $this->assertSame(
            'myHandler',
            $this->request->getUri()
        );
    }

    public function testGetUriWithHandlerAndParams()
    {
        $params = [
            'param1' => 1,
            'param2' => [2, 3],
        ];

        $this->request->setHandler('myHandler');
        $this->request->addParams($params);

        $this->assertSame(
            'myHandler?param1=1&param2=2&param2=3',
            $this->request->getUri()
        );
    }

    public function testSetAndGetAuthentication()
    {
        $user = 'someone';
        $pass = 'S0M3p455';

        $this->request->setAuthentication($user, $pass);

        $this->assertSame(
            [
                'username' => $user,
                'password' => $pass,
            ],
            $this->request->getAuthentication()
        );
    }

    public function testSetAuthenticationSensitiveParameter()
    {
        // #[\SensitiveParameter] was introduced in PHP 8.2
        if (!class_exists('\SensitiveParameter')) {
            $this->expectNotToPerformAssertions();

            return;
        }

        try {
            // trigger a \TypeError with the $user argument
            $this->request->setAuthentication(null, 'S0M3p455');
        } catch (\TypeError $e) {
            $trace = $e->getTrace();

            // \SensitiveParameterValue::class trips phpstan in PHP versions that don't support it
            $this->assertInstanceOf('\SensitiveParameterValue', $trace[0]['args'][1]);
        }
    }

    public function testSetAndGetIsServerRequest()
    {
        $this->request->setIsServerRequest();

        $this->assertFalse(
            $this->request->getIsServerRequest()
        );

        $this->request->setIsServerRequest(true);

        $this->assertTrue(
            $this->request->getIsServerRequest()
        );
    }

    public function testGetDefaultApi()
    {
        $this->assertSame(
            Request::API_V1,
            $this->request->getApi()
        );
    }

    public function testSetAndGetApi()
    {
        $this->request->setApi(Request::API_V2);

        $this->assertSame(
            Request::API_V2,
            $this->request->getApi()
        );
    }

    public function testGetHash()
    {
        $hash1 = $this->request->getHash();
        $hash2 = (new Request())->getHash();

        $this->assertNotEquals(
            $hash2,
            $hash1
        );
    }

    public function testSetAndGetParams()
    {
        $params = [
            'param1' => 1,
            'param2' => 2,
        ];

        $this->request->setParams($params);

        $this->assertSame(
            $params,
            $this->request->getParams()
        );
    }

    public function testSetAndGetParam()
    {
        $params = [
            'param1' => 1,
            'param2' => 2,
        ];

        $this->request->setParams($params);

        $this->assertSame(
            2,
            $this->request->getParam('param2')
        );
    }

    public function testGetInvalidParam()
    {
        $this->assertNull(
            $this->request->getParam('invalidname')
        );
    }

    public function testAddParam()
    {
        $params = [
            'param1' => 1,
            'param2' => 2,
        ];

        $this->request->setParams($params);
        $this->request->addParam('param3', 3);

        $params['param3'] = 3;

        $this->assertSame(
            $params,
            $this->request->getParams()
        );
    }

    public function testAddParamBoolean()
    {
        $params = [
            'param1' => true,
            'param2' => false,
        ];

        $this->request->addParams($params);

        $this->assertSame(
            [
                'param1' => 'true',
                'param2' => 'false',
            ],
            $this->request->getParams()
        );
    }

    public function testAddParamMultivalue()
    {
        $params = [
            'param1' => 1,
        ];

        $this->request->setParams($params);
        $this->request->addParam('param2', 2);
        $this->request->addParam('param2', 3);

        $params['param2'] = [2, 3];

        $this->assertSame(
            $params,
            $this->request->getParams()
        );
    }

    public function testAddParamNoValue()
    {
        $params = [
            'param1' => 1,
            'param2' => 2,
            'param3' => 3,
        ];

        $this->request->setParams($params);
        $this->request->addParam('param2', ''); // this should add an empty value to param2
        $this->request->addParam('param3', '', true); // this should overwrite param2 with an empty value
        $this->request->addParam('param4', ''); // this should add an empty param (for instance "q=" in dismax)
        $this->request->addParam('param5', null); // this param should be ignored

        $this->assertSame(
            [
                'param1' => 1,
                'param2' => [2, ''],
                'param3' => '',
                'param4' => '',
            ],
            $this->request->getParams()
        );
    }

    public function testAddParamOverwrite()
    {
        $params = [
            'param1' => 1,
        ];

        $this->request->setParams($params);
        $this->request->addParam('param1', 2, true);

        $this->assertSame(
            ['param1' => 2],
            $this->request->getParams()
        );
    }

    public function testAddParams()
    {
        $params = [
            'param1' => 1,
        ];

        $extraParams = [
            'param1' => 2,
            'param2' => 3,
        ];

        $this->request->setParams($params);
        $this->request->addParams($extraParams);

        $this->assertSame(
            [
                'param1' => [1, 2],
                'param2' => 3,
            ],
            $this->request->getParams()
        );
    }

    public function testAddParamsOverwrite()
    {
        $params = [
            'param1' => 1,
        ];

        $extraParams = [
            'param1' => 2,
            'param2' => 3,
        ];

        $this->request->setParams($params);
        $this->request->addParams($extraParams, true);

        $this->assertSame(
            [
                'param1' => 2,
                'param2' => 3,
            ],
            $this->request->getParams()
        );
    }

    public function testRemoveParam()
    {
        $params = [
            'param1' => 1,
            'param2' => 2,
        ];

        $this->request->setParams($params);
        $this->request->removeParam('param2');

        $this->assertSame(
            ['param1' => 1],
            $this->request->getParams()
        );
    }

    public function testClearParams()
    {
        $params = [
            'param1' => 1,
            'param2' => 2,
        ];

        $this->request->setParams($params);
        $this->request->clearParams();

        $this->assertSame(
            [],
            $this->request->getParams()
        );
    }
}

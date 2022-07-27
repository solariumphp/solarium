<?php

/*
 * This file is part of the Solarium package.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

namespace Solarium\Tests\Plugin;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Adapter\AdapterInterface;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Request;
use Solarium\Core\Event\Events;
use Solarium\Core\Event\PostCreateRequest;
use Solarium\Plugin\PostBigExtractRequest;
use Solarium\Tests\Integration\TestClientFactory;

class PostBigExtractRequestTest extends TestCase
{
    /**
     * @var PostBigExtractRequest
     */
    protected $plugin;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Solarium\QueryType\Extract\Query
     */
    protected $query;

    public function setUp(): void
    {
        $this->plugin = new PostBigExtractRequest();

        $this->client = TestClientFactory::createWithCurlAdapter();
        $this->query = $this->client->createExtract();
    }

    public function testInitPlugin(): Client
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $plugin = $client->getPlugin('postbigextractrequest');

        $this->assertInstanceOf(PostBigExtractRequest::class, $plugin);

        $expectedListeners = [
            Events::POST_CREATE_REQUEST => [
                [
                    $plugin,
                    'postCreateRequest',
                ],
            ],
        ];

        $this->assertSame(
            $expectedListeners,
            $client->getEventDispatcher()->getListeners()
        );

        return $client;
    }

    /**
     * @depends testInitPlugin
     */
    public function testDeinitPlugin(Client $client)
    {
        $client->removePlugin('postbigextractrequest');

        $this->assertSame(
            [],
            $client->getEventDispatcher()->getListeners()
        );
    }

    public function testSetAndGetMaxQueryStringLength()
    {
        $this->plugin->setMaxQueryStringLength(512);
        $this->assertSame(512, $this->plugin->getMaxQueryStringLength());
    }

    public function testPostCreateRequest()
    {
        $document = $this->query->createDocument();
        $document->field_1 = 'Field 1';
        $document->field_2 = 0;
        $document->field_3 = ['Field 3 a', 'Field 3 b'];
        $document->field_4 = [1, 2];
        $this->query->setDocument($document);

        $tmpfname = tempnam(sys_get_temp_dir(), 'tst');
        file_put_contents($tmpfname, 'Test file contents');
        $this->query->setFile($tmpfname);

        $requestOutput = $this->client->createRequest($this->query);
        $event = new PostCreateRequest($this->query, $requestOutput);
        $this->plugin->setMaxQueryStringLength(1)->postCreateRequest($event);

        $expectedRawDataRegex = <<<'REGEX'
~^--([[:xdigit:]]{32})\r\n
Content-Disposition:\ form-data;\ name="omitHeader"\r\n
Content-Type:\ text/plain;charset=UTF-8\r\n
\r\n
true\r\n
--\1\r\n
Content-Disposition:\ form-data;\ name="wt"\r\n
Content-Type:\ text/plain;charset=UTF-8\r\n
\r\n
json\r\n
--\1\r\n
Content-Disposition:\ form-data;\ name="json\.nl"\r\n
Content-Type:\ text/plain;charset=UTF-8\r\n
\r\n
flat\r\n
--\1\r\n
Content-Disposition:\ form-data;\ name="extractOnly"\r\n
Content-Type:\ text/plain;charset=UTF-8\r\n
\r\n
false\r\n
--\1\r\n
Content-Disposition:\ form-data;\ name="literal\.field_1"\r\n
Content-Type:\ text/plain;charset=UTF-8\r\n
\r\n
Field\ 1\r\n
--\1\r\n
Content-Disposition:\ form-data;\ name="literal\.field_2"\r\n
\r\n
0\r\n
--\1\r\n
Content-Disposition:\ form-data;\ name="literal\.field_3"\r\n
Content-Type:\ text/plain;charset=UTF-8\r\n
\r\n
Field\ 3\ a\r\n
--\1\r\n
Content-Disposition:\ form-data;\ name="literal\.field_3"\r\n
Content-Type:\ text/plain;charset=UTF-8\r\n
\r\n
Field\ 3\ b\r\n
--\1\r\n
Content-Disposition:\ form-data;\ name="literal\.field_4"\r\n
\r\n
1\r\n
--\1\r\n
Content-Disposition:\ form-data;\ name="literal\.field_4"\r\n
\r\n
2\r\n
--\1\r\n
Content-Disposition:\ form-data;\ name="resource\.name"\r\n
Content-Type:\ text/plain;charset=UTF-8\r\n
\r\n
(tst.+?)\r\n
--\1\r\n
Content-Disposition:\ form-data;\ name="file";\ filename="\2"\r\n
Content-Type:\ application/octet-stream\r\n
\r\n
Test\ file\ contents\r\n
--\1--\r\n
$~xD
REGEX;

        $this->assertSame(Request::METHOD_POST, $requestOutput->getMethod());
        $this->assertSame('', $requestOutput->getQueryString());
        $this->assertMatchesRegularExpression($expectedRawDataRegex, $requestOutput->getRawData());

        unlink($tmpfname);
    }

    public function testPostCreateRequestInputEncoding()
    {
        $this->query->setInputEncoding('ascii');

        $document = $this->query->createDocument();
        $document->field_1 = 'Field 1';
        $document->field_2 = 0;
        $this->query->setDocument($document);

        $tmpfname = tempnam(sys_get_temp_dir(), 'tst');
        file_put_contents($tmpfname, 'Test file contents');
        $this->query->setFile($tmpfname);

        $requestOutput = $this->client->createRequest($this->query);
        $event = new PostCreateRequest($this->query, $requestOutput);
        $this->plugin->setMaxQueryStringLength(1)->postCreateRequest($event);

        $expectedRawDataRegex = <<<'REGEX'
~^--([[:xdigit:]]{32})\r\n
Content-Disposition:\ form-data;\ name="omitHeader"\r\n
Content-Type:\ text/plain;charset=ascii\r\n
\r\n
true\r\n
--\1\r\n
Content-Disposition:\ form-data;\ name="ie"\r\n
Content-Type:\ text/plain;charset=ascii\r\n
\r\n
ascii\r\n
--\1\r\n
Content-Disposition:\ form-data;\ name="wt"\r\n
Content-Type:\ text/plain;charset=ascii\r\n
\r\n
json\r\n
--\1\r\n
Content-Disposition:\ form-data;\ name="json\.nl"\r\n
Content-Type:\ text/plain;charset=ascii\r\n
\r\n
flat\r\n
--\1\r\n
Content-Disposition:\ form-data;\ name="extractOnly"\r\n
Content-Type:\ text/plain;charset=ascii\r\n
\r\n
false\r\n
--\1\r\n
Content-Disposition:\ form-data;\ name="literal\.field_1"\r\n
Content-Type:\ text/plain;charset=ascii\r\n
\r\n
Field\ 1\r\n
--\1\r\n
Content-Disposition:\ form-data;\ name="literal\.field_2"\r\n
\r\n
0\r\n
--\1\r\n
Content-Disposition:\ form-data;\ name="resource\.name"\r\n
Content-Type:\ text/plain;charset=ascii\r\n
\r\n
(tst.+?)\r\n
--\1\r\n
Content-Disposition:\ form-data;\ name="file";\ filename="\2"\r\n
Content-Type:\ application/octet-stream\r\n
\r\n
Test\ file\ contents\r\n
--\1--\r\n
$~xD
REGEX;

        $this->assertSame(Request::METHOD_POST, $requestOutput->getMethod());
        $this->assertSame('', $requestOutput->getQueryString());
        $this->assertMatchesRegularExpression($expectedRawDataRegex, $requestOutput->getRawData());

        unlink($tmpfname);
    }

    public function testPostCreateRequestUnalteredSmallRequest()
    {
        $this->query->setFile(__FILE__);
        $requestOutput = $this->client->createRequest($this->query);
        $requestInput = clone $requestOutput;
        $event = new PostCreateRequest($this->query, $requestOutput);
        $this->plugin->postCreateRequest($event);

        $this->assertEquals($requestInput, $requestOutput);
    }

    public function testPostCreateRequestUnalteredPostRequest()
    {
        $query = $this->client->createUpdate();
        $query->addDeleteById(1);

        $requestOutput = $this->client->createRequest($query);
        $requestInput = clone $requestOutput;
        $event = new PostCreateRequest($query, $requestOutput);
        $this->plugin->postCreateRequest($event);

        $this->assertEquals($requestInput, $requestOutput);
    }

    public function testPluginIntegration()
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $client->registerPlugin('testplugin', $this->plugin);
        $this->plugin->setMaxQueryStringLength(1); // this forces literals moved from query string for even the smallest queries

        $query = $this->client->createExtract();
        $query->setCommit(true);
        $document = $query->createDocument();
        $document['field'] = 'Ipse dixit.';
        $query->setDocument($document);
        $query->setFile(__FILE__);
        $query->setOmitHeader(false);

        $request = $client->createRequest($query);
        $adapter = $this->createMock(AdapterInterface::class);
        $client->setAdapter($adapter);
        $response = $client->executeRequest($request);

        // default extract method is POST
        $this->assertSame(Request::METHOD_POST, $request->getMethod());
    }
}

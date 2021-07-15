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
use Solarium\Core\Event\PostCreateRequest as PostCreateRequest;
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

    public function testSetAndGetMaxQueryStringLength()
    {
        $this->plugin->setMaxQueryStringLength(512);
        $this->assertSame(512, $this->plugin->getMaxQueryStringLength());
    }

    public function testPostCreateRequest()
    {
        $document = $this->query->createDocument();
        // create a very long list of literals
        for ($i = 1; $i <= 100; ++$i) {
            $field_name = "field_{$i}";
            $document->$field_name = "value $i";
        }
        $this->query->setDocument($document);
        $this->query->setFile(__FILE__);

        $requestOutput = $this->client->createRequest($this->query);
        $requestInput = clone $requestOutput;
        $event = new PostCreateRequest($this->query, $requestOutput);
        $this->plugin->postCreateRequest($event);

        $this->assertSame(Request::METHOD_POST, $requestOutput->getMethod());
        $nl_pattern = '(?:\\r\\n|\\r|\\n)';
        foreach (explode('&', urldecode($requestInput->getQueryString())) as $qs_parameter) {
            $qs_parameter_arr = explode('=', $qs_parameter);
            $qs_parameter_name = $qs_parameter_arr[0];
            $qs_parameter_value = $qs_parameter_arr[1];
            $pattern = "/^-{2}.*?$nl_pattern+^Content-Disposition: form-data; name=\"".preg_quote($qs_parameter_name)."\"$nl_pattern+Content-Type:.*?$nl_pattern+".preg_quote($qs_parameter_value).'/m';
            $this->assertMatchesRegularExpression($pattern, $requestOutput->getRawData());
        }
        $this->assertSame('', $requestOutput->getQueryString());
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

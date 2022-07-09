<?php

namespace Solarium\Tests\Plugin;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Adapter\AdapterInterface;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Request;
use Solarium\Core\Event\Events;
use Solarium\Core\Event\PreExecuteRequest as PreExecuteRequestEvent;
use Solarium\Plugin\PostBigRequest;
use Solarium\QueryType\Select\Query\Query;
use Solarium\Tests\Integration\TestClientFactory;

class PostBigRequestTest extends TestCase
{
    /**
     * @var PostBigRequest
     */
    protected $plugin;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Query
     */
    protected $query;

    public function setUp(): void
    {
        $this->plugin = new PostBigRequest();

        $this->client = TestClientFactory::createWithCurlAdapter();
        $this->query = $this->client->createSelect();
    }

    public function testInitPlugin(): Client
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $plugin = $client->getPlugin('postbigrequest');

        $this->assertInstanceOf(PostBigRequest::class, $plugin);

        $expectedListeners = [
            Events::PRE_EXECUTE_REQUEST => [
                [
                    $plugin,
                    'preExecuteRequest',
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
        $client->removePlugin('postbigrequest');

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

    public function testPreExecuteRequest()
    {
        // create a very long query
        $fq = '';
        for ($i = 1; $i <= 1000; ++$i) {
            $fq .= ' OR price:'.$i;
        }
        $fq = substr($fq, 4);
        $this->query->createFilterQuery('fq')->setQuery($fq);
        $this->query->setInputEncoding('us-ascii');

        $requestOutput = $this->client->createRequest($this->query);
        $requestInput = clone $requestOutput;
        $endpoint = $this->client->getEndpoint();
        $event = new PreExecuteRequestEvent($requestOutput, $endpoint);
        $this->plugin->preExecuteRequest($event);

        $this->assertSame(Request::METHOD_GET, $requestInput->getMethod());
        $this->assertSame(Request::METHOD_POST, $requestOutput->getMethod());
        $this->assertSame(Request::CONTENT_TYPE_APPLICATION_X_WWW_FORM_URLENCODED, $requestOutput->getContentType());
        $this->assertSame(['charset' => 'us-ascii'], $requestOutput->getContentTypeParams());
        $this->assertSame($requestInput->getQueryString(), $requestOutput->getRawData());
        $this->assertSame('', $requestOutput->getQueryString());
    }

    public function testPreExecuteRequestUnalteredSmallRequest()
    {
        $requestOutput = $this->client->createRequest($this->query);
        $requestInput = clone $requestOutput;
        $endpoint = $this->client->getEndpoint();
        $event = new PreExecuteRequestEvent($requestOutput, $endpoint);
        $this->plugin->preExecuteRequest($event);

        $this->assertEquals($requestInput, $requestOutput);
    }

    public function testPreExecuteRequestUnalteredPostRequest()
    {
        $query = $this->client->createUpdate();
        $query->addDeleteById(1);

        $requestOutput = $this->client->createRequest($query);
        $requestInput = clone $requestOutput;
        $endpoint = $this->client->getEndpoint();
        $event = new PreExecuteRequestEvent($requestOutput, $endpoint);
        $this->plugin->preExecuteRequest($event);

        $this->assertEquals($requestInput, $requestOutput);
    }

    public function testPluginIntegration()
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $client->registerPlugin('testplugin', $this->plugin);
        $this->plugin->setMaxQueryStringLength(1); // this forces POST for even the smallest queries

        $query = $client->createSelect();
        $request = $client->createRequest($query);
        $adapter = $this->createMock(AdapterInterface::class);
        $client->setAdapter($adapter);
        $response = $client->executeRequest($request);

        // default method is GET, the plugin should have changed this to POST
        $this->assertSame(Request::METHOD_POST, $request->getMethod());
    }
}

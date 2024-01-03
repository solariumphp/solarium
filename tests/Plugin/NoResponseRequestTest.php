<?php

namespace Solarium\Tests\Plugin;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Adapter\AdapterInterface;
use Solarium\Core\Client\Adapter\TimeoutAwareInterface;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Request;
use Solarium\Core\Client\Response;
use Solarium\Core\Event\Events;
use Solarium\Core\Event\PostExecuteRequest;
use Solarium\Core\Event\PreExecuteRequest as PreExecuteRequestEvent;
use Solarium\Plugin\NoResponseRequest;
use Solarium\QueryType\Select\Query\Query;
use Solarium\Tests\Integration\TestClientFactory;

class NoResponseRequestTest extends TestCase
{
    /**
     * @var \Solarium\Plugin\NoResponseRequest
     */
    protected $plugin;

    /**
     * @var \Solarium\Core\Client\Adapter\Curl
     */
    protected $client;

    /**
     * @var Query
     */
    protected $query;

    public function setUp(): void
    {
        $this->client = TestClientFactory::createWithCurlAdapter();
        $this->plugin = $this->client->getPlugin('noresponserequest');
        $this->query = $this->client->createSuggester(['buildAll' => true]);
    }

    public function testInitPlugin(): Client
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $plugin = $client->getPlugin('noresponserequest');

        $this->assertInstanceOf(NoResponseRequest::class, $plugin);

        $expectedListeners = [
            Events::PRE_EXECUTE_REQUEST => [
                [
                    $plugin,
                    'preExecuteRequest',
                ],
            ],
            Events::POST_EXECUTE_REQUEST => [
                [
                    $plugin,
                    'postExecuteRequest',
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
        $client->removePlugin('noresponserequest');

        $this->assertSame(
            [],
            $client->getEventDispatcher()->getListeners()
        );
    }

    public function testExecuteRequest()
    {
        $requestOutput = $this->client->createRequest($this->query);
        $requestInput = clone $requestOutput;
        $endpoint = $this->client->getEndpoint();
        $event = new PreExecuteRequestEvent($requestOutput, $endpoint);
        $this->plugin->preExecuteRequest($event);

        $this->assertSame(Request::METHOD_GET, $requestInput->getMethod());
        $this->assertSame(Request::METHOD_POST, $requestOutput->getMethod());
        $this->assertSame(Request::CONTENT_TYPE_APPLICATION_X_WWW_FORM_URLENCODED, $requestOutput->getContentType());
        $this->assertSame($requestInput->getQueryString(), $requestOutput->getRawData());
        $this->assertSame('', $requestOutput->getQueryString());
        $this->assertSame(TimeoutAwareInterface::MINIMUM_TIMEOUT, $this->client->getAdapter()->getTimeout());
        $this->assertFalse($this->client->getAdapter()->getOption('return_transfer'));

        $event = new PostExecuteRequest($requestOutput, $endpoint, new Response('response'));
        $this->plugin->preExecuteRequest($event);

        $this->assertSame(TimeoutAwareInterface::DEFAULT_TIMEOUT, $this->client->getAdapter()->getTimeout());
        $this->assertTrue($this->client->getAdapter()->getOption('return_transfer'));
    }

    public function testPluginIntegration()
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $plugin = new NoResponseRequest();
        $client->registerPlugin('testplugin', $plugin);

        $query = $client->createSelect();
        $request = $client->createRequest($query);
        $adapter = $this->createMock(AdapterInterface::class);
        $client->setAdapter($adapter);
        $response = $client->executeRequest($request);

        // default method is GET, the plugin should have changed this to POST
        $this->assertSame(Request::METHOD_POST, $request->getMethod());
    }
}

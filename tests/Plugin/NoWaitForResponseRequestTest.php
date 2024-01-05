<?php

namespace Solarium\Tests\Plugin;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Adapter\AdapterInterface;
use Solarium\Core\Client\Adapter\TimeoutAwareInterface;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\Request;
use Solarium\Core\Event\Events;
use Solarium\Core\Event\PreExecuteRequest as PreExecuteRequestEvent;
use Solarium\Plugin\NoWaitForResponseRequest;
use Solarium\QueryType\Select\Query\Query;
use Solarium\Tests\Integration\TestClientFactory;

class NoWaitForResponseRequestTest extends TestCase
{
    /**
     * @var \Solarium\Plugin\NoWaitForResponseRequest
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
        $this->plugin = $this->client->getPlugin('nowaitforresponserequest');
        $this->query = $this->client->createSuggester(['buildAll' => true]);
    }

    public function testInitPlugin(): Client
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $plugin = $client->getPlugin('nowaitforresponserequest');

        $this->assertInstanceOf(NoWaitForResponseRequest::class, $plugin);

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
        $client->removePlugin('nowaitforresponserequest');

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
        $endpoint->setCore('my_core');
        $event = new PreExecuteRequestEvent($requestOutput, $endpoint);
        $this->plugin->preExecuteRequest($event);
        $response = $event->getResponse();

        $this->assertSame(Request::METHOD_GET, $requestInput->getMethod());
        $this->assertSame(Request::METHOD_POST, $requestOutput->getMethod());
        $this->assertSame(Request::CONTENT_TYPE_APPLICATION_X_WWW_FORM_URLENCODED, $requestOutput->getContentType());
        $this->assertSame($requestInput->getQueryString(), $requestOutput->getRawData());
        $this->assertSame('', $requestOutput->getQueryString());
        $this->assertSame(200, $response->getStatusCode());

        // The client should be configured with defaults again, after these
        // settings changed within the event subscriber.
        $this->assertSame(TimeoutAwareInterface::DEFAULT_TIMEOUT, $this->client->getAdapter()->getTimeout());
        $this->assertTrue($this->client->getAdapter()->getOption('return_transfer'));
    }

    public function testPluginIntegration()
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $plugin = new NoWaitForResponseRequest();
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

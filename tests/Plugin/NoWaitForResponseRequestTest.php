<?php

namespace Solarium\Tests\Plugin;

use PHPUnit\Framework\TestCase;
use Solarium\Client;
use Solarium\Core\Client\Adapter\AdapterInterface;
use Solarium\Core\Client\Adapter\Curl;
use Solarium\Core\Client\Adapter\Http;
use Solarium\Core\Client\Adapter\TimeoutAwareInterface;
use Solarium\Core\Client\Endpoint;
use Solarium\Core\Client\Request;
use Solarium\Core\Event\Events;
use Solarium\Core\Event\PreExecuteRequest as PreExecuteRequestEvent;
use Solarium\Exception\HttpException;
use Solarium\Plugin\NoWaitForResponseRequest;
use Solarium\QueryType\Select\Query\Query;
use Solarium\Tests\Integration\TestClientFactory;

class NoWaitForResponseRequestTest extends TestCase
{
    /**
     * @var NoWaitForResponseRequest
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

    public function testSetFastTimeout()
    {
        $observer = new class() extends Http {
            public $newTimeout;

            public function setTimeout(int $timeoutInSeconds): self
            {
                if (!isset($this->newTimeout)) {
                    $this->newTimeout = $timeoutInSeconds;
                }

                return parent::setTimeout($timeoutInSeconds);
            }
        };
        $this->client->setAdapter($observer);

        $requestOutput = $this->client->createRequest($this->query);
        $endpoint = $this->client->getEndpoint();
        $endpoint->setCore('my_core');
        $event = new PreExecuteRequestEvent($requestOutput, $endpoint);
        $this->plugin->preExecuteRequest($event);

        $this->assertSame(TimeoutAwareInterface::FAST_TIMEOUT, $observer->newTimeout);
    }

    public function testSetFastTimeoutWithConnectionTimeout()
    {
        $observer = new class() extends Curl {
            public $newTimeout;

            public function setTimeout(int $timeoutInSeconds): self
            {
                if (!isset($this->newTimeout)) {
                    $this->newTimeout = $timeoutInSeconds;
                }

                return parent::setTimeout($timeoutInSeconds);
            }
        };
        $observer->setConnectionTimeout(1);
        $this->client->setAdapter($observer);

        $requestOutput = $this->client->createRequest($this->query);
        $endpoint = $this->client->getEndpoint();
        $endpoint->setCore('my_core');
        $event = new PreExecuteRequestEvent($requestOutput, $endpoint);
        $this->plugin->preExecuteRequest($event);

        $this->assertSame(1 + TimeoutAwareInterface::FAST_TIMEOUT, $observer->newTimeout);
    }

    public function testUnrelatedHttpExceptionIsRethrown()
    {
        $requestOutput = $this->client->createRequest($this->query);
        $endpoint = $this->client->getEndpoint();
        $event = new PreExecuteRequestEvent($requestOutput, $endpoint);

        // thrown by AdapterHelper::buildUri() because we didn't set a core or collection on the endpoint
        $this->expectException(HttpException::class);
        $this->expectExceptionCode(404);
        $this->plugin->preExecuteRequest($event);
    }

    public function testUnrelatedExceptionIsRethrown()
    {
        $requestOutput = $this->client->createRequest($this->query);
        $endpoint = $this->client->getEndpoint();
        $event = new class($requestOutput, $endpoint) extends PreExecuteRequestEvent {
            public function getEndpoint(): Endpoint
            {
                throw new \RuntimeException('', 42);
            }
        };

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(42);
        $this->plugin->preExecuteRequest($event);
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

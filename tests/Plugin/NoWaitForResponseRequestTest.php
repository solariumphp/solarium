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

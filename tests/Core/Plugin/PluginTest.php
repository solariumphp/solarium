<?php

namespace Solarium\Tests\Core\Plugin;

use PHPUnit\Framework\TestCase;
use Solarium\Client;
use Solarium\Core\Event\Events;
use Solarium\Core\Plugin\AbstractPlugin;
use Solarium\Tests\Integration\TestClientFactory;

class PluginTest extends TestCase
{
    /**
     * @var MyPlugin
     */
    protected $plugin;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $options;

    public function setUp(): void
    {
        $this->client = TestClientFactory::createWithCurlAdapter();
        $this->options = ['option1' => 1];
        $this->plugin = new MyPlugin();
        $this->plugin->initPlugin($this->client, $this->options);
    }

    public function testConstructor()
    {
        $this->assertSame($this->client, $this->plugin->getClient());
        $this->assertSame($this->options, $this->plugin->getOptions());
    }

    public function testInitPluginType()
    {
        $this->assertFalse($this->plugin->eventReceived);
        $this->client->createSelect();
        $this->assertTrue($this->plugin->eventReceived);
    }
}

class MyPlugin extends AbstractPlugin
{
    public $eventReceived = false;

    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Event hook to customize the request object.
     *
     * @param object $event
     */
    public function preCreateQuery($event): void
    {
        $this->eventReceived = true;
    }

    /**
     * Plugin init function.
     *
     * Register event listeners
     */
    protected function initPluginType()
    {
        $dispatcher = $this->client->getEventDispatcher();
        if (is_subclass_of($dispatcher, '\Symfony\Component\EventDispatcher\EventDispatcherInterface')) {
            $dispatcher->addListener(Events::PRE_CREATE_QUERY, [$this, 'preCreateQuery']);
        }
    }
}

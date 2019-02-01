<?php

namespace Solarium\Tests\Core\Plugin;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Plugin\AbstractPlugin;

class PluginTest extends TestCase
{
    /**
     * @var AbstractPlugin
     */
    protected $plugin;

    protected $client;

    protected $options;

    public function setUp()
    {
        $this->client = 'dummy';
        $this->options = ['option1' => 1];
        $this->plugin = new MyPlugin();
        $this->plugin->initPlugin($this->client, $this->options);
    }

    public function testConstructor()
    {
        $this->assertSame($this->client, $this->plugin->getClient());
        $this->assertSame($this->options, $this->plugin->getOptions());
    }
}

class MyPlugin extends AbstractPlugin
{
    public function getClient()
    {
        return $this->client;
    }
}

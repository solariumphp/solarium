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
        $this->options = array('option1' => 1);
        $this->plugin = new MyPlugin();
        $this->plugin->initPlugin($this->client, $this->options);
    }

    public function testConstructor()
    {
        $this->assertSame($this->client, $this->plugin->getClient());
        $this->assertSame($this->options, $this->plugin->getOptions());
    }

    public function testEventHooksEmpty()
    {
        $this->markTestSkipped('This test is currently skipped for unknown reasons.');

        $this->assertSame(null, $this->plugin->preCreateRequest(null));
        $this->assertSame(null, $this->plugin->postCreateRequest(null, null));
        $this->assertSame(null, $this->plugin->preExecuteRequest(null));
        $this->assertSame(null, $this->plugin->postExecuteRequest(null, null));
        $this->assertSame(null, $this->plugin->preExecute(null));
        $this->assertSame(null, $this->plugin->postExecute(null, null));
        $this->assertSame(null, $this->plugin->preCreateResult(null, null));
        $this->assertSame(null, $this->plugin->postCreateResult(null, null, null));
        $this->assertSame(null, $this->plugin->preCreateQuery(null, null));
        $this->assertSame(null, $this->plugin->postCreateQuery(null, null, null));
    }
}

class MyPlugin extends AbstractPlugin
{
    public function getClient()
    {
        return $this->client;
    }
}

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

        $this->assertNull($this->plugin->preCreateRequest(null));
        $this->assertNull($this->plugin->postCreateRequest(null, null));
        $this->assertNull($this->plugin->preExecuteRequest(null));
        $this->assertNull($this->plugin->postExecuteRequest(null, null));
        $this->assertNull($this->plugin->preExecute(null));
        $this->assertNull($this->plugin->postExecute(null, null));
        $this->assertNull($this->plugin->preCreateResult(null, null));
        $this->assertNull($this->plugin->postCreateResult(null, null, null));
        $this->assertNull($this->plugin->preCreateQuery(null, null));
        $this->assertNull($this->plugin->postCreateQuery(null, null, null));
    }
}

class MyPlugin extends AbstractPlugin
{
    public function getClient()
    {
        return $this->client;
    }
}

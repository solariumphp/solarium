<?php

namespace Solarium\Tests\Plugin\BufferedAdd;

use Solarium\Plugin\BufferedAdd\BufferedAdd;
use Solarium\Plugin\BufferedAdd\Event\AddDocument;
use Solarium\QueryType\Update\Query\Document;
use Solarium\Tests\Integration\TestClientFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BufferedAddTest extends BufferedAddLiteTest
{
    /**
     * @var string
     */
    protected $pluginClass = BufferedAdd::class;

    /**
     * @var BufferedAdd
     */
    protected $plugin;

    public function testInitPlugin()
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $plugin = $client->getPlugin('bufferedadd');

        $this->assertInstanceOf(BufferedAdd::class, $plugin);
    }

    public function testAddDocumentEventIsTriggered()
    {
        $data = ['id' => '123', 'name' => 'test'];
        $doc = new Document($data);

        $expectedEvent = new AddDocument($doc);

        $mockEventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $mockEventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo($expectedEvent));

        $mockClient = $this->getClient($mockEventDispatcher);
        $plugin = new BufferedAdd();
        $plugin->initPlugin($mockClient, []);
        $plugin->addDocument($doc);
    }
}

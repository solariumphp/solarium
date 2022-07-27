<?php

namespace Solarium\Tests\Plugin\BufferedDelete;

use Solarium\Plugin\BufferedDelete\BufferedDelete;
use Solarium\Plugin\BufferedDelete\Delete\Id as DeleteById;
use Solarium\Plugin\BufferedDelete\Delete\Query as DeleteQuery;
use Solarium\Plugin\BufferedDelete\Event\AddDeleteById;
use Solarium\Plugin\BufferedDelete\Event\AddDeleteQuery;
use Solarium\Tests\Integration\TestClientFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BufferedDeleteTest extends BufferedDeleteLiteTest
{
    /**
     * @var string
     */
    protected $pluginClass = BufferedDelete::class;

    /**
     * @var BufferedDelete
     */
    protected $plugin;

    public function testInitPlugin()
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $plugin = $client->getPlugin('buffereddelete');

        $this->assertInstanceOf(BufferedDelete::class, $plugin);
    }

    public function testAddDeleteByIdEventIsTriggered()
    {
        $expectedEvent = new AddDeleteById(new DeleteById(123));

        $mockEventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $mockEventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo($expectedEvent));

        $mockClient = $this->getClient($mockEventDispatcher);
        $plugin = new BufferedDelete();
        $plugin->initPlugin($mockClient, []);
        $plugin->addDeleteById(123);
    }

    public function testAddDeleteQueryEventIsTriggered()
    {
        $expectedEvent = new AddDeleteQuery(new DeleteQuery('cat:abc'));

        $mockEventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $mockEventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo($expectedEvent));

        $mockClient = $this->getClient($mockEventDispatcher);
        $plugin = new BufferedDelete();
        $plugin->initPlugin($mockClient, []);
        $plugin->addDeleteQuery('cat:abc');
    }
}

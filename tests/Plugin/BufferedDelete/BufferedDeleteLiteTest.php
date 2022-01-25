<?php

namespace Solarium\Tests\Plugin\BufferedDelete;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\ClientInterface;
use Solarium\Core\Client\Endpoint;
use Solarium\Exception\RuntimeException;
use Solarium\Plugin\BufferedDelete\AbstractDelete;
use Solarium\Plugin\BufferedDelete\BufferedDeleteLite;
use Solarium\Plugin\BufferedDelete\Delete\Id as DeleteById;
use Solarium\Plugin\BufferedDelete\Delete\Query as DeleteQuery;
use Solarium\QueryType\Update\Query\Command\Delete as DeleteCommand;
use Solarium\QueryType\Update\Query\Query;
use Solarium\QueryType\Update\Result;
use Solarium\Tests\Integration\TestClientFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BufferedDeleteLiteTest extends TestCase
{
    /**
     * @var BufferedDeleteLite
     */
    protected $plugin;

    public function setUp(): void
    {
        $this->plugin = new BufferedDeleteLite();
        $this->plugin->initPlugin(TestClientFactory::createWithCurlAdapter(), []);
    }

    public function testSetAndGetBufferSize()
    {
        $this->plugin->setBufferSize(500);
        $this->assertSame(500, $this->plugin->getBufferSize());
    }

    public function testAddDeleteById()
    {
        $expected = [
            new DeleteById(123),
            new DeleteById('abc'),
        ];

        $this->plugin->addDeleteById(123);
        $this->plugin->addDeleteById('abc');

        $this->assertEquals($expected, $this->plugin->getDeletes());
    }

    public function testAddDeleteByIds()
    {
        $expected = [
            new DeleteById('abc'),
            new DeleteById(123),
        ];

        $this->plugin->addDeleteByIds(['abc', 123]);

        $this->assertEquals($expected, $this->plugin->getDeletes());
    }

    public function testAddDeleteByIdAutoFlush()
    {
        $updateQuery = $this->createMock(Query::class);
        $updateQuery->expects($this->exactly(2))
            ->method('add')
            ->withConsecutive(
                [null, (new DeleteCommand())->addId(123)],
                [null, (new DeleteCommand())->addId('abc')],
            );

        $mockResult = $this->createMock(Result::class);

        $client = $this->getClient();

        $client->expects($this->exactly(3))
            ->method('createUpdate')
            ->willReturn($updateQuery);
        $client->expects($this->exactly(2))
            ->method('update')
            ->willReturn($mockResult);

        $pluginClass = \get_class($this->plugin);
        $plugin = new $pluginClass();
        $plugin->initPlugin($client, []);
        $plugin->setBufferSize(1);
        $plugin->addDeleteByIds([123, 'abc']);
    }

    public function testAddDeleteQuery()
    {
        $expected = [
            new DeleteQuery('cat:abc'),
        ];

        $this->plugin->addDeleteQuery('cat:abc');

        $this->assertEquals($expected, $this->plugin->getDeletes());
    }

    public function testAddDeleteQueries()
    {
        $expected = [
            new DeleteQuery('cat:abc'),
            new DeleteQuery('cat:def'),
        ];

        $this->plugin->addDeleteQueries(['cat:abc', 'cat:def']);

        $this->assertEquals($expected, $this->plugin->getDeletes());
    }

    public function testAddDeleteQueryAutoFlush()
    {
        $updateQuery = $this->createMock(Query::class);
        $updateQuery->expects($this->exactly(2))
            ->method('add')
            ->withConsecutive(
                [null, (new DeleteCommand())->addQuery('cat:abc')],
                [null, (new DeleteCommand())->addQuery('cat:def')],
            );

        $mockResult = $this->createMock(Result::class);

        $client = $this->getClient();

        $client->expects($this->exactly(3))
            ->method('createUpdate')
            ->willReturn($updateQuery);
        $client->expects($this->exactly(2))
            ->method('update')
            ->willReturn($mockResult);

        $pluginClass = \get_class($this->plugin);
        $plugin = new $pluginClass();
        $plugin->initPlugin($client, []);
        $plugin->setBufferSize(1);
        $plugin->addDeleteQueries(['cat:abc', 'cat:def']);
    }

    public function testGetBuffer()
    {
        $expected = [
            new DeleteById(123),
            new DeleteQuery('cat:abc'),
        ];

        $this->plugin->addDeleteById(123);
        $this->plugin->addDeleteQuery('cat:abc');

        $this->assertEquals($expected, $this->plugin->getBuffer());
    }

    public function testClear()
    {
        $this->plugin->addDeleteById(123);
        $this->plugin->clear();

        $this->assertEquals([], $this->plugin->getDeletes());
    }

    public function testFlushEmptyBuffer()
    {
        $this->assertFalse($this->plugin->flush());
    }

    public function testFlush()
    {
        $mockUpdate = $this->createMock(Query::class);
        $mockUpdate->expects($this->once())
            ->method('add')
            ->with(
                $this->equalTo(null),
                $this->equalTo((new DeleteCommand())->addId('abc')->addQuery('cat:def')),
            );

        $mockResult = $this->createMock(Result::class);

        $mockClient = $this->getClient();
        $mockClient->expects($this->exactly(2))->method('createUpdate')->willReturn($mockUpdate);
        $mockClient->expects($this->once())->method('update')->willReturn($mockResult);

        $pluginClass = \get_class($this->plugin);
        $plugin = new $pluginClass();
        $plugin->initPlugin($mockClient, []);
        $plugin->addDeleteById('abc');
        $plugin->addDeleteQuery('cat:def');

        $this->assertSame($mockResult, $plugin->flush());
    }

    public function testFlushUnknownType()
    {
        $plugin = new BufferedDeleteDummy();
        $plugin->initPlugin(TestClientFactory::createWithCurlAdapter(), []);
        $plugin->addUnknownDeleteType();

        $this->expectException(RuntimeException::class);
        $this->expectErrorMessage('Unsupported delete type in buffer');
        $plugin->flush();
    }

    public function testCommit()
    {
        $mockUpdate = $this->createMock(Query::class);
        $mockUpdate->expects($this->once())
            ->method('add')
            ->with(
                $this->equalTo(null),
                $this->equalTo((new DeleteCommand())->addId('abc')->addQuery('cat:def')),
            );
        $mockUpdate->expects($this->once())
            ->method('addCommit')
            ->with($this->equalTo(false), $this->equalTo(true), $this->equalTo(false));

        $mockResult = $this->createMock(Result::class);

        $mockClient = $this->getClient();
        $mockClient->expects($this->exactly(2))->method('createUpdate')->willReturn($mockUpdate);
        $mockClient->expects($this->once())->method('update')->willReturn($mockResult);

        $pluginClass = \get_class($this->plugin);
        $plugin = new $pluginClass();
        $plugin->initPlugin($mockClient, []);
        $plugin->addDeleteById('abc');
        $plugin->addDeleteQuery('cat:def');

        $this->assertSame($mockResult, $plugin->commit(false, true, false));
    }

    public function testSetAndGetEndpoint()
    {
        $endpoint = new Endpoint();
        $endpoint->setKey('master');
        $this->assertSame($this->plugin, $this->plugin->setEndpoint($endpoint));
        $this->assertSame($endpoint, $this->plugin->getEndPoint());
    }

    /**
     * @param EventDispatcherInterface|null $dispatcher
     *
     * @return Client|MockObject
     */
    protected function getClient(EventDispatcherInterface $dispatcher = null): ClientInterface
    {
        if (!$dispatcher) {
            $dispatcher = $this->createMock(EventDispatcherInterface::class);
            $dispatcher->expects($this->any())
                ->method('dispatch');
        }

        /** @var Client|MockObject $client */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->any())
            ->method('getEventDispatcher')
            ->willReturn($dispatcher);

        return $client;
    }
}

class BufferedDeleteDummy extends BufferedDeleteLite
{
    public function addUnknownDeleteType()
    {
        $this->buffer[] = new DeleteDummy();
    }
}

class DeleteDummy extends AbstractDelete
{
    public function getType(): string
    {
        return 'unknown';
    }

    public function __toString(): string
    {
        return '';
    }
}

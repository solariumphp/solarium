<?php

namespace Solarium\Tests\Plugin\BufferedDelete;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\ClientInterface;
use Solarium\Core\Client\Endpoint;
use Solarium\Exception\DomainException;
use Solarium\Exception\InvalidArgumentException;
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
     * @var string
     */
    protected $pluginClass = BufferedDeleteLite::class;

    /**
     * @var BufferedDeleteLite
     */
    protected $plugin;

    public function setUp(): void
    {
        $this->plugin = new $this->pluginClass();
        $this->plugin->initPlugin(TestClientFactory::createWithCurlAdapter(), []);
    }

    public function testInitPlugin()
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $plugin = $client->getPlugin('buffereddeletelite');

        $this->assertInstanceOf(BufferedDeleteLite::class, $plugin);
    }

    public function testConstructor()
    {
        $options = [
            'buffersize' => 50,
        ];

        $pluginClass = \get_class($this->plugin);
        $plugin = new $pluginClass($options);

        $this->assertEquals(50, $plugin->getBufferSize());
    }

    /**
     * @testWith [0]
     *           [-10]
     */
    public function testConstructorWithInvalidBufferSize(int $size)
    {
        $options = [
            'buffersize' => $size,
        ];

        $pluginClass = \get_class($this->plugin);
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Buffer size must be at least 1.');
        new $pluginClass($options);
    }

    public function testConfigMode()
    {
        $options = [
            'endpoint' => new Endpoint(),
            'requestformat' => Query::REQUEST_FORMAT_XML,
            'buffersize' => 200,
        ];

        $plugin = new $this->pluginClass();
        $plugin->initPlugin(TestClientFactory::createWithCurlAdapter(), $options);

        $this->assertSame($options['endpoint'], $plugin->getEndpoint());
        $this->assertSame($options['requestformat'], $plugin->getRequestFormat());
        $this->assertSame($options['buffersize'], $plugin->getBufferSize());
    }

    public function testSetAndGetBufferSize()
    {
        $this->plugin->setBufferSize(500);
        $this->assertSame(500, $this->plugin->getBufferSize());
    }

    /**
     * @testWith [0]
     *           [-10]
     */
    public function testSetInvalidBufferSize(int $size)
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Buffer size must be at least 1.');
        $this->plugin->setBufferSize($size);
    }

    public function testInitCallsSetBufferSize()
    {
        $options = [
            'buffersize' => 50,
        ];

        $plugin = $this->getMockBuilder(\get_class($this->plugin))
            ->onlyMethods(['setBufferSize'])
            ->getMock();
        $plugin->expects($this->once())
            ->method('setBufferSize')
            ->with($this->equalTo(50));

        $plugin->initPlugin($this->getClient(), $options);
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
        $ids = [123, 'abc'];

        $updateQuery = $this->createMock(Query::class);
        $updateQuery->expects($this->exactly(2))
            ->method('add')
            ->with(
                $this->equalTo(null),
                $this->callback(function (DeleteCommand $command) use ($ids): bool {
                    static $i = 0;

                    return [$ids[$i++]] === $command->getIds();
                })
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
        $plugin->addDeleteByIds($ids);
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
        $queries = ['cat:abc', 'cat:def'];

        $updateQuery = $this->createMock(Query::class);
        $updateQuery->expects($this->exactly(2))
            ->method('add')
            ->with(
                $this->equalTo(null),
                $this->callback(function (DeleteCommand $command) use ($queries): bool {
                    static $i = 0;

                    return [$queries[$i++]] === $command->getQueries();
                })
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
        $plugin->addDeleteQueries($queries);
    }

    public function testSetBufferSizeAutoFlush()
    {
        $pluginClass = \get_class($this->plugin);
        $client = $this->getClient();

        $plugin = $this->getMockBuilder($pluginClass)
            ->onlyMethods(['flush'])
            ->getMock();
        $plugin->expects($this->never())
            ->method('flush');

        $plugin->initPlugin($client, ['buffersize' => 5]);
        $plugin->addDeleteByIds([1, 2]);
        $plugin->setBufferSize(6); // grow
        $plugin->setBufferSize(4); // shrink with room to spare

        $plugin = $this->getMockBuilder($pluginClass)
            ->onlyMethods(['flush'])
            ->getMock();
        $plugin->expects($this->once())
            ->method('flush');

        $plugin->initPlugin($client, ['buffersize' => 5]);
        $plugin->addDeleteByIds([1, 2, 3]);
        $plugin->setBufferSize(3); // shrink to exact content size

        $plugin = $this->getMockBuilder($pluginClass)
            ->onlyMethods(['flush'])
            ->getMock();
        $plugin->expects($this->once())
            ->method('flush');

        $plugin->initPlugin($client, ['buffersize' => 5]);
        $plugin->addDeleteByIds([1, 2]);
        $plugin->setBufferSize(1); // shrink below content size
    }

    /**
     * The buffer should be flushed before an exception is thrown when trying to set
     * an invalid size to allow easier recovery from this exception without data loss.
     *
     * @testWith [0]
     *           [-10]
     */
    public function testSetInvalidBufferSizeFlushesBeforeThrowing(int $size)
    {
        $pluginClass = \get_class($this->plugin);
        $client = $this->getClient();

        $plugin = $this->getMockBuilder($pluginClass)
            ->onlyMethods(['flush'])
            ->getMock();
        $plugin->expects($this->once())
            ->method('flush');

        $plugin->initPlugin($client, ['buffersize' => 5]);
        $plugin->addDeleteByIds([1, 2]);
        $this->expectException(DomainException::class);
        $plugin->setBufferSize($size);
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

    public function testClearKeepsRequestFormat()
    {
        $this->plugin->setRequestFormat(Query::REQUEST_FORMAT_XML);
        $this->plugin->clear();

        $this->assertSame(Query::REQUEST_FORMAT_XML, $this->plugin->getRequestFormat());
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
        $this->expectExceptionMessage('Unsupported delete type in buffer');
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

    public function testDefaultRequestFormat()
    {
        $this->assertSame(Query::REQUEST_FORMAT_JSON, $this->plugin->getRequestFormat());
    }

    public function testSetAndGetRequestFormat()
    {
        $this->plugin->setRequestFormat(Query::REQUEST_FORMAT_XML);
        $this->assertSame(Query::REQUEST_FORMAT_XML, $this->plugin->getRequestFormat());
    }

    public function testSetUnsupportedRequestFormat()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported request format: foobar');
        $this->plugin->setRequestFormat('foobar');
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

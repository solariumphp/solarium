<?php

namespace Solarium\Tests\Plugin\BufferedAdd;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\ClientInterface;
use Solarium\Core\Client\Endpoint;
use Solarium\Exception\DomainException;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Plugin\BufferedAdd\BufferedAddLite;
use Solarium\QueryType\Update\Query\Command\Add as AddCommand;
use Solarium\QueryType\Update\Query\Document;
use Solarium\QueryType\Update\Query\Query;
use Solarium\QueryType\Update\Result;
use Solarium\Tests\Integration\TestClientFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BufferedAddLiteTest extends TestCase
{
    protected string $pluginClass = BufferedAddLite::class;

    protected BufferedAddLite $plugin;

    public function setUp(): void
    {
        $this->plugin = new $this->pluginClass();
        $this->plugin->initPlugin(TestClientFactory::createWithCurlAdapter(), []);
    }

    public static function updateRequestFormatProvider(): array
    {
        return [
            [Query::REQUEST_FORMAT_XML],
            [Query::REQUEST_FORMAT_JSON],
            [Query::REQUEST_FORMAT_CBOR],
        ];
    }

    public function testInitPlugin(): void
    {
        $client = TestClientFactory::createWithCurlAdapter();
        $plugin = $client->getPlugin('bufferedaddlite');

        $this->assertInstanceOf(BufferedAddLite::class, $plugin);
    }

    public function testConstructor(): void
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
    public function testConstructorWithInvalidBufferSize(int $size): void
    {
        $options = [
            'buffersize' => $size,
        ];

        $pluginClass = \get_class($this->plugin);
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Buffer size must be at least 1.');
        new $pluginClass($options);
    }

    public function testConfigMode(): void
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

    public function testSetAndGetBufferSize(): void
    {
        $this->plugin->setBufferSize(500);
        $this->assertSame(500, $this->plugin->getBufferSize());
    }

    /**
     * @testWith [0]
     *           [-10]
     */
    public function testSetInvalidBufferSize(int $size): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Buffer size must be at least 1.');
        $this->plugin->setBufferSize($size);
    }

    public function testInitCallsSetBufferSize(): void
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

    public function testSetAndGetOverwrite(): void
    {
        $this->plugin->setOverwrite(true);
        $this->assertTrue($this->plugin->getOverwrite());
    }

    public function testSetAndGetCommitWithin(): void
    {
        $this->plugin->setCommitWithin(500);
        $this->assertSame(500, $this->plugin->getCommitWithin());
    }

    public function testAddDocument(): void
    {
        $doc = new Document();
        $doc->id = '123';
        $doc->name = 'test';

        $this->plugin->addDocument($doc);

        $this->assertEquals([$doc], $this->plugin->getDocuments());
    }

    public function testCreateDocument(): void
    {
        $data = ['id' => '123', 'name' => 'test'];
        $doc = new Document($data);

        $this->plugin->createDocument($data);

        $this->assertEquals([$doc], $this->plugin->getDocuments());
    }

    public function testAddDocuments(): void
    {
        $doc1 = new Document();
        $doc1->id = '123';
        $doc1->name = 'test';

        $doc2 = new Document();
        $doc2->id = '234';
        $doc2->name = 'test2';

        $docs = [$doc1, $doc2];

        $this->plugin->addDocuments($docs);

        $this->assertSame($docs, $this->plugin->getDocuments());
    }

    public function testAddDocumentAutoFlush(): void
    {
        $doc1 = new Document();
        $doc1->id = '123';
        $doc1->name = 'test';

        $doc2 = new Document();
        $doc2->id = '234';
        $doc2->name = 'test2';

        $docs = [$doc1, $doc2];

        $updateQuery = $this->createMock(Query::class);
        $updateQuery->expects($this->exactly(2))
            ->method('add')
            ->with(
                $this->equalTo(null),
                $this->callback(function (AddCommand $command) use ($docs): bool {
                    static $i = 0;

                    return [$docs[$i++]] === $command->getDocuments();
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
        $plugin->addDocuments($docs);
    }

    public function testSetBufferSizeAutoFlush(): void
    {
        $doc = new Document();
        $doc->id = '123';
        $doc->name = 'test';

        $pluginClass = \get_class($this->plugin);
        $client = $this->getClient();

        $plugin = $this->getMockBuilder($pluginClass)
            ->onlyMethods(['flush'])
            ->getMock();
        $plugin->expects($this->never())
            ->method('flush');

        $plugin->initPlugin($client, ['buffersize' => 5]);
        $plugin->addDocuments([$doc, $doc]);
        $plugin->setBufferSize(6); // grow
        $plugin->setBufferSize(4); // shrink with room to spare

        $plugin = $this->getMockBuilder($pluginClass)
            ->onlyMethods(['flush'])
            ->getMock();
        $plugin->expects($this->once())
            ->method('flush');

        $plugin->initPlugin($client, ['buffersize' => 5]);
        $plugin->addDocuments([$doc, $doc, $doc]);
        $plugin->setBufferSize(3); // shrink to exact content size

        $plugin = $this->getMockBuilder($pluginClass)
            ->onlyMethods(['flush'])
            ->getMock();
        $plugin->expects($this->once())
            ->method('flush');

        $plugin->initPlugin($client, ['buffersize' => 5]);
        $plugin->addDocuments([$doc, $doc]);
        $plugin->setBufferSize(1); // shrink below content size
    }

    /**
     * The buffer should be flushed before an exception is thrown when trying to set
     * an invalid size to allow easier recovery from this exception without data loss.
     *
     * @testWith [0]
     *           [-10]
     */
    public function testSetInvalidBufferSizeFlushesBeforeThrowing(int $size): void
    {
        $doc = new Document();
        $doc->id = '123';
        $doc->name = 'test';

        $pluginClass = \get_class($this->plugin);
        $client = $this->getClient();

        $plugin = $this->getMockBuilder($pluginClass)
            ->onlyMethods(['flush'])
            ->getMock();
        $plugin->expects($this->once())
            ->method('flush');

        $plugin->initPlugin($client, ['buffersize' => 5]);
        $plugin->addDocuments([$doc, $doc]);
        $this->expectException(DomainException::class);
        $plugin->setBufferSize($size);
    }

    public function testGetBuffer(): void
    {
        $doc = new Document();
        $doc->id = '123';
        $doc->name = 'test';

        $this->plugin->addDocument($doc);

        $this->assertEquals([$doc], $this->plugin->getBuffer());
    }

    public function testClear(): void
    {
        $doc = new Document();
        $doc->id = '123';
        $doc->name = 'test';

        $this->plugin->addDocument($doc);
        $this->plugin->clear();

        $this->assertEquals([], $this->plugin->getDocuments());
    }

    public function testClearKeepsRequestFormat(): void
    {
        $this->plugin->setRequestFormat(Query::REQUEST_FORMAT_XML);
        $this->plugin->clear();

        $this->assertSame(Query::REQUEST_FORMAT_XML, $this->plugin->getRequestFormat());
    }

    public function testFlushEmptyBuffer(): void
    {
        $this->assertFalse($this->plugin->flush());
    }

    /**
     * @dataProvider updateRequestFormatProvider
     */
    public function testFlush(string $requestFormat): void
    {
        $doc1 = new Document(['id' => '123', 'name' => 'test 1']);
        $doc2 = new Document(['id' => '456', 'name' => 'test 2']);
        $doc3 = new Document(['id' => '789', 'name' => 'test 3']);

        /** @var MockObject&Query $mockUpdate */
        $mockUpdate = $this->getMockBuilder(Query::class)
            ->onlyMethods(['add'])
            ->getMock();
        $mockUpdate->expects($this->once())
            ->method('add')
            ->with(
                $this->equalTo(null),
                $this->equalTo((new AddCommand())->setOverwrite(true)->setCommitWithin(12)->addDocuments([$doc1, $doc2, $doc3])),
            );

        $mockResult = $this->createMock(Result::class);

        $mockClient = $this->getClient();
        $mockClient->expects($this->exactly(2))->method('createUpdate')->willReturn($mockUpdate);
        $mockClient->expects($this->once())->method('update')->willReturn($mockResult);

        $pluginClass = \get_class($this->plugin);
        $plugin = new $pluginClass();
        $plugin->initPlugin($mockClient, []);
        $plugin->setRequestFormat($requestFormat);
        $plugin->addDocuments([$doc1, $doc2]);
        $plugin->addDocument($doc3);

        $this->assertSame($mockResult, $plugin->flush(true, 12));
    }

    /**
     * @dataProvider updateRequestFormatProvider
     */
    public function testCommit(string $requestFormat): void
    {
        $doc1 = new Document(['id' => '123', 'name' => 'test 1']);
        $doc2 = new Document(['id' => '456', 'name' => 'test 2']);
        $doc3 = new Document(['id' => '789', 'name' => 'test 3']);

        /** @var MockObject&Query $mockUpdate */
        $mockUpdate = $this->getMockBuilder(Query::class)
            ->onlyMethods(['add', 'addCommit'])
            ->getMock();
        $mockUpdate->expects($this->once())
            ->method('add')
            ->with(
                $this->equalTo(null),
                $this->equalTo((new AddCommand())->setOverwrite(true)->addDocuments([$doc1, $doc2, $doc3])),
            );

        if (Query::REQUEST_FORMAT_CBOR === $requestFormat) {
            $mockUpdate->expects($this->never())
                ->method('addCommit');
        } else {
            $mockUpdate->expects($this->once())
                ->method('addCommit')
                ->with($this->equalTo(false), $this->equalTo(true), $this->equalTo(false));
        }

        $mockResult = $this->createMock(Result::class);

        $mockClient = $this->getClient();
        $mockClient->expects($this->exactly(2))->method('createUpdate')->willReturn($mockUpdate);
        $mockClient->expects($this->once())->method('update')->willReturn($mockResult);

        $pluginClass = \get_class($this->plugin);
        $plugin = new $pluginClass();
        $plugin->initPlugin($mockClient, []);
        $plugin->setRequestFormat($requestFormat);
        $plugin->addDocument($doc1);
        $plugin->addDocuments([$doc2, $doc3]);

        $this->assertSame($mockResult, $plugin->commit(true, false, true, false));

        if (Query::REQUEST_FORMAT_CBOR === $requestFormat) {
            $params = $mockUpdate->getParams();

            $this->assertTrue($params['commit']);
            $this->assertFalse($params['softCommit']);
            $this->assertTrue($params['waitSearcher']);
            $this->assertFalse($params['expungeDeletes']);
        }
    }

    /**
     * @dataProvider updateRequestFormatProvider
     */
    public function testCommitWithOptionalValues(string $requestFormat): void
    {
        $doc1 = new Document(['id' => '123', 'name' => 'test 1']);
        $doc2 = new Document(['id' => '456', 'name' => 'test 2']);

        /** @var MockObject&Query $mockUpdate */
        $mockUpdate = $this->getMockBuilder(Query::class)
            ->onlyMethods(['add', 'addCommit'])
            ->getMock();
        $mockUpdate->expects($this->once())
            ->method('add')
            ->with(
                $this->equalTo(null),
                $this->equalTo((new AddCommand())->setOverwrite(true)->addDocuments([$doc1, $doc2])),
            );

        if (Query::REQUEST_FORMAT_CBOR === $requestFormat) {
            $mockUpdate->expects($this->never())
                ->method('addCommit');
        } else {
            $mockUpdate->expects($this->once())
                ->method('addCommit')
                ->with($this->equalTo(null), $this->equalTo(null), $this->equalTo(null));
        }

        $mockResult = $this->createMock(Result::class);

        $mockClient = $this->getClient();
        $mockClient->expects($this->exactly(2))->method('createUpdate')->willReturn($mockUpdate);
        $mockClient->expects($this->once())->method('update')->willReturn($mockResult);

        $pluginClass = \get_class($this->plugin);
        $plugin = new $pluginClass();
        $plugin->initPlugin($mockClient, []);
        $plugin->setRequestFormat($requestFormat);
        $plugin->addDocument($doc1);
        $plugin->addDocument($doc2);
        $plugin->setOverwrite(true);

        $this->assertSame($mockResult, $plugin->commit(null, null, null, null));

        if (Query::REQUEST_FORMAT_CBOR === $requestFormat) {
            $params = $mockUpdate->getParams();

            $this->assertTrue($params['commit']);
            // explicitly null or omitted is an implementation detail with the same end result
            $this->assertNull($params['softCommit'] ?? null);
            $this->assertNull($params['waitSearcher'] ?? null);
            $this->assertNull($params['expungeDeletes'] ?? null);
        }
    }

    public function testSetAndGetEndpoint(): void
    {
        $endpoint = new Endpoint();
        $endpoint->setKey('master');
        $this->assertSame($this->plugin, $this->plugin->setEndpoint($endpoint));
        $this->assertSame($endpoint, $this->plugin->getEndPoint());
    }

    public function testDefaultRequestFormat(): void
    {
        $this->assertSame(Query::REQUEST_FORMAT_JSON, $this->plugin->getRequestFormat());
    }

    public function testSetAndGetRequestFormat(): void
    {
        $this->plugin->setRequestFormat(Query::REQUEST_FORMAT_XML);
        $this->assertSame(Query::REQUEST_FORMAT_XML, $this->plugin->getRequestFormat());
    }

    public function testSetUnsupportedRequestFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported request format: foobar');
        $this->plugin->setRequestFormat('foobar');
    }

    /**
     * @return MockObject&Client
     */
    protected function getClient(?EventDispatcherInterface $dispatcher = null)
    {
        if (!$dispatcher) {
            $dispatcher = $this->createMock(EventDispatcherInterface::class);
            $dispatcher->expects($this->any())
                ->method('dispatch');
        }

        /** @var MockObject&Client $client */
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->any())
            ->method('getEventDispatcher')
            ->willReturn($dispatcher);

        return $client;
    }
}

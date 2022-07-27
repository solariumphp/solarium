<?php

namespace Solarium\Tests\Plugin\BufferedAdd;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\ClientInterface;
use Solarium\Core\Client\Endpoint;
use Solarium\Plugin\BufferedAdd\BufferedAddLite;
use Solarium\QueryType\Update\Query\Command\Add as AddCommand;
use Solarium\QueryType\Update\Query\Document;
use Solarium\QueryType\Update\Query\Query;
use Solarium\QueryType\Update\Result;
use Solarium\Tests\Integration\TestClientFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BufferedAddLiteTest extends TestCase
{
    /**
     * @var string
     */
    protected $pluginClass = BufferedAddLite::class;

    /**
     * @var BufferedAddLite
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
        $plugin = $client->getPlugin('bufferedaddlite');

        $this->assertInstanceOf(BufferedAddLite::class, $plugin);
    }

    public function testSetAndGetBufferSize()
    {
        $this->plugin->setBufferSize(500);
        $this->assertSame(500, $this->plugin->getBufferSize());
    }

    public function testSetAndGetOverwrite()
    {
        $this->plugin->setOverwrite(true);
        $this->assertTrue($this->plugin->getOverwrite());
    }

    public function testSetAndGetCommitWithin()
    {
        $this->plugin->setCommitWithin(500);
        $this->assertSame(500, $this->plugin->getCommitWithin());
    }

    public function testAddDocument()
    {
        $doc = new Document();
        $doc->id = '123';
        $doc->name = 'test';

        $this->plugin->addDocument($doc);

        $this->assertEquals([$doc], $this->plugin->getDocuments());
    }

    public function testCreateDocument()
    {
        $data = ['id' => '123', 'name' => 'test'];
        $doc = new Document($data);

        $this->plugin->createDocument($data);

        $this->assertEquals([$doc], $this->plugin->getDocuments());
    }

    public function testAddDocuments()
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

    public function testAddDocumentAutoFlush()
    {
        $doc1 = new Document();
        $doc1->id = '123';
        $doc1->name = 'test';

        $doc2 = new Document();
        $doc2->id = '234';
        $doc2->name = 'test2';

        $updateQuery = $this->createMock(Query::class);
        $updateQuery->expects($this->exactly(2))
            ->method('add')
            ->withConsecutive(
                [null, (new AddCommand())->addDocument($doc1)],
                [null, (new AddCommand())->addDocument($doc2)],
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
        $plugin->addDocuments([$doc1, $doc2]);
    }

    public function testGetBuffer()
    {
        $doc = new Document();
        $doc->id = '123';
        $doc->name = 'test';

        $this->plugin->addDocument($doc);

        $this->assertEquals([$doc], $this->plugin->getBuffer());
    }

    public function testClear()
    {
        $doc = new Document();
        $doc->id = '123';
        $doc->name = 'test';

        $this->plugin->addDocument($doc);
        $this->plugin->clear();

        $this->assertEquals([], $this->plugin->getDocuments());
    }

    public function testFlushEmptyBuffer()
    {
        $this->assertFalse($this->plugin->flush());
    }

    public function testFlush()
    {
        $doc1 = new Document(['id' => '123', 'name' => 'test 1']);
        $doc2 = new Document(['id' => '456', 'name' => 'test 2']);
        $doc3 = new Document(['id' => '789', 'name' => 'test 3']);

        $mockUpdate = $this->createMock(Query::class);
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
        $plugin->addDocuments([$doc1, $doc2]);
        $plugin->addDocument($doc3);

        $this->assertSame($mockResult, $plugin->flush(true, 12));
    }

    public function testCommit()
    {
        $doc1 = new Document(['id' => '123', 'name' => 'test 1']);
        $doc2 = new Document(['id' => '456', 'name' => 'test 2']);
        $doc3 = new Document(['id' => '789', 'name' => 'test 3']);

        $mockUpdate = $this->createMock(Query::class);
        $mockUpdate->expects($this->once())
            ->method('add')
            ->with(
                $this->equalTo(null),
                $this->equalTo((new AddCommand())->setOverwrite(true)->addDocuments([$doc1, $doc2, $doc3])),
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
        $plugin->addDocument($doc1);
        $plugin->addDocuments([$doc2, $doc3]);

        $this->assertSame($mockResult, $plugin->commit(true, false, true, false));
    }

    public function testCommitWithOptionalValues()
    {
        $doc1 = new Document(['id' => '123', 'name' => 'test 1']);
        $doc2 = new Document(['id' => '456', 'name' => 'test 2']);

        $mockUpdate = $this->createMock(Query::class);
        $mockUpdate->expects($this->once())
            ->method('add')
            ->with(
                $this->equalTo(null),
                $this->equalTo((new AddCommand())->setOverwrite(true)->addDocuments([$doc1, $doc2])),
            );
        $mockUpdate->expects($this->once())
            ->method('addCommit')
            ->with($this->equalTo(null), $this->equalTo(null), $this->equalTo(null));

        $mockResult = $this->createMock(Result::class);

        $mockClient = $this->getClient();
        $mockClient->expects($this->exactly(2))->method('createUpdate')->willReturn($mockUpdate);
        $mockClient->expects($this->once())->method('update')->willReturn($mockResult);

        $pluginClass = \get_class($this->plugin);
        $plugin = new $pluginClass();
        $plugin->initPlugin($mockClient, []);
        $plugin->addDocument($doc1);
        $plugin->addDocument($doc2);
        $plugin->setOverwrite(true);

        $this->assertSame($mockResult, $plugin->commit(null, null, null, null));
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

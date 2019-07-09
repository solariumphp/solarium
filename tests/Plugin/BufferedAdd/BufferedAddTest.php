<?php

namespace Solarium\Tests\Plugin\BufferedAdd;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\ClientInterface;
use Solarium\Core\Client\Endpoint;
use Solarium\Plugin\BufferedAdd\BufferedAdd;
use Solarium\Plugin\BufferedAdd\Event\AddDocument;
use Solarium\Plugin\BufferedAdd\Event\Events;
use Solarium\QueryType\Update\Query\Document;
use Solarium\QueryType\Update\Query\Query;
use Solarium\QueryType\Update\Result;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Kernel;

class BufferedAddTest extends TestCase
{
    /**
     * @var BufferedAdd
     */
    protected $plugin;

    public function setUp(): void
    {
        $this->plugin = new BufferedAdd();
        $this->plugin->initPlugin(new Client(), []);
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
        $updateQuery = $this->createMock(Query::class);
        $updateQuery->expects($this->exactly(2))
            ->method('addDocuments');

        $mockResult = $this->createMock(Result:: class);

        $client = $this->getClient();

        $client->expects($this->exactly(3))
            ->method('createUpdate')
            ->willReturn($updateQuery);
        $client->expects($this->exactly(2))
            ->method('update')
            ->willReturn($mockResult);

        $doc1 = new Document();
        $doc1->id = '123';
        $doc1->name = 'test';

        $doc2 = new Document();
        $doc2->id = '234';
        $doc2->name = 'test2';

        $docs = [$doc1, $doc2];

        $plugin = new BufferedAdd();
        $plugin->initPlugin($client, []);
        $plugin->setBufferSize(1);
        $plugin->addDocuments($docs);
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
        $data = ['id' => '123', 'name' => 'test'];
        $doc = new Document($data);

        $mockUpdate = $this->createMock(Query::class);
        $mockUpdate->expects($this->once())
            ->method('addDocuments')
            ->with($this->equalTo([$doc]), $this->equalTo(true), $this->equalTo(12));

        $mockResult = $this->createMock(Result:: class);

        $mockClient = $this->getClient();
        $mockClient->expects($this->exactly(2))->method('createUpdate')->willReturn($mockUpdate);
        $mockClient->expects($this->once())->method('update')->willReturn($mockResult);

        $plugin = new BufferedAdd();
        $plugin->initPlugin($mockClient, []);
        $plugin->addDocument($doc);

        $this->assertSame($mockResult, $plugin->flush(true, 12));
    }

    public function testCommit()
    {
        $data = ['id' => '123', 'name' => 'test'];
        $doc = new Document($data);

        $mockUpdate = $this->createMock(Query::class); //, array('addDocuments', 'addCommit'));
        $mockUpdate->expects($this->once())
            ->method('addDocuments')
            ->with($this->equalTo([$doc]), $this->equalTo(true));
        $mockUpdate->expects($this->once())
            ->method('addCommit')
            ->with($this->equalTo(false), $this->equalTo(true), $this->equalTo(false));

        $mockResult = $this->createMock(Result:: class);

        $mockClient = $this->getClient();
        $mockClient->expects($this->exactly(2))->method('createUpdate')->willReturn($mockUpdate);
        $mockClient->expects($this->once())->method('update')->willReturn($mockResult);

        $plugin = new BufferedAdd();
        $plugin->initPlugin($mockClient, []);
        $plugin->addDocument($doc);

        $this->assertSame($mockResult, $plugin->commit(true, false, true, false));
    }

    public function testAddDocumentEventIsTriggered()
    {
        $data = ['id' => '123', 'name' => 'test'];
        $doc = new Document($data);

        $expectedEvent = new AddDocument($doc);

        $mockEventDispatcher = $this->createMock(EventDispatcherInterface::class);
        if (Kernel::VERSION_ID >= 40300) {
            $mockEventDispatcher
                ->expects($this->exactly(2))
                ->method('dispatch')
                ->withConsecutive(
                    [$this->equalTo($expectedEvent)],
                    [$this->equalTo($expectedEvent), $this->equalTo(Events::ADD_DOCUMENT)]);
        } else {
            $mockEventDispatcher
                ->expects($this->once())
                ->method('dispatch')
                ->with($this->equalTo(Events::ADD_DOCUMENT), $this->equalTo($expectedEvent));
        }

        $mockClient = $this->getClient($mockEventDispatcher);
        $plugin = new BufferedAdd();
        $plugin->initPlugin($mockClient, []);
        $plugin->addDocument($doc);
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
    private function getClient(EventDispatcherInterface $dispatcher = null): ClientInterface
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

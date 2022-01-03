<?php

namespace Solarium\Tests\Plugin\BufferedAdd;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Core\Client\ClientInterface;
use Solarium\Core\Client\Endpoint;
use Solarium\Exception\RuntimeException;
use Solarium\Plugin\BufferedAdd\AbstractDelete;
use Solarium\Plugin\BufferedAdd\BufferedAdd;
use Solarium\Plugin\BufferedAdd\Delete\Id as DeleteById;
use Solarium\Plugin\BufferedAdd\Delete\Query as DeleteQuery;
use Solarium\Plugin\BufferedAdd\Event\AddDeleteById;
use Solarium\Plugin\BufferedAdd\Event\AddDeleteQuery;
use Solarium\Plugin\BufferedAdd\Event\AddDocument;
use Solarium\QueryType\Update\Query\Command\Add as AddCommand;
use Solarium\QueryType\Update\Query\Command\Delete as DeleteCommand;
use Solarium\QueryType\Update\Query\Document;
use Solarium\QueryType\Update\Query\Query;
use Solarium\QueryType\Update\Result;
use Solarium\Tests\Integration\TestClientFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BufferedAddTest extends TestCase
{
    /**
     * @var BufferedAdd
     */
    protected $plugin;

    public function setUp(): void
    {
        $this->plugin = new BufferedAdd();
        $this->plugin->initPlugin(TestClientFactory::createWithCurlAdapter(), []);
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
                [null, (new AddCommand)->addDocument($doc1)],
                [null, (new AddCommand)->addDocument($doc2)],
            );

        $mockResult = $this->createMock(Result::class);

        $client = $this->getClient();

        $client->expects($this->exactly(3))
            ->method('createUpdate')
            ->willReturn($updateQuery);
        $client->expects($this->exactly(2))
            ->method('update')
            ->willReturn($mockResult);

        $plugin = new BufferedAdd();
        $plugin->initPlugin($client, []);
        $plugin->setBufferSize(1);
        $plugin->addDocuments([$doc1, $doc2]);
    }

    public function testAddDeleteById()
    {
        $expected = [
            new DeleteById(123),
            new DeleteById('abc'),
        ];

        $this->plugin->addDeleteById(123);
        $this->plugin->addDeleteById('abc');

        $this->assertEquals($expected, $this->plugin->getDocuments());
    }

    public function testAddDeleteByIds()
    {
        $expected = [
            new DeleteById('abc'),
            new DeleteById(123),
        ];

        $this->plugin->addDeleteByIds(['abc', 123]);

        $this->assertEquals($expected, $this->plugin->getDocuments());
    }

    public function testAddDeleteByIdAutoFlush()
    {
        $updateQuery = $this->createMock(Query::class);
        $updateQuery->expects($this->exactly(2))
            ->method('add')
            ->withConsecutive(
                [null, (new DeleteCommand)->addId(123)],
                [null, (new DeleteCommand)->addId('abc')],
            );

        $mockResult = $this->createMock(Result::class);

        $client = $this->getClient();

        $client->expects($this->exactly(3))
            ->method('createUpdate')
            ->willReturn($updateQuery);
        $client->expects($this->exactly(2))
            ->method('update')
            ->willReturn($mockResult);

        $plugin = new BufferedAdd();
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

        $this->assertEquals($expected, $this->plugin->getDocuments());
    }

    public function testAddDeleteQueries()
    {
        $expected = [
            new DeleteQuery('cat:abc'),
            new DeleteQuery('cat:def'),
        ];

        $this->plugin->addDeleteQueries(['cat:abc', 'cat:def']);

        $this->assertEquals($expected, $this->plugin->getDocuments());
    }

    public function testAddMixed()
    {
        $doc = new Document();
        $doc->id = '123';
        $doc->name = 'test';

        $expected = [
            $doc,
            new DeleteById(123),
            new DeleteQuery('cat:abc'),
        ];

        $this->plugin->addDocument($doc);
        $this->plugin->addDeleteById(123);
        $this->plugin->addDeleteQuery('cat:abc');

        $this->assertEquals($expected, $this->plugin->getDocuments());
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
        $mockUpdate->expects($this->exactly(3))
            ->method('add')
            ->withConsecutive(
                [null, (new AddCommand)->setOverwrite(true)->setCommitWithin(12)->addDocuments([$doc1, $doc2])],
                [null, (new DeleteCommand)->addId('abc')->addQuery('cat:def')],
                [null, (new AddCommand)->setOverwrite(true)->setCommitWithin(12)->addDocument($doc3)],
            );

        $mockResult = $this->createMock(Result::class);

        $mockClient = $this->getClient();
        $mockClient->expects($this->exactly(2))->method('createUpdate')->willReturn($mockUpdate);
        $mockClient->expects($this->once())->method('update')->willReturn($mockResult);

        $plugin = new BufferedAdd();
        $plugin->initPlugin($mockClient, []);
        $plugin->addDocument($doc1);
        $plugin->addDocument($doc2);
        $plugin->addDeleteById('abc');
        $plugin->addDeleteQuery('cat:def');
        $plugin->addDocument($doc3);

        $this->assertSame($mockResult, $plugin->flush(true, 12));
    }

    public function testFlushUnknownType()
    {
        $plugin = new BufferedAddDummy();
        $plugin->initPlugin(TestClientFactory::createWithCurlAdapter(), []);
        $plugin->addUnknownType();

        $this->expectException(RuntimeException::class);
        $this->expectErrorMessage('Unsupported type in buffer');
        $plugin->flush();
    }

    public function testFlushUnknownDeleteType()
    {
        $plugin = new BufferedAddDummy();
        $plugin->initPlugin(TestClientFactory::createWithCurlAdapter(), []);
        $plugin->addUnknownDeleteType();

        $this->expectException(RuntimeException::class);
        $this->expectErrorMessage('Unsupported delete type in buffer');
        $plugin->flush();
    }

    public function testCommit()
    {
        $doc1 = new Document(['id' => '123', 'name' => 'test 1']);
        $doc2 = new Document(['id' => '456', 'name' => 'test 2']);
        $doc3 = new Document(['id' => '789', 'name' => 'test 3']);

        $mockUpdate = $this->createMock(Query::class);
        $mockUpdate->expects($this->exactly(3))
            ->method('add')
            ->withConsecutive(
                [null, (new AddCommand)->setOverwrite(true)->addDocuments([$doc1, $doc2])],
                [null, (new DeleteCommand)->addId('abc')->addQuery('cat:def')],
                [null, (new AddCommand)->setOverwrite(true)->addDocument($doc3)],
            );
        $mockUpdate->expects($this->once())
            ->method('addCommit')
            ->with($this->equalTo(false), $this->equalTo(true), $this->equalTo(false));

        $mockResult = $this->createMock(Result::class);

        $mockClient = $this->getClient();
        $mockClient->expects($this->exactly(2))->method('createUpdate')->willReturn($mockUpdate);
        $mockClient->expects($this->once())->method('update')->willReturn($mockResult);

        $plugin = new BufferedAdd();
        $plugin->initPlugin($mockClient, []);
        $plugin->addDocument($doc1);
        $plugin->addDocument($doc2);
        $plugin->addDeleteById('abc');
        $plugin->addDeleteQuery('cat:def');
        $plugin->addDocument($doc3);

        $this->assertSame($mockResult, $plugin->commit(true, false, true, false));
    }

    public function testCommitWithOptionalValues()
    {
        $doc1 = new Document(['id' => '123', 'name' => 'test 1']);
        $doc2 = new Document(['id' => '456', 'name' => 'test 2']);
        $doc3 = new Document(['id' => '789', 'name' => 'test 3']);

        $mockUpdate = $this->createMock(Query::class); //, array('addDocuments', 'addCommit'));
        $mockUpdate->expects($this->exactly(3))
            ->method('add')
            ->withConsecutive(
                [null, (new AddCommand)->setOverwrite(true)->addDocuments([$doc1, $doc2])],
                [null, (new DeleteCommand)->addId('abc')->addQuery('cat:def')],
                [null, (new AddCommand)->setOverwrite(true)->addDocument($doc3)],
            );
        $mockUpdate->expects($this->once())
            ->method('addCommit')
            ->with($this->equalTo(null), $this->equalTo(null), $this->equalTo(null));

        $mockResult = $this->createMock(Result::class);

        $mockClient = $this->getClient();
        $mockClient->expects($this->exactly(2))->method('createUpdate')->willReturn($mockUpdate);
        $mockClient->expects($this->once())->method('update')->willReturn($mockResult);

        $plugin = new BufferedAdd();
        $plugin->initPlugin($mockClient, []);
        $plugin->addDocument($doc1);
        $plugin->addDocument($doc2);
        $plugin->addDeleteById('abc');
        $plugin->addDeleteQuery('cat:def');
        $plugin->addDocument($doc3);
        $plugin->setOverwrite(true);

        $this->assertSame($mockResult, $plugin->commit(null, null, null, null));
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

    public function testAddDeleteByIdEventIsTriggered()
    {
        $expectedEvent = new AddDeleteById(new DeleteById(123));

        $mockEventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $mockEventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo($expectedEvent));

        $mockClient = $this->getClient($mockEventDispatcher);
        $plugin = new BufferedAdd();
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
        $plugin = new BufferedAdd();
        $plugin->initPlugin($mockClient, []);
        $plugin->addDeleteQuery('cat:abc');
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

class BufferedAddDummy extends BufferedAdd
{
    public function addUnknownType()
    {
        $unknownObj = new \stdClass();
        $unknownObj->id = 123;
        $unknownObj->name = 'test';

        $this->buffer[] = $unknownObj;
    }

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

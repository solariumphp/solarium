<?php

namespace Solarium\Tests\QueryType\Update\Query\Command;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Update\Query\Command\Add;
use Solarium\QueryType\Update\Query\Document;
use Solarium\QueryType\Update\Query\Query;

class AddTest extends TestCase
{
    /**
     * @var Add
     */
    protected $command;

    public function setUp(): void
    {
        $this->command = new Add();
    }

    public function testGetType()
    {
        $this->assertSame(
            Query::COMMAND_ADD,
            $this->command->getType()
        );
    }

    public function testAddDocument()
    {
        $doc = new Document(['id' => 1]);
        $this->command->addDocument($doc);
        $this->assertSame(
            [$doc],
            $this->command->getDocuments()
        );
    }

    public function testAddDocuments()
    {
        $doc1 = new Document(['id' => 1]);
        $doc2 = new Document(['id' => 2]);
        $this->command->addDocuments([$doc1, $doc2]);
        $this->assertSame(
            [$doc1, $doc2],
            $this->command->getDocuments()
        );
    }

    public function testAddDocumentsMultipleTimes()
    {
        $doc1 = new Document(['id' => 1]);
        $doc2 = new Document(['id' => 2]);
        $this->command->addDocuments([$doc1, $doc2]);

        $doc3 = new Document(['id' => 3]);
        $doc4 = new Document(['id' => 4]);
        $this->command->addDocuments([$doc3, $doc4]);

        $this->assertSame(
            [$doc1, $doc2, $doc3, $doc4],
            $this->command->getDocuments()
        );
    }

    public function testAddDocumentsIteration()
    {
        $doc1 = new Document(['id' => 1]);
        $doc2 = new Document(['id' => 2]);

        $it = new \ArrayIterator([$doc1, $doc2]);

        $this->command->addDocuments($it);

        $command_documents = $this->command->getDocuments();

        $this->assertSame(
            [$doc1, $doc2],
            $command_documents,
            'checking first two documents are added correctly'
        );

        $doc3 = new Document(['id' => 3]);
        $doc4 = new Document(['id' => 4]);
        $doc5 = new Document(['id' => 5]);

        $it2 = new \ArrayIterator([$doc3, $doc4, $doc5]);

        $this->command->addDocuments($it2);

        $command_documents = $this->command->getDocuments();

        $this->assertSame(
            [$doc1, $doc2, $doc3, $doc4, $doc5],
            $command_documents,
            'checking second three documents are added correctly to first two'
        );
    }

    /**
     * @depends testAddDocumentsIteration
     */
    public function testAddDocumentToIteration()
    {
        $doc1 = new Document(['id' => 1]);
        $doc2 = new Document(['id' => 2]);

        $this->command->addDocuments(new \ArrayIterator([$doc1, $doc2]));

        $doc3 = new Document(['id' => 3]);

        $this->command->addDocument($doc3);

        $command_documents = $this->command->getDocuments();

        $this->assertSame(
            [$doc1, $doc2, $doc3],
            $command_documents
        );
    }

    public function testAddDocumentsException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Documents must implement DocumentInterface.');
        $this->command->addDocuments([new \stdClass()]);
    }

    public function testSetDocuments()
    {
        $doc1 = new Document(['id' => 1]);
        $doc2 = new Document(['id' => 2]);
        $doc3 = new Document(['id' => 3]);
        $this->command->addDocument($doc1);
        $this->command->setDocuments([$doc2, $doc3]);
        $this->assertSame(
            [$doc2, $doc3],
            $this->command->getDocuments()
        );
    }

    public function testClear()
    {
        $doc1 = new Document(['id' => 1]);
        $doc2 = new Document(['id' => 2]);
        $this->command->addDocuments([$doc1, $doc2]);
        $this->assertCount(2, $this->command->getDocuments());
        $this->command->clear();
        $this->assertCount(0, $this->command->getDocuments());
    }

    public function testGetAndSetOverwrite()
    {
        $this->command->setOverwrite(false);
        $this->assertFalse(
            $this->command->getOverwrite()
        );
    }

    public function testGetAndSetCommitWithin()
    {
        $this->command->setCommitWithin(100);
        $this->assertSame(
            100,
            $this->command->getCommitWithin()
        );
    }
}

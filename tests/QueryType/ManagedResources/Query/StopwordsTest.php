<?php

namespace Solarium\Tests\QueryType\ManagedResources\Query;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Exception\InvalidArgumentException;
use Solarium\QueryType\ManagedResources\Query\Command\Config;
use Solarium\QueryType\ManagedResources\Query\Command\Delete;
use Solarium\QueryType\ManagedResources\Query\Command\Exists;
use Solarium\QueryType\ManagedResources\Query\Command\Remove;
use Solarium\QueryType\ManagedResources\Query\Command\Stopwords\Add;
use Solarium\QueryType\ManagedResources\Query\Command\Stopwords\Create;
use Solarium\QueryType\ManagedResources\Query\Stopwords;
use Solarium\QueryType\ManagedResources\Query\Stopwords\InitArgs;
use Solarium\QueryType\ManagedResources\RequestBuilder\Resource as RequestBuilder;
use Solarium\QueryType\ManagedResources\ResponseParser\Command as CommandResponseParser;
use Solarium\QueryType\ManagedResources\ResponseParser\Exists as ExistsResponseParser;
use Solarium\QueryType\ManagedResources\ResponseParser\Remove as RemoveResponseParser;
use Solarium\QueryType\ManagedResources\ResponseParser\Stopword as StopwordResponseParser;
use Solarium\QueryType\ManagedResources\ResponseParser\Stopwords as StopwordsResponseParser;

class StopwordsTest extends TestCase
{
    protected Stopwords $query;

    public function setUp(): void
    {
        $this->query = new Stopwords();
    }

    public function testQuery(): void
    {
        $this->assertEquals(Client::QUERY_MANAGED_STOPWORDS, $this->query->getType());
    }

    public function testGetRequestBuilder(): void
    {
        $this->assertInstanceOf(RequestBuilder::class, $this->query->getRequestBuilder());
    }

    public function testGetResponseParser(): void
    {
        $this->assertInstanceOf(StopwordsResponseParser::class, $this->query->getResponseParser());
    }

    public function testGetResponseParserWithTerm(): void
    {
        $this->query->setTerm('test');
        $this->assertInstanceOf(StopwordResponseParser::class, $this->query->getResponseParser());
    }

    public function testGetResponseParserWithCommand(): void
    {
        $command = $this->query->createCommand(Stopwords::COMMAND_ADD);
        $this->query->setCommand($command);
        $this->assertInstanceOf(CommandResponseParser::class, $this->query->getResponseParser());
    }

    public function testGetResponseParserWithExistsCommand(): void
    {
        $command = $this->query->createCommand(Stopwords::COMMAND_EXISTS);
        $this->query->setCommand($command);
        $this->assertInstanceOf(ExistsResponseParser::class, $this->query->getResponseParser());
    }

    public function testGetResponseParserWithRemoveCommand(): void
    {
        $command = $this->query->createCommand(Stopwords::COMMAND_REMOVE);
        $this->query->setCommand($command);
        $this->assertInstanceOf(RemoveResponseParser::class, $this->query->getResponseParser());
    }

    public function testGetResponseParserAfterRemovingCommand(): void
    {
        $command = $this->query->createCommand(Stopwords::COMMAND_ADD);
        $this->query->setCommand($command);
        $this->query->removeCommand();
        $this->assertInstanceOf(StopwordsResponseParser::class, $this->query->getResponseParser());
    }

    public function testSetAndGetName(): void
    {
        $this->query->setName('test');
        $this->assertSame('test', $this->query->getName());
    }

    public function testSetAndGetTerm(): void
    {
        $this->query->setTerm('test');
        $this->assertSame('test', $this->query->getTerm());
    }

    public function testRemoveTerm(): void
    {
        $this->query->setTerm('test');
        $this->query->removeTerm();
        $this->assertNull($this->query->getTerm());
    }

    public function testCommand(): void
    {
        $command = $this->query->createCommand(Stopwords::COMMAND_ADD);
        $this->query->setCommand($command);
        $this->assertInstanceOf(Add::class, $this->query->getCommand());
        $this->query->removeCommand();
        $this->assertNull($this->query->getCommand());
    }

    public function testAddCommand(): void
    {
        $command = $this->query->createCommand(Stopwords::COMMAND_ADD);
        $this->assertInstanceOf(Add::class, $command);
    }

    public function testConfigCommand(): void
    {
        $command = $this->query->createCommand(Stopwords::COMMAND_CONFIG);
        $this->assertInstanceOf(Config::class, $command);
    }

    public function testCreateCommand(): void
    {
        $command = $this->query->createCommand(Stopwords::COMMAND_CREATE);
        $this->assertInstanceOf(Create::class, $command);
    }

    public function testDeleteCommand(): void
    {
        $command = $this->query->createCommand(Stopwords::COMMAND_DELETE);
        $this->assertInstanceOf(Delete::class, $command);
    }

    public function testExistsCommand(): void
    {
        $command = $this->query->createCommand(Stopwords::COMMAND_EXISTS);
        $this->assertInstanceOf(Exists::class, $command);
    }

    public function testRemoveCommand(): void
    {
        $command = $this->query->createCommand(Stopwords::COMMAND_REMOVE);
        $this->assertInstanceOf(Remove::class, $command);
    }

    public function testUnknownCommand(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $command = $this->query->createCommand('unknowncommand');
    }

    public function testCreateInitArgs(): void
    {
        $initArgs = $this->query->createInitArgs();
        $this->assertInstanceOf(InitArgs::class, $initArgs);
        $this->assertEquals([], $initArgs->getInitArgs());
    }

    public function testCreateInitArgsWithArgs(): void
    {
        $initArgs = $this->query->createInitArgs(['ignoreCase' => true]);
        $this->assertInstanceOf(InitArgs::class, $initArgs);
        $this->assertEquals(['ignoreCase' => true], $initArgs->getInitArgs());
    }
}

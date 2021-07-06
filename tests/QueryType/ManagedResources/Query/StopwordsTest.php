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
    protected $query;

    public function setUp(): void
    {
        $this->query = new Stopwords();
    }

    public function testQuery()
    {
        $this->assertEquals(Client::QUERY_MANAGED_STOPWORDS, $this->query->getType());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(RequestBuilder::class, $this->query->getRequestBuilder());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(StopwordsResponseParser::class, $this->query->getResponseParser());
    }

    public function testGetResponseParserWithTerm()
    {
        $this->query->setTerm('test');
        $this->assertInstanceOf(StopwordResponseParser::class, $this->query->getResponseParser());
    }

    public function testGetResponseParserWithCommand()
    {
        $command = $this->query->createCommand(Stopwords::COMMAND_ADD);
        $this->query->setCommand($command);
        $this->assertInstanceOf(CommandResponseParser::class, $this->query->getResponseParser());
    }

    public function testGetResponseParserWithExistsCommand()
    {
        $command = $this->query->createCommand(Stopwords::COMMAND_EXISTS);
        $this->query->setCommand($command);
        $this->assertInstanceOf(ExistsResponseParser::class, $this->query->getResponseParser());
    }

    public function testGetResponseParserWithRemoveCommand()
    {
        $command = $this->query->createCommand(Stopwords::COMMAND_REMOVE);
        $this->query->setCommand($command);
        $this->assertInstanceOf(RemoveResponseParser::class, $this->query->getResponseParser());
    }

    public function testGetResponseParserAfterRemovingCommand()
    {
        $command = $this->query->createCommand(Stopwords::COMMAND_ADD);
        $this->query->setCommand($command);
        $this->query->removeCommand();
        $this->assertInstanceOf(StopwordsResponseParser::class, $this->query->getResponseParser());
    }

    public function testSetAndGetName()
    {
        $this->query->setName('test');
        $this->assertSame('test', $this->query->getName());
    }

    public function testSetAndGetTerm()
    {
        $this->query->setTerm('test');
        $this->assertSame('test', $this->query->getTerm());
    }

    public function testRemoveTerm()
    {
        $this->query->setTerm('test');
        $this->query->removeTerm();
        $this->assertNull($this->query->getTerm());
    }

    public function testCommand()
    {
        $command = $this->query->createCommand(Stopwords::COMMAND_ADD);
        $this->query->setCommand($command);
        $this->assertInstanceOf(Add::class, $this->query->getCommand());
        $this->query->removeCommand();
        $this->assertNull($this->query->getCommand());
    }

    public function testAddCommand()
    {
        $command = $this->query->createCommand(Stopwords::COMMAND_ADD);
        $this->assertInstanceOf(Add::class, $command);
    }

    public function testConfigCommand()
    {
        $command = $this->query->createCommand(Stopwords::COMMAND_CONFIG);
        $this->assertInstanceOf(Config::class, $command);
    }

    public function testCreateCommand()
    {
        $command = $this->query->createCommand(Stopwords::COMMAND_CREATE);
        $this->assertInstanceOf(Create::class, $command);
    }

    public function testDeleteCommand()
    {
        $command = $this->query->createCommand(Stopwords::COMMAND_DELETE);
        $this->assertInstanceOf(Delete::class, $command);
    }

    public function testExistsCommand()
    {
        $command = $this->query->createCommand(Stopwords::COMMAND_EXISTS);
        $this->assertInstanceOf(Exists::class, $command);
    }

    public function testRemoveCommand()
    {
        $command = $this->query->createCommand(Stopwords::COMMAND_REMOVE);
        $this->assertInstanceOf(Remove::class, $command);
    }

    public function testUnknownCommand()
    {
        $this->expectException(InvalidArgumentException::class);
        $command = $this->query->createCommand('unknowncommand');
    }

    public function testCreateInitArgs()
    {
        $initArgs = $this->query->createInitArgs();
        $this->assertInstanceOf(InitArgs::class, $initArgs);
        $this->assertEquals([], $initArgs->getInitArgs());
    }

    public function testCreateInitArgsWithArgs()
    {
        $initArgs = $this->query->createInitArgs(['ignoreCase' => true]);
        $this->assertInstanceOf(InitArgs::class, $initArgs);
        $this->assertEquals(['ignoreCase' => true], $initArgs->getInitArgs());
    }
}

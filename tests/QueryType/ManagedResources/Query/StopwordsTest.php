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
use Solarium\QueryType\ManagedResources\ResponseParser\Stopwords as ResponeParser;

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
        $this->assertInstanceOf(ResponeParser::class, $this->query->getResponseParser());
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
        $args = ['ignoreCase' => true];
        $initArgs = $this->query->createInitArgs($args);
        $this->assertInstanceOf(InitArgs::class, $initArgs);
        $this->assertEquals($args, $initArgs->getInitArgs());
    }

    public function testInitArgs()
    {
        $initArgs = new InitArgs();
        $this->assertEquals([], $initArgs->getInitArgs());
    }

    public function testInitArgsIgnoreCase()
    {
        $initArgs = new InitArgs();
        $initArgs->setIgnoreCase(true);
        $this->assertTrue($initArgs->getIgnoreCase());
    }

    public function testInitArgsSet()
    {
        $args = ['ignoreCase' => true];
        $initArgs = new InitArgs();
        $initArgs->setInitArgs($args);
        $this->assertEquals($args, $initArgs->getInitArgs());
    }
}

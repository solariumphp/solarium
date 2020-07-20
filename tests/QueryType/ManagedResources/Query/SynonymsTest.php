<?php

namespace Solarium\Tests\QueryType\ManagedResources\Query;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\UnexpectedValueException;
use Solarium\QueryType\ManagedResources\Query\Command\Config;
use Solarium\QueryType\ManagedResources\Query\Command\Delete;
use Solarium\QueryType\ManagedResources\Query\Command\Exists;
use Solarium\QueryType\ManagedResources\Query\Command\Remove;
use Solarium\QueryType\ManagedResources\Query\Command\Synonyms\Add;
use Solarium\QueryType\ManagedResources\Query\Command\Synonyms\Create;
use Solarium\QueryType\ManagedResources\Query\Synonyms;
use Solarium\QueryType\ManagedResources\Query\Synonyms\InitArgs;
use Solarium\QueryType\ManagedResources\RequestBuilder\Resource as RequestBuilder;
use Solarium\QueryType\ManagedResources\ResponseParser\Synonyms as ResponeParser;

class SynonymsTest extends TestCase
{
    protected $query;

    public function setUp(): void
    {
        $this->query = new Synonyms();
    }

    public function testQuery()
    {
        $this->assertEquals(Client::QUERY_MANAGED_SYNONYMS, $this->query->getType());
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
        $command = $this->query->createCommand(Synonyms::COMMAND_ADD);
        $this->query->setCommand($command);
        $this->assertInstanceOf(Add::class, $this->query->getCommand());
        $this->query->removeCommand();
        $this->assertNull($this->query->getCommand());
    }

    public function testAddCommand()
    {
        $command = $this->query->createCommand(Synonyms::COMMAND_ADD);
        $this->assertInstanceOf(Add::class, $command);
        $synonyms = new Synonyms\Synonyms();
        $term = 'mad';
        $synonyms->setTerm($term);
        $synonyms->setSynonyms(['angry', 'upset']);
        $command->setSynonyms($synonyms);
        $this->assertEquals('mad', $command->getSynonyms()->getTerm());
        $this->assertEquals(['angry', 'upset'], $command->getSynonyms()->getSynonyms());
    }

    public function testConfigCommand()
    {
        $command = $this->query->createCommand(Synonyms::COMMAND_CONFIG);
        $this->assertInstanceOf(Config::class, $command);
    }

    public function testCreateCommand()
    {
        $command = $this->query->createCommand(Synonyms::COMMAND_CREATE);
        $this->assertInstanceOf(Create::class, $command);
    }

    public function testDeleteCommand()
    {
        $command = $this->query->createCommand(Synonyms::COMMAND_DELETE);
        $this->assertInstanceOf(Delete::class, $command);
    }

    public function testExistsCommand()
    {
        $command = $this->query->createCommand(Synonyms::COMMAND_EXISTS);
        $this->assertInstanceOf(Exists::class, $command);
    }

    public function testRemoveCommand()
    {
        $command = $this->query->createCommand(Synonyms::COMMAND_REMOVE);
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
        $args = ['ignoreCase' => true, 'format' => InitArgs::FORMAT_SOLR];
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

    public function testInitArgsFormat()
    {
        $initArgs = new InitArgs();
        $initArgs->setFormat(InitArgs::FORMAT_SOLR);
        $this->assertSame(InitArgs::FORMAT_SOLR, $initArgs->getFormat());
    }

    public function testInitArgsUnknownFormat()
    {
        $initArgs = new InitArgs();
        $this->expectException(UnexpectedValueException::class);
        $initArgs->setFormat('unknownformat');
    }

    public function testInitArgsSet()
    {
        $args = ['ignoreCase' => true, 'format' => InitArgs::FORMAT_SOLR];
        $initArgs = new InitArgs();
        $initArgs->setInitArgs($args);
        $this->assertEquals($args, $initArgs->getInitArgs());
    }
}

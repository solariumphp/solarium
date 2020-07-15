<?php

namespace Solarium\Tests\QueryType\ManagedResources\Query;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\InvalidArgumentException;
use Solarium\Exception\UnexpectedValueException;
use Solarium\QueryType\ManagedResources\Query\Command\Config;
use Solarium\QueryType\ManagedResources\Query\Command\Delete;
use Solarium\QueryType\ManagedResources\Query\Command\Exists;
use Solarium\QueryType\ManagedResources\Query\Command\Remove;
use Solarium\QueryType\ManagedResources\Query\Synonyms;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Add;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Create;
use Solarium\QueryType\ManagedResources\Query\Synonyms\InitArgs;

class SynonymsTest extends TestCase
{
    protected $query;

    public function setUp(): void
    {
        $this->query = new Synonyms();
    }

    public function testQuery()
    {
        $this->assertEquals('synonyms', $this->query->getType());
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

    public function testInitArgs()
    {
        $config = ['ignoreCase' => true, 'format' => InitArgs::FORMAT_SOLR];
        $initArgs = new InitArgs();
        $initArgs->setInitArgs($config);
        $this->assertEquals($config, $initArgs->getInitArgs());
    }
}

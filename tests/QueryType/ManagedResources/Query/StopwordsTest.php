<?php

namespace Solarium\Tests\QueryType\ManagedResources\Query;

use Solarium\QueryType\ManagedResources\Query\Stopwords;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Add;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Config;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Create;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Delete;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Exists;
use Solarium\QueryType\ManagedResources\Query\Stopwords\InitArgs;
use PHPUnit\Framework\TestCase;

class StopwordsTest extends TestCase
{
    protected $query;

    public function setUp(): void
    {
        $this->query = new Stopwords();
    }

    public function testQuery()
    {
        $this->assertEquals('stopwords', $this->query->getType());
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

    public function testUnknownCommand()
    {
        $this->expectException(\InvalidArgumentException::class);
        $command = $this->query->createCommand('unknowncommand');
    }

    public function testInitArgsIgnoreCase()
    {
        $initArgs = new InitArgs();
        $initArgs->setIgnoreCase(true);
        $this->assertTrue($initArgs->getIgnoreCase());
    }

    public function testInitArgs()
    {
        $config = ['ignoreCase' => true];
        $initArgs = new InitArgs();
        $initArgs->setInitArgs($config);
        $this->assertEquals($config, $initArgs->getInitArgs());
    }
}

<?php

namespace Solarium\Tests\QueryType\ManagedResources\Query;

use Solarium\QueryType\ManagedResources\Query\Stopwords;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Add;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Delete;
use Solarium\QueryType\ManagedResources\Query\Stopwords\Command\Exists;
use PHPUnit\Framework\TestCase;

class StopwordsTest extends TestCase
{
    protected $query;

    public function setUp()
    {
        $this->query = new Stopwords();
    }

    public function testQuery()
    {
        $this->assertEquals('stopwords', $this->query->getType());
    }

    public function testAddCommand()
    {
        $command = $this->query->createCommand(Stopwords::COMMAND_ADD);
        $this->assertInstanceOf(Add::class, $command);
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
}

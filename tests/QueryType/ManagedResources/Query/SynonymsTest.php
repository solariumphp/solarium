<?php

namespace Solarium\Tests\QueryType\ManagedResources\Query;

use Solarium\QueryType\ManagedResources\Query\Synonyms;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Add;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Delete;
use Solarium\QueryType\ManagedResources\Query\Synonyms\Command\Exists;
use PHPUnit\Framework\TestCase;

class SynonymsTest extends TestCase
{
    protected $query;

    public function setUp()
    {
        $this->query = new Synonyms();
    }

    public function testQuery()
    {
        $this->assertEquals('synonyms', $this->query->getType());
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
}

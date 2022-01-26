<?php

namespace Solarium\Tests\QueryType\Update\Query\Command;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Update\Query\Command\Delete;
use Solarium\QueryType\Update\Query\Query;

class DeleteTest extends TestCase
{
    protected $command;

    public function setUp(): void
    {
        $this->command = new Delete();
    }

    public function testGetType()
    {
        $this->assertSame(
            Query::COMMAND_DELETE,
            $this->command->getType()
        );
    }

    public function testConfigMode()
    {
        $options = [
            'id' => 1,
            'query' => '*:*',
        ];

        $command = new Delete($options);

        $this->assertSame(
            [1],
            $command->getIds()
        );

        $this->assertSame(
            ['*:*'],
            $command->getQueries()
        );
    }

    public function testConfigModeMultiValue()
    {
        $options = [
            'id' => [1, 2],
            'query' => ['id:1', 'id:2'],
        ];

        $command = new Delete($options);

        $this->assertSame(
            [1, 2],
            $command->getIds()
        );

        $this->assertSame(
            ['id:1', 'id:2'],
            $command->getQueries()
        );
    }

    public function testAddId()
    {
        $this->command->addId(1);
        $this->assertSame(
            [1],
            $this->command->getIds()
        );
    }

    public function testAddIds()
    {
        $this->command->addId(1);
        $this->command->addIds([2, 3]);
        $this->assertSame(
            [1, 2, 3],
            $this->command->getIds()
        );
    }

    public function testAddQuery()
    {
        $this->command->addQuery('*:*');
        $this->assertSame(
            ['*:*'],
            $this->command->getQueries()
        );
    }

    public function testAddQueries()
    {
        $this->command->addQuery('*:*');
        $this->command->addQueries(['id:1', 'id:2']);
        $this->assertSame(
            ['*:*', 'id:1', 'id:2'],
            $this->command->getQueries()
        );
    }

    public function testClear()
    {
        $this->command->addIds([1, 2]);
        $this->command->addQueries(['id:1', 'id:2']);
        $this->assertCount(2, $this->command->getIds());
        $this->assertCount(2, $this->command->getQueries());
        $this->command->clear();
        $this->assertCount(0, $this->command->getIds());
        $this->assertCount(0, $this->command->getQueries());
    }
}

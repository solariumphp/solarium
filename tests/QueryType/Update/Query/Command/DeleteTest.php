<?php


namespace Solarium\Tests\QueryType\Update\Query\Command;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Update\Query\Command\Delete;
use Solarium\QueryType\Update\Query\Query;

class DeleteTest extends TestCase
{
    protected $command;

    public function setUp()
    {
        $this->command = new Delete;
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
        $options = array(
            'id' => 1,
            'query' => '*:*',
        );

        $command = new Delete($options);

        $this->assertSame(
            array(1),
            $command->getIds()
        );

        $this->assertSame(
            array('*:*'),
            $command->getQueries()
        );
    }

    public function testConfigModeMultiValue()
    {
        $options = array(
            'id' => array(1, 2),
            'query' => array('id:1', 'id:2'),
        );

        $command = new Delete($options);

        $this->assertSame(
            array(1, 2),
            $command->getIds()
        );

        $this->assertSame(
            array('id:1', 'id:2'),
            $command->getQueries()
        );
    }

    public function testAddId()
    {
        $this->command->addId(1);
        $this->assertSame(
            array(1),
            $this->command->getIds()
        );
    }

    public function testAddIds()
    {
        $this->command->addId(1);
        $this->command->addIds(array(2, 3));
        $this->assertSame(
            array(1, 2, 3),
            $this->command->getIds()
        );
    }

    public function testAddQuery()
    {
        $this->command->addQuery('*:*');
        $this->assertSame(
            array('*:*'),
            $this->command->getQueries()
        );
    }

    public function testAddQueries()
    {
        $this->command->addQuery('*:*');
        $this->command->addQueries(array('id:1', 'id:2'));
        $this->assertSame(
            array('*:*', 'id:1', 'id:2'),
            $this->command->getQueries()
        );
    }
}

<?php

namespace Solarium\Tests\QueryType\Update\Query\Command;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Update\Query\Command\Raw;
use Solarium\QueryType\Update\Query\Query;

class RawTest extends TestCase
{
    protected $command;

    public function setUp(): void
    {
        $this->command = new Raw();
    }

    public function testGetType()
    {
        $this->assertSame(
            Query::COMMAND_RAW,
            $this->command->getType()
        );
    }

    public function testConfigMode()
    {
        $options = [
            'command' => '<add><doc><field name="id">1</field></doc></add>',
        ];

        $command = new Raw($options);

        $this->assertSame(
            ['<add><doc><field name="id">1</field></doc></add>'],
            $command->getCommands()
        );
    }

    public function testConfigModeMultiValue()
    {
        $options = [
            'command' => ['<add><doc><field name="id">1</field></doc></add>', '<add><doc><field name="id">2</field></doc></add>'],
        ];

        $command = new Raw($options);

        $this->assertSame(
            ['<add><doc><field name="id">1</field></doc></add>', '<add><doc><field name="id">2</field></doc></add>'],
            $command->getCommands()
        );
    }

    public function testAddCommand()
    {
        $this->command->addCommand('<add><doc><field name="id">1</field></doc></add>');
        $this->assertSame(
            ['<add><doc><field name="id">1</field></doc></add>'],
            $this->command->getCommands()
        );
    }

    public function testAddCommands()
    {
        $this->command->addCommand('<add><doc><field name="id">1</field></doc></add>');
        $this->command->addCommands(['<add><doc><field name="id">2</field></doc></add>', '<add><doc><field name="id">3</field></doc></add>']);
        $this->assertSame(
            ['<add><doc><field name="id">1</field></doc></add>', '<add><doc><field name="id">2</field></doc></add>', '<add><doc><field name="id">3</field></doc></add>'],
            $this->command->getCommands()
        );
    }
}

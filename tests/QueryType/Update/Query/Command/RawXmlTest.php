<?php

namespace Solarium\Tests\QueryType\Update\Query\Command;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\RuntimeException;
use Solarium\QueryType\Update\Query\Command\RawXml;
use Solarium\QueryType\Update\Query\Query;

class RawXmlTest extends TestCase
{
    protected $command;

    public function setUp(): void
    {
        $this->command = new RawXml();
    }

    public function testGetType()
    {
        $this->assertSame(
            Query::COMMAND_RAWXML,
            $this->command->getType()
        );
    }

    public function testConfigMode()
    {
        $options = [
            'command' => '<add><doc><field name="id">1</field></doc></add>',
        ];

        $command = new RawXml($options);

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

        $command = new RawXml($options);

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

    public function testAddCommandFromFile()
    {
        $tmpfname = tempnam(sys_get_temp_dir(), 'xml');
        file_put_contents($tmpfname, '<add><doc><field name="id">1</field></doc></add>');

        $this->command->addCommandFromFile($tmpfname);

        $this->assertSame(
            ['<add><doc><field name="id">1</field></doc></add>'],
            $this->command->getCommands()
        );

        unlink($tmpfname);
    }

    public function testAddCommandFromFileWithUtf8Bom()
    {
        $tmpfname = tempnam(sys_get_temp_dir(), 'xml');
        file_put_contents($tmpfname, pack('CCC', 0xEF, 0xBB, 0xBF).'<add><doc><field name="id">1</field></doc></add>');

        $this->command->addCommandFromFile($tmpfname);

        $this->assertSame(
            ['<add><doc><field name="id">1</field></doc></add>'],
            $this->command->getCommands()
        );

        unlink($tmpfname);
    }

    public function testAddCommandFromFileWithXmlDeclaration()
    {
        $tmpfname = tempnam(sys_get_temp_dir(), 'xml');
        file_put_contents($tmpfname, '<?xml version="1.0" encoding="UTF-8"?><add><doc><field name="id">1</field></doc></add>');

        $this->command->addCommandFromFile($tmpfname);

        $this->assertSame(
            ['<add><doc><field name="id">1</field></doc></add>'],
            $this->command->getCommands()
        );

        unlink($tmpfname);
    }

    public function testAddCommandFromFileWithUtf8BomAndXmlDeclaration()
    {
        $tmpfname = tempnam(sys_get_temp_dir(), 'xml');
        file_put_contents($tmpfname, pack('CCC', 0xEF, 0xBB, 0xBF).'<?xml version="1.0" encoding="UTF-8"?><add><doc><field name="id">1</field></doc></add>');

        $this->command->addCommandFromFile($tmpfname);

        $this->assertSame(
            ['<add><doc><field name="id">1</field></doc></add>'],
            $this->command->getCommands()
        );

        unlink($tmpfname);
    }

    public function testAddCommandFromFileFailure()
    {
        $this->expectException(RuntimeException::class);
        $this->command->addCommandFromFile('nonexistent.xml');
    }

    public function testClear()
    {
        $this->command->addCommands(['<add><doc><field name="id">1</field></doc></add>', '<add><doc><field name="id">2</field></doc></add>']);
        $this->assertCount(2, $this->command->getCommands());
        $this->command->clear();
        $this->assertCount(0, $this->command->getCommands());
    }
}

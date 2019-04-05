<?php

namespace Solarium\Tests\QueryType\Update\Query\Command;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Update\Query\Command\Optimize;
use Solarium\QueryType\Update\Query\Query;

class OptimizeTest extends TestCase
{
    protected $command;

    public function setUp(): void
    {
        $this->command = new Optimize();
    }

    public function testConfigMode()
    {
        $options = [
            'softcommit' => true,
            'waitsearcher' => false,
            'maxsegments' => 6,
        ];

        $command = new Optimize($options);

        $this->assertTrue(
            $command->getSoftCommit()
        );

        $this->assertFalse(
            $command->getWaitSearcher()
        );

        $this->assertSame(
            6,
            $command->getMaxSegments()
        );
    }

    public function testGetType()
    {
        $this->assertSame(
            Query::COMMAND_OPTIMIZE,
            $this->command->getType()
        );
    }

    public function testGetAndSetSoftCommit()
    {
        $this->command->setSoftCommit(false);
        $this->assertFalse(
            $this->command->getSoftCommit()
        );
    }

    public function testGetAndSetWaitSearcher()
    {
        $this->command->setWaitSearcher(false);
        $this->assertFalse(
            $this->command->getWaitSearcher()
        );
    }

    public function testGetAndSetMaxSegments()
    {
        $this->command->setMaxSegments(12);
        $this->assertSame(
            12,
            $this->command->getMaxSegments()
        );
    }
}

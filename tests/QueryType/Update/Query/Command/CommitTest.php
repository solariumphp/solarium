<?php

namespace Solarium\Tests\QueryType\Update\Query\Command;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Update\Query\Command\Commit;
use Solarium\QueryType\Update\Query\Query;

class CommitTest extends TestCase
{
    protected $command;

    public function setUp(): void
    {
        $this->command = new Commit();
    }

    public function testGetType()
    {
        $this->assertSame(
            Query::COMMAND_COMMIT,
            $this->command->getType()
        );
    }

    public function testConfigMode()
    {
        $options = [
            'softcommit' => true,
            'waitsearcher' => false,
            'expungedeletes' => true,
        ];

        $command = new Commit($options);

        $this->assertTrue(
            $command->getSoftCommit()
        );

        $this->assertFalse(
            $command->getWaitSearcher()
        );

        $this->assertTrue(
            $command->getExpungeDeletes()
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

    public function testGetAndSetExpungeDeletes()
    {
        $this->command->setExpungeDeletes(true);
        $this->assertTrue(
            $this->command->getExpungeDeletes()
        );
    }
}

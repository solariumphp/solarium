<?php


namespace Solarium\Tests\QueryType\Update\Query\Command;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Update\Query\Command\Commit;
use Solarium\QueryType\Update\Query\Query;

class CommitTest extends TestCase
{
    protected $command;

    public function setUp()
    {
        $this->command = new Commit;
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
        $options = array(
            'softcommit' => true,
            'waitsearcher' => false,
            'expungedeletes' => true,
        );

        $command = new Commit($options);

        $this->assertSame(
            true,
            $command->getSoftCommit()
        );

        $this->assertSame(
            false,
            $command->getWaitSearcher()
        );

        $this->assertSame(
            true,
            $command->getExpungeDeletes()
        );
    }

    public function testGetAndSetSoftCommit()
    {
        $this->command->setSoftCommit(false);
        $this->assertSame(
            false,
            $this->command->getSoftCommit()
        );
    }

    public function testGetAndSetWaitSearcher()
    {
        $this->command->setWaitSearcher(false);
        $this->assertSame(
            false,
            $this->command->getWaitSearcher()
        );
    }

    public function testGetAndSetExpungeDeletes()
    {
        $this->command->setExpungeDeletes(true);
        $this->assertSame(
            true,
            $this->command->getExpungeDeletes()
        );
    }
}

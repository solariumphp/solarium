<?php

namespace Solarium\Tests\QueryType\Update\Query\Command;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Update\Query\Command\Rollback;
use Solarium\QueryType\Update\Query\Query;

class RollbackTest extends TestCase
{
    public function testGetType()
    {
        $command = new Rollback();
        $this->assertSame(
            Query::COMMAND_ROLLBACK,
            $command->getType()
        );
    }
}

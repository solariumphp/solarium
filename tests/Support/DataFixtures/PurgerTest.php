<?php

namespace Solarium\Tests\Support\DataFixtures;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\ClientInterface;
use Solarium\QueryType\Update\Query\Query;
use Solarium\QueryType\Update\Result;
use Solarium\Support\DataFixtures\Purger;

class PurgerTest extends TestCase
{
    public function testPurge()
    {
        $client = $this->createMock(ClientInterface::class);

        $update = $this->createMock(Query::class);
        $update->expects($this->once())
            ->method('addDeleteQuery')
            ->with('*:*');
        $update->expects($this->once())
            ->method('addCommit');

        $queryResult = $this->createMock(Result::class);
        $queryResult->expects($this->once())
            ->method('getStatus')
            ->willReturn(0);

        $client->expects($this->once())
            ->method('createUpdate')
            ->willReturn($update);

        $client->expects($this->once())
            ->method('update')
            ->with($update)
            ->willReturn($queryResult);

        $purger = new Purger($client);
        $purger->purge();
    }
}

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
            ->will($this->returnValue(0));

        $client->expects($this->once())
            ->method('createUpdate')
            ->will($this->returnValue($update));

        $client->expects($this->once())
            ->method('update')
            ->with($update)
            ->will($this->returnValue($queryResult));

        $purger = new Purger($client);
        $purger->purge();
    }
}

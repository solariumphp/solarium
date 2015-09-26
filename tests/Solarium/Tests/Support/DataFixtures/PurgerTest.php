<?php

namespace Solarium\Tests\Support\DataFixtures;

use Solarium\Support\DataFixtures\Purger;

class PurgerTest extends \PHPUnit_Framework_TestCase
{
    public function testPurge()
    {
        $solarium = $this->getMock('Solarium\Core\Client\ClientInterface');

        $update = $this->getMock('\Solarium\QueryType\Update\Query\Query');
        $update->expects($this->once())
            ->method('addDeleteQuery')
            ->with('*:*');
        $update->expects($this->once())
            ->method('addCommit');

        $queryResult = $this->getMockBuilder('\Solarium\QueryType\Update\Result')
            ->disableOriginalConstructor()
            ->getMock();
        $queryResult->expects($this->once())
            ->method('getStatus')
            ->will($this->returnValue(0));

        $solarium->expects($this->once())
            ->method('createUpdate')
            ->will($this->returnValue($update));

        $solarium->expects($this->once())
            ->method('update')
            ->with($update)
            ->will($this->returnValue($queryResult));

        $purger = new Purger($solarium);
        $purger->purge();
    }
}

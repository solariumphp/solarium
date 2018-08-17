<?php

namespace Solarium\Tests\QueryType\Server\CoreAdmin\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\RequestRecovery;

class RequestRecoveryTest extends TestCase
{
    /**
     * @var RequestRecovery
     */
    protected $action;

    public function setUp()
    {
        $this->action = new RequestRecovery();
    }

    public function testGetType()
    {
        $this->assertSame('REQUESTRECOVERY', $this->action->getType());
    }
}

<?php

namespace Solarium\Tests\QueryType\Server\CoreAdmin\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Split;
use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;

class SplitTest extends TestCase
{
    /**
     * @var Split
     */
    protected $action;

    public function setUp(): void
    {
        $this->action = new Split();
    }

    public function testGetType(): void
    {
        $this->assertSame(CoreAdminQuery::ACTION_SPLIT, $this->action->getType());
    }

    public function testSetCore(): void
    {
        $this->action->setCore('test');
        $this->assertSame('test', $this->action->getCore());
    }

    public function testSetAsync(): void
    {
        $this->action->setAsync('fooXyz');
        $this->assertSame('fooXyz', $this->action->getAsync());
    }

    public function testSetPath(): void
    {
        $this->action->setPath(['/index1', '/index2']);
        $this->assertSame(['/index1', '/index2'], $this->action->getPath());
    }

    public function testSetTargetCore(): void
    {
        $this->action->setTargetCore(['targetCoreA', 'targetCoreB']);
        $this->assertSame(['targetCoreA', 'targetCoreB'], $this->action->getTargetCore());
    }

    public function testSetRanges(): void
    {
        $this->action->setRanges('0-1f4,1f5-3e8,3e9-5dc');
        $this->assertSame('0-1f4,1f5-3e8,3e9-5dc', $this->action->getRanges());
    }

    public function testSetSplitKey(): void
    {
        $this->action->setSplitKey('A!');
        $this->assertSame('A!', $this->action->getSplitKey());
    }
}

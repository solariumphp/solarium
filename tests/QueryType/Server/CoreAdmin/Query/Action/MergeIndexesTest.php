<?php

namespace Solarium\Tests\QueryType\Server\CoreAdmin\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\MergeIndexes;
use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;

class MergeIndexesTest extends TestCase
{
    /**
     * @var MergeIndexes
     */
    protected $action;

    public function setUp(): void
    {
        $this->action = new MergeIndexes();
    }

    public function testGetType(): void
    {
        $this->assertSame(CoreAdminQuery::ACTION_MERGE_INDEXES, $this->action->getType());
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

    public function testSetIndexDir(): void
    {
        $this->action->setIndexDir(['/dirA', '/dirB']);
        $this->assertSame(['/dirA', '/dirB'], $this->action->getIndexDir());
    }

    public function testSetSrcCore(): void
    {
        $this->action->setSrcCore(['coreA', 'coreB']);
        $this->assertSame(['coreA', 'coreB'], $this->action->getSrcCore());
    }
}

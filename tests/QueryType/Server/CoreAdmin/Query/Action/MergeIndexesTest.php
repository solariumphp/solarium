<?php

namespace Solarium\Tests\QueryType\Server\CoreAdmin\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\MergeIndexes;

class MergeIndexesTest extends TestCase
{
    /**
     * @var MergeIndexes
     */
    protected $action;

    public function setUp()
    {
        $this->action = new MergeIndexes();
    }

    public function testSetSrcCore()
    {
        $this->action->setSrcCore(['coreA', 'coreB']);
        $this->assertSame(['coreA', 'coreB'], $this->action->getSrcCore());
    }

    public function testSetIndexDir()
    {
        $this->action->setIndexDir(['/dirA', '/dirB']);
        $this->assertSame(['/dirA', '/dirB'], $this->action->getIndexDir());
    }

    public function testGetType()
    {
        $this->assertSame('MERGEINDEXES', $this->action->getType());
    }
}

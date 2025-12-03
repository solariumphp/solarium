<?php

namespace Solarium\Tests\QueryType\Server\CoreAdmin\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Create;
use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;

class CreateTest extends TestCase
{
    protected Create $action;

    public function setUp(): void
    {
        $this->action = new Create();
    }

    public function testGetType(): void
    {
        $this->assertSame(CoreAdminQuery::ACTION_CREATE, $this->action->getType());
    }

    public function testSetCore(): void
    {
        $this->action->setCore('myCore');
        $this->assertSame('myCore', $this->action->getCore());
    }

    public function testSetAsync(): void
    {
        $this->action->setAsync('fooXyz');
        $this->assertSame('fooXyz', $this->action->getAsync());
    }

    public function testSetInstanceDir(): void
    {
        $this->action->setInstanceDir('myDir');
        $this->assertSame('myDir', $this->action->getInstanceDir());
    }

    public function testSetConfig(): void
    {
        $this->action->setConfig('myConfig');
        $this->assertSame('myConfig', $this->action->getConfig());
    }

    public function testSetSchema(): void
    {
        $this->action->setSchema('mySchema');
        $this->assertSame('mySchema', $this->action->getSchema());
    }

    public function testSetDataDir(): void
    {
        $this->action->setDataDir('myDataDir');
        $this->assertSame('myDataDir', $this->action->getDataDir());
    }

    public function testSetConfigSet(): void
    {
        $this->action->setConfigSet('myConfigSet');
        $this->assertSame('myConfigSet', $this->action->getConfigSet());
    }

    public function testSetCollection(): void
    {
        $this->action->setCollection('myCollection');
        $this->assertSame('myCollection', $this->action->getCollection());
    }

    public function testSetShard(): void
    {
        $this->action->setShard('myShard');
        $this->assertSame('myShard', $this->action->getShard());
    }

    public function testSetCoreProperty(): void
    {
        $this->action->setCoreProperty('foo', 'bar');
        $this->assertSame('bar', $this->action->getCoreProperty('foo'));
    }
}

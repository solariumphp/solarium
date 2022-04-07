<?php

namespace Solarium\Tests\QueryType\Server\CoreAdmin\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\CoreAdmin\Query\Action\Create;
use Solarium\QueryType\Server\CoreAdmin\Query\Query as CoreAdminQuery;

class CreateTest extends TestCase
{
    /**
     * @var Create
     */
    protected $action;

    public function setUp(): void
    {
        $this->action = new Create();
    }

    public function testGetType()
    {
        $this->assertSame(CoreAdminQuery::ACTION_CREATE, $this->action->getType());
    }

    public function testSetCore()
    {
        $this->action->setCore('myCore');
        $this->assertSame('myCore', $this->action->getCore());
    }

    public function testSetAsync()
    {
        $this->action->setAsync('fooXyz');
        $this->assertSame('fooXyz', $this->action->getAsync());
    }

    public function testSetInstanceDir()
    {
        $this->action->setInstanceDir('myDir');
        $this->assertSame('myDir', $this->action->getInstanceDir());
    }

    public function testSetConfig()
    {
        $this->action->setConfig('myConfig');
        $this->assertSame('myConfig', $this->action->getConfig());
    }

    public function testSetSchema()
    {
        $this->action->setSchema('mySchema');
        $this->assertSame('mySchema', $this->action->getSchema());
    }

    public function testSetDataDir()
    {
        $this->action->setDataDir('myDataDir');
        $this->assertSame('myDataDir', $this->action->getDataDir());
    }

    public function testSetConfigSet()
    {
        $this->action->setConfigSet('myConfigSet');
        $this->assertSame('myConfigSet', $this->action->getConfigSet());
    }

    public function testSetCollection()
    {
        $this->action->setCollection('myCollection');
        $this->assertSame('myCollection', $this->action->getCollection());
    }

    public function testSetShard()
    {
        $this->action->setShard('myShard');
        $this->assertSame('myShard', $this->action->getShard());
    }

    public function testSetCoreProperty()
    {
        $this->action->setCoreProperty('foo', 'bar');
        $this->assertSame('bar', $this->action->getCoreProperty('foo'));
    }
}

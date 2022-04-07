<?php

namespace Solarium\Tests\QueryType\Server\Configsets\Query\Action;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Server\Configsets\Query\Action\Upload;
use Solarium\QueryType\Server\Configsets\Query\Query as ConfigsetsQuery;
use Solarium\QueryType\Server\Configsets\Result\ConfigsetsResult;

class UploadTest extends TestCase
{
    /**
     * @var Upload
     */
    protected $action;

    public function setUp(): void
    {
        $this->action = new Upload();
    }

    public function testGetType()
    {
        $this->assertSame(ConfigsetsQuery::ACTION_UPLOAD, $this->action->getType());
    }

    public function testSetName()
    {
        $this->action->setName('test');
        $this->assertSame('test', $this->action->getName());
    }

    public function testSetFile()
    {
        $this->action->setFile('test');
        $this->assertSame('test', $this->action->getFile());
    }

    public function testSetFilePath()
    {
        $this->action->setFilePath('test');
        $this->assertSame('test', $this->action->getFilePath());
    }

    public function testSetBaseConfigSet()
    {
        $this->action->setOverwrite(true);
        $this->assertTrue($this->action->getOverwrite());
        $this->action->setOverwrite(false);
        $this->assertFalse($this->action->getOverwrite());
    }

    public function testSetCleanup()
    {
        $this->action->setCleanup(true);
        $this->assertTrue($this->action->getCleanup());
        $this->action->setCleanup(false);
        $this->assertFalse($this->action->getCleanup());
    }

    public function testGetResultClass()
    {
        $this->assertSame(ConfigsetsResult::class, $this->action->getResultClass());
    }
}

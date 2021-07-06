<?php

namespace Solarium\Tests\QueryType\ManagedResources\Result;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\ManagedResources\Result\Command as CommandResult;

class CommandTest extends TestCase
{
    protected $result;

    public function setUp(): void
    {
        $this->result = new CommandResultDummy();
    }

    public function testGetWasSuccessful()
    {
        $this->assertTrue($this->result->getWasSuccessful());
    }

    public function testGetStatusMessage()
    {
        $this->assertSame('OK', $this->result->getStatusMessage());
    }
}

class CommandResultDummy extends CommandResult
{
    protected $parsed = true;

    public function __construct()
    {
        $this->wasSuccessful = true;
        $this->statusMessage = 'OK';
    }
}

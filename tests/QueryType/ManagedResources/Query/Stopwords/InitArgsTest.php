<?php

namespace Solarium\Tests\QueryType\ManagedResources\Query\Stopwords;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\ManagedResources\Query\Stopwords\InitArgs;

class InitArgsTest extends TestCase
{
    /** @var InitArgs */
    protected $initArgs;

    public function setUp(): void
    {
        $this->initArgs = new InitArgs();
    }

    public function testConstructor(): void
    {
        $initArgs = new InitArgs();
        $this->assertSame([], $initArgs->getInitArgs());
    }

    public function testConstructorWithInitArgs(): void
    {
        $initArgs = new InitArgs(['ignoreCase' => true]);
        $this->assertSame(['ignoreCase' => true], $initArgs->getInitArgs());
    }

    public function testSetAndGetIgnoreCase(): void
    {
        $this->initArgs->setIgnoreCase(true);
        $this->assertTrue($this->initArgs->getIgnoreCase());
    }

    public function testSetAndGetInitArgs(): void
    {
        $this->initArgs->setInitArgs(['ignoreCase' => false]);
        $this->assertSame(['ignoreCase' => false], $this->initArgs->getInitArgs());
    }
}

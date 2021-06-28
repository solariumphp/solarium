<?php

namespace Solarium\Tests\QueryType\ManagedResources\Query\Synonyms;

use PHPUnit\Framework\TestCase;
use Solarium\Exception\UnexpectedValueException;
use Solarium\QueryType\ManagedResources\Query\Synonyms\InitArgs;

class InitArgsTest extends TestCase
{
    /** @var InitArgs */
    protected $initArgs;

    public function setUp(): void
    {
        $this->initArgs = new InitArgs();
    }

    public function testConstructor()
    {
        $initArgs = new InitArgs();
        $this->assertSame([], $initArgs->getInitArgs());
    }

    public function testConstructorWithInitArgs()
    {
        $initArgs = new InitArgs(['ignoreCase' => true, 'format' => InitArgs::FORMAT_SOLR]);
        $this->assertSame(['ignoreCase' => true, 'format' => 'solr'], $initArgs->getInitArgs());
    }

    public function testSetAndGetIgnoreCase()
    {
        $this->initArgs->setIgnoreCase(true);
        $this->assertTrue($this->initArgs->getIgnoreCase());
    }

    public function testSetAndGetFormat()
    {
        $this->initArgs->setFormat(InitArgs::FORMAT_SOLR);
        $this->assertSame('solr', $this->initArgs->getFormat());
    }

    public function testSetUnknownFormat()
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Format unknown: unknown');
        $this->initArgs->setFormat('unknown');
    }

    public function testSetAndGetInitArgs()
    {
        $this->initArgs->setInitArgs(['ignoreCase' => false, 'format' => InitArgs::FORMAT_SOLR]);
        $this->assertSame(['ignoreCase' => false, 'format' => 'solr'], $this->initArgs->getInitArgs());
    }
}

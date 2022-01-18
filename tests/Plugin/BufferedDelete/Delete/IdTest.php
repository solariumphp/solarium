<?php

namespace Solarium\Tests\Plugin\BufferedDelete\Delete;

use PHPUnit\Framework\TestCase;
use Solarium\Plugin\BufferedDelete\AbstractDelete;
use Solarium\Plugin\BufferedDelete\Delete\Id;

class IdTest extends TestCase
{
    /**
     * @var Id
     */
    protected $intId;

    /**
     * @var Id
     */
    protected $stringId;

    public function setUp(): void
    {
        $this->intId = new Id(123);
        $this->stringId = new Id('abc');
    }

    public function testGetType()
    {
        $this->assertSame(AbstractDelete::TYPE_ID, $this->intId->getType());
        $this->assertSame(AbstractDelete::TYPE_ID, $this->stringId->getType());
    }

    public function testGetId()
    {
        $this->assertSame(123, $this->intId->getId());
        $this->assertSame('abc', $this->stringId->getId());
    }

    public function testSetAndGetId()
    {
        $this->assertSame($this->intId, $this->intId->setId(456));
        $this->assertSame($this->stringId, $this->stringId->setId('def'));

        $this->assertSame(456, $this->intId->getId());
        $this->assertSame('def', $this->stringId->getId());
    }

    public function testToString()
    {
        $this->assertSame('123', (string) $this->intId);
        $this->assertSame('abc', (string) $this->stringId);
    }
}

<?php

namespace Solarium\Tests\Plugin\BufferedDelete\Delete;

use PHPUnit\Framework\TestCase;
use Solarium\Plugin\BufferedDelete\DeleteInterface;
use Solarium\Plugin\BufferedDelete\Delete\Id;

class IdTest extends TestCase
{
    protected Id $intId;

    protected Id $stringId;

    public function setUp(): void
    {
        $this->intId = new Id(123);
        $this->stringId = new Id('abc');
    }

    public function testGetType(): void
    {
        $this->assertSame(DeleteInterface::TYPE_ID, $this->intId->getType());
        $this->assertSame(DeleteInterface::TYPE_ID, $this->stringId->getType());
    }

    public function testGetId(): void
    {
        $this->assertSame(123, $this->intId->getId());
        $this->assertSame('abc', $this->stringId->getId());
    }

    public function testSetAndGetId(): void
    {
        $this->assertSame($this->intId, $this->intId->setId(456));
        $this->assertSame($this->stringId, $this->stringId->setId('def'));

        $this->assertSame(456, $this->intId->getId());
        $this->assertSame('def', $this->stringId->getId());
    }

    public function testToString(): void
    {
        $this->assertSame('123', (string) $this->intId);
        $this->assertSame('abc', (string) $this->stringId);
    }
}

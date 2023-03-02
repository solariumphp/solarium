<?php

namespace Solarium\Tests\QueryType\Luke\Result;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Luke\Result\Flag;

class FlagTest extends TestCase
{
    /**
     * @var Flag
     */
    protected $flag;

    public function setUp(): void
    {
        $this->flag = new Flag('A', 'A Flag');
    }

    public function testGetAbbreviation()
    {
        $this->assertSame('A', $this->flag->getAbbreviation());
    }

    public function testGetDisplay()
    {
        $this->assertSame('A Flag', $this->flag->getDisplay());
    }

    public function testToString()
    {
        $this->assertSame('A Flag', (string) $this->flag);
    }
}

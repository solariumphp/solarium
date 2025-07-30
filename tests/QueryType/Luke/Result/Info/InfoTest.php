<?php

namespace Solarium\Tests\QueryType\Luke\Result\Info;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Luke\Result\Info\Info;

class InfoTest extends TestCase
{
    /**
     * @var Info
     */
    protected $info;

    public function setUp(): void
    {
        $this->info = new Info();
    }

    public function testSetAndGetKey()
    {
        $key = [
            'A' => 'A Flag',
            'O' => 'Other Flag',
        ];
        $this->assertSame($this->info, $this->info->setKey($key));
        $this->assertSame($key, $this->info->getKey());
    }

    public function testSetAndGetNote()
    {
        $this->assertSame($this->info, $this->info->setNote('This is a note.'));
        $this->assertSame('This is a note.', $this->info->getNote());
    }
}

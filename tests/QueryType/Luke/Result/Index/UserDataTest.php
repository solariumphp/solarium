<?php

namespace Solarium\Tests\QueryType\Luke\Result\Index;

use PHPUnit\Framework\TestCase;
use Solarium\QueryType\Luke\Result\Index\UserData;

class UserDataTest extends TestCase
{
    /**
     * @var UserData
     */
    protected $userData;

    public function setUp(): void
    {
        $this->userData = new UserData();
    }

    public function testSetAndGetCommitCommandVer()
    {
        $this->assertSame($this->userData, $this->userData->setCommitCommandVer('123456789123456789'));
        $this->assertSame('123456789123456789', $this->userData->getCommitCommandVer());

        $this->assertSame($this->userData, $this->userData->setCommitCommandVer(null));
        $this->assertNull($this->userData->getCommitCommandVer());
    }

    public function testSetAndGetCommitTimeMSec()
    {
        $this->assertSame($this->userData, $this->userData->setCommitTimeMSec('123456789'));
        $this->assertSame('123456789', $this->userData->getCommitTimeMSec());

        $this->assertSame($this->userData, $this->userData->setCommitTimeMSec(null));
        $this->assertNull($this->userData->getCommitTimeMSec());
    }
}

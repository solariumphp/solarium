<?php

namespace Solarium\Tests\Component\DisMax;

use PHPUnit\Framework\TestCase;
use Solarium\Component\DisMax\BoostQuery;

class BoostQueryTest extends TestCase
{
    protected $boostQuery;

    public function setUp(): void
    {
        $this->boostQuery = new BoostQuery();
    }

    public function testConfigMode()
    {
        $fq = new BoostQuery(['key' => 'k1', 'query' => 'id:[10 TO 20]']);

        $this->assertSame('k1', $fq->getKey());
        $this->assertSame('id:[10 TO 20]', $fq->getQuery());
    }

    public function testSetAndGetKey()
    {
        $this->boostQuery->setKey('testkey');
        $this->assertSame('testkey', $this->boostQuery->getKey());
    }

    public function testSetAndGetQuery()
    {
        $this->boostQuery->setQuery('category:1');
        $this->assertSame('category:1', $this->boostQuery->getQuery());
    }

    public function testSetAndGetQueryWithBind()
    {
        $this->boostQuery->setQuery('id:%1%', [678]);
        $this->assertSame('id:678', $this->boostQuery->getQuery());
    }
}

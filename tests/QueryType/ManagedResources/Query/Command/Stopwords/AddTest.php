<?php

namespace Solarium\Tests\QueryType\ManagedResources\Query\Command\Stopwords;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\AbstractQuery as Query;
use Solarium\QueryType\ManagedResources\Query\Command\Stopwords\Add;

class AddTest extends TestCase
{
    protected Add $add;

    public function setUp(): void
    {
        $this->add = new Add();
    }

    public function testGetType(): void
    {
        $this->assertSame(Query::COMMAND_ADD, $this->add->getType());
    }

    public function testGetRequestMethod(): void
    {
        $this->assertSame(Request::METHOD_PUT, $this->add->getRequestMethod());
    }

    public function testSetAndGetStopwords(): void
    {
        $this->add->setStopwords(['de']);
        $this->assertSame(['de'], $this->add->getStopwords());
    }

    public function testGetRawData(): void
    {
        $this->add->setStopwords(['de']);
        $this->assertSame('["de"]', $this->add->getRawData());
    }

    public function testGetRawDataEmptyStopwords(): void
    {
        $this->add->setStopwords([]);
        $this->assertNull($this->add->getRawData());
    }

    public function testGetRawDataNoStopwords(): void
    {
        $this->assertNull($this->add->getRawData());
    }
}

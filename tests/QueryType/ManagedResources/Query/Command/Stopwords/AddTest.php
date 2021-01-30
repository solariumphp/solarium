<?php

namespace Solarium\Tests\QueryType\ManagedResources\Query\Command\Stopwords;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Request;
use Solarium\QueryType\ManagedResources\Query\AbstractQuery as Query;
use Solarium\QueryType\ManagedResources\Query\Command\Stopwords\Add;

class AddTest extends TestCase
{
    /** @var Add */
    protected $add;

    public function setUp(): void
    {
        $this->add = new Add();
    }

    public function testGetType()
    {
        $this->assertSame(Query::COMMAND_ADD, $this->add->getType());
    }

    public function testGetRequestMethod()
    {
        $this->assertSame(Request::METHOD_PUT, $this->add->getRequestMethod());
    }

    public function testSetAndGetStopwords()
    {
        $this->add->setStopwords(['de']);
        $this->assertSame(['de'], $this->add->getStopwords());
    }

    public function testGetRawData()
    {
        $this->add->setStopwords(['de']);
        $this->assertSame('["de"]', $this->add->getRawData());
    }

    public function testGetRawDataEmptyStopwords()
    {
        $this->add->setStopwords([]);
        $this->assertNull($this->add->getRawData());
    }

    public function testGetRawDataNoStopwords()
    {
        $this->assertNull($this->add->getRawData());
    }
}

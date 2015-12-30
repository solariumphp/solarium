<?php

namespace Solarium\Tests\QueryType\Schema;

use Solarium\QueryType\Schema\Result as SchemaResult;

class ResultTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->solariumResult = new SchemaDummy();
    }

    public function testIsResult()
    {
        $this->assertInstanceOf('Solarium\Core\Query\Result\ResultInterface', $this->solariumResult);
    }

    public function testGetStatus()
    {
        $this->assertEquals(1, $this->solariumResult->getStatus());
    }

    public function testGetQueryTime()
    {
        $this->assertEquals(13, $this->solariumResult->getQueryTime());
    }
}

class SchemaDummy extends SchemaResult
{
    protected $parsed = true;

    public function __construct()
    {
        $this->status = 1;
        $this->queryTime = 13;
    }
}

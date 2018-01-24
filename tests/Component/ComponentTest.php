<?php

namespace Solarium\Tests\Component;

use PHPUnit\Framework\TestCase;
use Solarium\Component\AbstractComponent;
use Solarium\QueryType\Select\Query\Query;

class ComponentTest extends TestCase
{
    public function testGetType()
    {
        $component = new TestComponent();
        $this->assertEquals('testtype', $component->getType());
    }

    public function testSetAndGetQueryInstance()
    {
        $query = new Query();
        $component = new TestComponent();
        $component->setQueryInstance($query);
        $this->assertEquals($query, $component->getQueryInstance());
    }
}

class TestComponent extends AbstractComponent
{
    public function getType()
    {
        return 'testtype';
    }

    public function getRequestBuilder()
    {
        return null;
    }

    public function getResponseParser()
    {
        return null;
    }
}

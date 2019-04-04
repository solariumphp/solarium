<?php

namespace Solarium\Tests\Component;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\Suggester;
use Solarium\QueryType\Select\Query\Query;

class SuggesterTest extends TestCase
{
    /**
     * @var Suggester
     */
    protected $suggester;

    public function setUp(): void
    {
        $this->suggester = new Suggester();
        $this->suggester->setQueryInstance(new Query());
    }

    public function testGetType()
    {
        $this->assertEquals(ComponentAwareQueryInterface::COMPONENT_SUGGESTER, $this->suggester->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(
            'Solarium\Component\ResponseParser\Suggester',
            $this->suggester->getResponseParser()
        );
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\Component\RequestBuilder\Suggester',
            $this->suggester->getRequestBuilder()
        );
    }

    public function testSetAndGetQuery()
    {
        $value = 'testquery';
        $this->suggester->setQuery($value);

        $this->assertEquals(
            $value,
            $this->suggester->getQuery()
        );
    }

    public function testSetAndGetQueryWithBind()
    {
        $this->suggester->setQuery('id:%1%', [678]);
        $this->assertEquals('id:678', $this->suggester->getQuery());
    }

    public function testSetAndGetContextFilterQuery()
    {
        $value = 'context filter query';
        $this->suggester->setContextFilterQuery($value);

        $this->assertEquals(
            $value,
            $this->suggester->getContextFilterQuery()
        );
    }

    public function testSetAndGetBuild()
    {
        $value = true;
        $this->suggester->setBuild($value);

        $this->assertEquals(
            $value,
            $this->suggester->getBuild()
        );
    }

    public function testSetAndGetReload()
    {
        $value = false;
        $this->suggester->setReload($value);

        $this->assertEquals(
            $value,
            $this->suggester->getReload()
        );
    }

    public function testSetAndGetDictionary()
    {
        $value = 'myDictionary';
        $this->suggester->setDictionary($value);

        $this->assertEquals(
            [$value],
            $this->suggester->getDictionary()
        );
    }

    public function testSetAndGetDictionaries()
    {
        $value = ['myDictionary1', 'myDictionary2'];
        $this->suggester->setDictionary($value);

        $this->assertEquals(
            $value,
            $this->suggester->getDictionary()
        );
    }

    public function testSetAndGetCount()
    {
        $value = 11;
        $this->suggester->setCount($value);

        $this->assertEquals(
            $value,
            $this->suggester->getCount()
        );
    }
}

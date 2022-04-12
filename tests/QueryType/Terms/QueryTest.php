<?php

namespace Solarium\Tests\QueryType\Terms;

use PHPUnit\Framework\TestCase;
use Solarium\Core\Client\Client;
use Solarium\QueryType\Terms\Query;

class QueryTest extends TestCase
{
    /**
     * @var Query
     */
    protected $query;

    public function setUp(): void
    {
        $this->query = new Query();
    }

    public function testGetType()
    {
        $this->assertSame(Client::QUERY_TERMS, $this->query->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf('Solarium\QueryType\Terms\ResponseParser', $this->query->getResponseParser());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf('Solarium\QueryType\Terms\RequestBuilder', $this->query->getRequestBuilder());
    }

    public function testSetAndGetFields()
    {
        $this->query->setFields('fieldA,fieldB');
        $this->assertSame(['fieldA', 'fieldB'], $this->query->getFields());
    }

    public function testSetAndGetFieldsWithArray()
    {
        $this->query->setFields(['fieldA', 'fieldB']);
        $this->assertSame(['fieldA', 'fieldB'], $this->query->getFields());
    }

    public function testGetFieldsAlwaysReturnsArray()
    {
        $this->query->setFields(null);
        $this->assertSame([], $this->query->getFields());
    }

    public function testSetAndGetLowerbound()
    {
        $this->query->setLowerbound('f');
        $this->assertSame('f', $this->query->getLowerbound());
    }

    public function testSetAndGetLowerboundInclude()
    {
        $this->query->setLowerboundInclude(true);
        $this->assertTrue($this->query->getLowerboundInclude());
    }

    public function testSetAndGetMinCount()
    {
        $this->query->setMinCount(3);
        $this->assertSame(3, $this->query->getMinCount());
    }

    public function testSetAndGetMaxCount()
    {
        $this->query->setMaxCount(25);
        $this->assertSame(25, $this->query->getMaxCount());
    }

    public function testSetAndGetPrefix()
    {
        $this->query->setPrefix('wat');
        $this->assertSame('wat', $this->query->getPrefix());
    }

    public function testSetAndGetRegex()
    {
        $this->query->setRegex('at.*');
        $this->assertSame('at.*', $this->query->getRegex());
    }

    public function testSetAndGetRegexFlags()
    {
        $this->query->setRegexFlags('case_insensitive,comments');
        $this->assertSame(['case_insensitive', 'comments'], $this->query->getRegexFlags());
    }

    public function testSetAndGetRegexFlagsWithArray()
    {
        $this->query->setRegexFlags(['case_insensitive', 'comments']);
        $this->assertSame(['case_insensitive', 'comments'], $this->query->getRegexFlags());
    }

    public function testGetRegexFlagsAlwaysReturnsArray()
    {
        $this->query->setRegexFlags(null);
        $this->assertSame([], $this->query->getRegexFlags());
    }

    public function testSetAndGetLimit()
    {
        $this->query->setLimit(15);
        $this->assertSame(15, $this->query->getLimit());
    }

    public function testSetAndGetUpperbound()
    {
        $this->query->setUpperbound('x');
        $this->assertSame('x', $this->query->getUpperbound());
    }

    public function testSetAndGetUpperboundInclude()
    {
        $this->query->setUpperboundInclude(true);
        $this->assertTrue($this->query->getUpperboundInclude());
    }

    public function testSetAndGetRaw()
    {
        $this->query->setRaw(false);
        $this->assertFalse($this->query->getRaw());
    }

    public function testSetAndGetSort()
    {
        $this->query->setSort('index');
        $this->assertSame('index', $this->query->getSort());
    }
}

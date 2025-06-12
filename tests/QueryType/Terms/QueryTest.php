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

    public function testGetType(): void
    {
        $this->assertSame(Client::QUERY_TERMS, $this->query->getType());
    }

    public function testGetResponseParser(): void
    {
        $this->assertInstanceOf('Solarium\QueryType\Terms\ResponseParser', $this->query->getResponseParser());
    }

    public function testGetRequestBuilder(): void
    {
        $this->assertInstanceOf('Solarium\QueryType\Terms\RequestBuilder', $this->query->getRequestBuilder());
    }

    public function testSetAndGetFields(): void
    {
        $this->query->setFields('fieldA,fieldB');
        $this->assertSame(['fieldA', 'fieldB'], $this->query->getFields());
    }

    public function testSetAndGetFieldsWithArray(): void
    {
        $this->query->setFields(['fieldA', 'fieldB']);
        $this->assertSame(['fieldA', 'fieldB'], $this->query->getFields());
    }

    public function testGetFieldsAlwaysReturnsArray(): void
    {
        $this->query->setFields(null);
        $this->assertSame([], $this->query->getFields());
    }

    public function testSetAndGetLowerbound(): void
    {
        $this->query->setLowerbound('f');
        $this->assertSame('f', $this->query->getLowerbound());
    }

    public function testSetAndGetLowerboundInclude(): void
    {
        $this->query->setLowerboundInclude(true);
        $this->assertTrue($this->query->getLowerboundInclude());
    }

    public function testSetAndGetMinCount(): void
    {
        $this->query->setMinCount(3);
        $this->assertSame(3, $this->query->getMinCount());
    }

    public function testSetAndGetMaxCount(): void
    {
        $this->query->setMaxCount(25);
        $this->assertSame(25, $this->query->getMaxCount());
    }

    public function testSetAndGetPrefix(): void
    {
        $this->query->setPrefix('wat');
        $this->assertSame('wat', $this->query->getPrefix());
    }

    public function testSetAndGetRegex(): void
    {
        $this->query->setRegex('at.*');
        $this->assertSame('at.*', $this->query->getRegex());
    }

    public function testSetAndGetRegexFlags(): void
    {
        $this->query->setRegexFlags('case_insensitive,comments');
        $this->assertSame(['case_insensitive', 'comments'], $this->query->getRegexFlags());
    }

    public function testSetAndGetRegexFlagsWithArray(): void
    {
        $this->query->setRegexFlags(['case_insensitive', 'comments']);
        $this->assertSame(['case_insensitive', 'comments'], $this->query->getRegexFlags());
    }

    public function testGetRegexFlagsAlwaysReturnsArray(): void
    {
        $this->query->setRegexFlags(null);
        $this->assertSame([], $this->query->getRegexFlags());
    }

    public function testSetAndGetLimit(): void
    {
        $this->query->setLimit(15);
        $this->assertSame(15, $this->query->getLimit());
    }

    public function testSetAndGetUpperbound(): void
    {
        $this->query->setUpperbound('x');
        $this->assertSame('x', $this->query->getUpperbound());
    }

    public function testSetAndGetUpperboundInclude(): void
    {
        $this->query->setUpperboundInclude(true);
        $this->assertTrue($this->query->getUpperboundInclude());
    }

    public function testSetAndGetRaw(): void
    {
        $this->query->setRaw(false);
        $this->assertFalse($this->query->getRaw());
    }

    public function testSetAndGetSort(): void
    {
        $this->query->setSort('index');
        $this->assertSame('index', $this->query->getSort());
    }
}

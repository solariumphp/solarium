<?php

namespace Solarium\Tests\Component;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\Terms;
use Solarium\QueryType\Select\Query\Query;

class TermsTest extends TestCase
{
    protected Terms $terms;

    public function setUp(): void
    {
        $this->terms = new Terms();
        $this->terms->setQueryInstance(new Query());
    }

    public function testGetType(): void
    {
        $this->assertEquals(ComponentAwareQueryInterface::COMPONENT_TERMS, $this->terms->getType());
    }

    public function testGetResponseParser(): void
    {
        $this->assertInstanceOf(
            'Solarium\Component\ResponseParser\Terms',
            $this->terms->getResponseParser()
        );
    }

    public function testGetRequestBuilder(): void
    {
        $this->assertInstanceOf(
            'Solarium\Component\RequestBuilder\Terms',
            $this->terms->getRequestBuilder()
        );
    }

    public function testSetAndGetFields(): void
    {
        $this->terms->setFields('fieldA,fieldB');
        $this->assertSame(['fieldA', 'fieldB'], $this->terms->getFields());
    }

    public function testSetAndGetFieldsWithArray(): void
    {
        $this->terms->setFields(['fieldA', 'fieldB']);
        $this->assertSame(['fieldA', 'fieldB'], $this->terms->getFields());
    }

    public function testGetFieldsAlwaysReturnsArray(): void
    {
        $this->assertSame([], $this->terms->getFields());
    }

    public function testSetAndGetLowerbound(): void
    {
        $this->terms->setLowerbound('f');
        $this->assertSame('f', $this->terms->getLowerbound());
    }

    public function testSetAndGetLowerboundInclude(): void
    {
        $this->terms->setLowerboundInclude(true);
        $this->assertTrue($this->terms->getLowerboundInclude());
    }

    public function testSetAndGetMinCount(): void
    {
        $this->terms->setMinCount(3);
        $this->assertSame(3, $this->terms->getMinCount());
    }

    public function testSetAndGetMaxCount(): void
    {
        $this->terms->setMaxCount(25);
        $this->assertSame(25, $this->terms->getMaxCount());
    }

    public function testSetAndGetPrefix(): void
    {
        $this->terms->setPrefix('wat');
        $this->assertSame('wat', $this->terms->getPrefix());
    }

    public function testSetAndGetRegex(): void
    {
        $this->terms->setRegex('at.*');
        $this->assertSame('at.*', $this->terms->getRegex());
    }

    public function testSetAndGetRegexFlags(): void
    {
        $this->terms->setRegexFlags('case_insensitive,comments');
        $this->assertSame(['case_insensitive', 'comments'], $this->terms->getRegexFlags());
    }

    public function testSetAndGetRegexFlagsWithArray(): void
    {
        $this->terms->setRegexFlags(['case_insensitive', 'comments']);
        $this->assertSame(['case_insensitive', 'comments'], $this->terms->getRegexFlags());
    }

    public function testGetRegexFlagsAlwaysReturnsArray(): void
    {
        $this->assertSame([], $this->terms->getRegexFlags());
    }

    public function testSetAndGetLimit(): void
    {
        $this->terms->setLimit(15);
        $this->assertSame(15, $this->terms->getLimit());
    }

    public function testSetAndGetUpperbound(): void
    {
        $this->terms->setUpperbound('x');
        $this->assertSame('x', $this->terms->getUpperbound());
    }

    public function testSetAndGetUpperboundInclude(): void
    {
        $this->terms->setUpperboundInclude(true);
        $this->assertTrue($this->terms->getUpperboundInclude());
    }

    public function testSetAndGetRaw(): void
    {
        $this->terms->setRaw(false);
        $this->assertFalse($this->terms->getRaw());
    }

    public function testSetAndGetSort(): void
    {
        $this->terms->setSort('index');
        $this->assertSame('index', $this->terms->getSort());
    }
}

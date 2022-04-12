<?php

namespace Solarium\Tests\Component;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\Terms;
use Solarium\QueryType\Select\Query\Query;

class TermsTest extends TestCase
{
    /**
     * @var Terms
     */
    protected $terms;

    public function setUp(): void
    {
        $this->terms = new Terms();
        $this->terms->setQueryInstance(new Query());
    }

    public function testGetType()
    {
        $this->assertEquals(ComponentAwareQueryInterface::COMPONENT_TERMS, $this->terms->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(
            'Solarium\Component\ResponseParser\Terms',
            $this->terms->getResponseParser()
        );
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\Component\RequestBuilder\Terms',
            $this->terms->getRequestBuilder()
        );
    }

    public function testSetAndGetFields()
    {
        $this->terms->setFields('fieldA,fieldB');
        $this->assertSame(['fieldA', 'fieldB'], $this->terms->getFields());
    }

    public function testSetAndGetFieldsWithArray()
    {
        $this->terms->setFields(['fieldA', 'fieldB']);
        $this->assertSame(['fieldA', 'fieldB'], $this->terms->getFields());
    }

    public function testGetFieldsAlwaysReturnsArray()
    {
        $this->terms->setFields(null);
        $this->assertSame([], $this->terms->getFields());
    }

    public function testSetAndGetLowerbound()
    {
        $this->terms->setLowerbound('f');
        $this->assertSame('f', $this->terms->getLowerbound());
    }

    public function testSetAndGetLowerboundInclude()
    {
        $this->terms->setLowerboundInclude(true);
        $this->assertTrue($this->terms->getLowerboundInclude());
    }

    public function testSetAndGetMinCount()
    {
        $this->terms->setMinCount(3);
        $this->assertSame(3, $this->terms->getMinCount());
    }

    public function testSetAndGetMaxCount()
    {
        $this->terms->setMaxCount(25);
        $this->assertSame(25, $this->terms->getMaxCount());
    }

    public function testSetAndGetPrefix()
    {
        $this->terms->setPrefix('wat');
        $this->assertSame('wat', $this->terms->getPrefix());
    }

    public function testSetAndGetRegex()
    {
        $this->terms->setRegex('at.*');
        $this->assertSame('at.*', $this->terms->getRegex());
    }

    public function testSetAndGetRegexFlags()
    {
        $this->terms->setRegexFlags('case_insensitive,comments');
        $this->assertSame(['case_insensitive', 'comments'], $this->terms->getRegexFlags());
    }

    public function testSetAndGetRegexFlagsWithArray()
    {
        $this->terms->setRegexFlags(['case_insensitive', 'comments']);
        $this->assertSame(['case_insensitive', 'comments'], $this->terms->getRegexFlags());
    }

    public function testGetRegexFlagsAlwaysReturnsArray()
    {
        $this->terms->setRegexFlags(null);
        $this->assertSame([], $this->terms->getRegexFlags());
    }

    public function testSetAndGetLimit()
    {
        $this->terms->setLimit(15);
        $this->assertSame(15, $this->terms->getLimit());
    }

    public function testSetAndGetUpperbound()
    {
        $this->terms->setUpperbound('x');
        $this->assertSame('x', $this->terms->getUpperbound());
    }

    public function testSetAndGetUpperboundInclude()
    {
        $this->terms->setUpperboundInclude(true);
        $this->assertTrue($this->terms->getUpperboundInclude());
    }

    public function testSetAndGetRaw()
    {
        $this->terms->setRaw(false);
        $this->assertFalse($this->terms->getRaw());
    }

    public function testSetAndGetSort()
    {
        $this->terms->setSort('index');
        $this->assertSame('index', $this->terms->getSort());
    }
}

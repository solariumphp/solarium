<?php

namespace Solarium\Tests\Component;

use PHPUnit\Framework\TestCase;
use Solarium\Component\ComponentAwareQueryInterface;
use Solarium\Component\Spellcheck;
use Solarium\QueryType\Select\Query\Query;

class SpellcheckTest extends TestCase
{
    /**
     * @var Spellcheck
     */
    protected $spellCheck;

    public function setUp(): void
    {
        $this->spellCheck = new Spellcheck();
        $this->spellCheck->setQueryInstance(new Query());
    }

    public function testGetType()
    {
        $this->assertEquals(ComponentAwareQueryInterface::COMPONENT_SPELLCHECK, $this->spellCheck->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(
            'Solarium\Component\ResponseParser\Spellcheck',
            $this->spellCheck->getResponseParser()
        );
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\Component\RequestBuilder\Spellcheck',
            $this->spellCheck->getRequestBuilder()
        );
    }

    public function testSetAndGetQuery()
    {
        $value = 'testquery';
        $this->spellCheck->setQuery($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getQuery()
        );
    }

    public function testSetAndGetQueryWithBind()
    {
        $this->spellCheck->setQuery('id:%1%', [678]);
        $this->assertEquals('id:678', $this->spellCheck->getQuery());
    }

    public function testSetAndGetBuild()
    {
        $value = true;
        $this->spellCheck->setBuild($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getBuild()
        );
    }

    public function testSetAndGetReload()
    {
        $value = false;
        $this->spellCheck->setReload($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getReload()
        );
    }

    public function testSetAndGetDictionary()
    {
        $value = 'myDictionary';
        $this->spellCheck->setDictionary($value);

        $this->assertEquals(
            [$value],
            $this->spellCheck->getDictionary()
        );
    }

    public function testSetAndGetCount()
    {
        $value = 11;
        $this->spellCheck->setCount($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getCount()
        );
    }

    public function testSetAndGetOnlyMorePopular()
    {
        $value = false;
        $this->spellCheck->setOnlyMorePopular($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getOnlyMorePopular()
        );
    }

    public function testSetAndGetAlternativeTermCount()
    {
        $value = 5;
        $this->spellCheck->setAlternativeTermCount($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getAlternativeTermCount()
        );
    }

    public function testSetAndGetExtendedResults()
    {
        $value = false;
        $this->spellCheck->setExtendedResults($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getExtendedResults()
        );
    }

    public function testSetAndGetCollate()
    {
        $value = false;
        $this->spellCheck->setCollate($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getCollate()
        );
    }

    public function testSetAndGetMaxCollations()
    {
        $value = 23;
        $this->spellCheck->setMaxCollations($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getMaxCollations()
        );
    }

    public function testSetAndGetMaxCollationTries()
    {
        $value = 10;
        $this->spellCheck->setMaxCollationTries($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getMaxCollationTries()
        );
    }

    public function testSetAndGetMaxCollationEvaluations()
    {
        $value = 10;
        $this->spellCheck->setMaxCollationEvaluations($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getMaxCollationEvaluations()
        );
    }

    public function testSetAndGetCollateExtendedResults()
    {
        $value = true;
        $this->spellCheck->setCollateExtendedResults($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getCollateExtendedResults()
        );
    }

    public function testSetAndGetAccuracy()
    {
        $value = .1;
        $this->spellCheck->setAccuracy($value);

        $this->assertEquals(
            $value,
            $this->spellCheck->getAccuracy()
        );
    }

    public function testSetAndGetCollateParams()
    {
        $this->assertEquals(
            $this->spellCheck,
            $this->spellCheck->setCollateParam('mm', '100%')
        );

        $params = $this->spellCheck->getCollateParams();

        $this->assertArrayHasKey('mm', $params);
        $this->assertEquals('100%', $params['mm']);
    }
}

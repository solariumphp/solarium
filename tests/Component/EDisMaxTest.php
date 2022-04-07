<?php

namespace Solarium\Tests\Component;

use PHPUnit\Framework\TestCase;
use Solarium\Component\EdisMax;
use Solarium\QueryType\Select\Query\Query;

class EDisMaxTest extends TestCase
{
    /**
     * @var EdisMax
     */
    protected $eDisMax;

    public function setUp(): void
    {
        $this->eDisMax = new EdisMax();
    }

    public function testConfigMode()
    {
        $options = [
            'queryparser' => 'edismax',
            'queryalternative' => '*:*',
            'queryfields' => 'title^2.0 description',
            'minimummatch' => '2.0',
            'phrasefields' => 'title^2.0 description^3.5',
            'phraseslop' => 2,
            'phrasebigramfields' => 'description^1.3 date^4.3 field_text2^1.3',
            'phrasebigramslop' => 3,
            'phrasetrigramfields' => 'datetime^4 field1^5 myotherfield^9',
            'phrasetrigramslop' => 5,
            'queryphraseslop' => 4,
            'tie' => 2.1,
            'boostquery' => 'cat:1^3',
            'boostfunctions' => 'funcA(arg1,arg2)^1.2 funcB(arg3,arg4)^2.2',
            'boostfunctionsmult' => 'funcC(arg5,arg6)^4.3 funcD(arg7,arg8)^3.4',
            'userfields' => 'date *_ul',
        ];

        $this->eDisMax->setOptions($options);

        $this->assertSame($options['queryparser'], $this->eDisMax->getQueryParser());
        $this->assertSame($options['queryalternative'], $this->eDisMax->getQueryAlternative());
        $this->assertSame($options['queryfields'], $this->eDisMax->getQueryFields());
        $this->assertSame($options['minimummatch'], $this->eDisMax->getMinimumMatch());
        $this->assertSame($options['phrasefields'], $this->eDisMax->getPhraseFields());
        $this->assertSame($options['phraseslop'], $this->eDisMax->getPhraseSlop());
        $this->assertSame($options['phrasebigramfields'], $this->eDisMax->getPhraseBigramFields());
        $this->assertSame($options['phrasebigramslop'], $this->eDisMax->getPhraseBigramSlop());
        $this->assertSame($options['phrasetrigramfields'], $this->eDisMax->getPhraseTrigramFields());
        $this->assertSame($options['phrasetrigramslop'], $this->eDisMax->getPhraseTrigramSlop());
        $this->assertSame($options['queryphraseslop'], $this->eDisMax->getQueryPhraseSlop());
        $this->assertSame($options['tie'], $this->eDisMax->getTie());
        $this->assertSame($options['boostquery'], $this->eDisMax->getBoostQuery());
        $this->assertSame($options['boostfunctionsmult'], $this->eDisMax->getBoostFunctionsMult());
        $this->assertSame($options['userfields'], $this->eDisMax->getUserFields());
    }

    public function testGetType()
    {
        $this->assertSame(
            Query::COMPONENT_EDISMAX,
            $this->eDisMax->getType()
        );
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\Component\RequestBuilder\EdisMax',
            $this->eDisMax->getRequestBuilder()
        );
    }

    public function testSetAndGetQueryParser()
    {
        $value = 'dummyparser';
        $this->eDisMax->setQueryParser($value);

        $this->assertSame(
            $value,
            $this->eDisMax->getQueryParser()
        );
    }

    public function testSetAndGetQueryAlternative()
    {
        $value = '*:*';
        $this->eDisMax->setQueryAlternative($value);

        $this->assertSame(
            $value,
            $this->eDisMax->getQueryAlternative()
        );
    }

    public function testSetAndGetQueryFields()
    {
        $value = 'title^2.0 description';
        $this->eDisMax->setQueryFields($value);

        $this->assertSame(
            $value,
            $this->eDisMax->getQueryFields()
        );
    }

    public function testSetAndGetMinimumMatch()
    {
        $value = '2.0';
        $this->eDisMax->setMinimumMatch($value);

        $this->assertSame(
            $value,
            $this->eDisMax->getMinimumMatch()
        );

        $value = '30%';
        $this->eDisMax->setMinimumMatch($value);

        $this->assertSame(
            $value,
            $this->eDisMax->getMinimumMatch()
        );
    }

    public function testSetAndGetPhraseFields()
    {
        $value = 'title^2.0 description^3.5';
        $this->eDisMax->setPhraseFields($value);

        $this->assertSame(
            $value,
            $this->eDisMax->getPhraseFields()
        );
    }

    public function testSetAndGetPhraseSlop()
    {
        $value = 2;
        $this->eDisMax->setPhraseSlop($value);

        $this->assertSame(
            $value,
            $this->eDisMax->getPhraseSlop()
        );
    }

    public function testSetAndGetPhraseBigramFields()
    {
        $value = 'description^1.3 date^4.3 field_text2^1.3';
        $this->eDisMax->setPhraseBigramFields($value);

        $this->assertSame(
            $value,
            $this->eDisMax->getPhraseBigramFields()
        );
    }

    public function testSetAndGetPhraseBigramSlop()
    {
        $value = 3;
        $this->eDisMax->setPhraseBigramSlop($value);

        $this->assertSame(
            $value,
            $this->eDisMax->getPhraseBigramSlop()
        );
    }

    public function testSetAndGetPhraseTrigramFields()
    {
        $value = 'datetime^4 field1^5 myotherfield^9';
        $this->eDisMax->setPhraseTrigramFields($value);

        $this->assertSame(
            $value,
            $this->eDisMax->getPhraseTrigramFields()
        );
    }

    public function testSetAndGetPhraseTrigramSlop()
    {
        $value = 5;
        $this->eDisMax->setPhraseTrigramSlop($value);

        $this->assertSame(
            $value,
            $this->eDisMax->getPhraseTrigramSlop()
        );
    }

    public function testSetAndGetQueryPhraseSlop()
    {
        $value = 3;
        $this->eDisMax->setQueryPhraseSlop($value);

        $this->assertSame(
            $value,
            $this->eDisMax->getQueryPhraseSlop()
        );
    }

    public function testSetAndGetTie()
    {
        $value = 2.1;
        $this->eDisMax->setTie($value);

        $this->assertSame(
            $value,
            $this->eDisMax->getTie()
        );
    }

    public function testSetAndGetBoostQuery()
    {
        $value = 'cat:1^3';
        $this->eDisMax->setBoostQuery($value);

        $this->assertSame(
            $value,
            $this->eDisMax->getBoostQuery()
        );
    }

    public function testSetAndGetBoostFunctions()
    {
        $value = 'funcA(arg1,arg2)^1.2 funcB(arg3,arg4)^2.2';
        $this->eDisMax->setBoostFunctions($value);

        $this->assertSame(
            $value,
            $this->eDisMax->getBoostFunctions()
        );
    }

    public function testSetAndGetBoostFunctionsMult()
    {
        $value = 'funcC(arg5,arg6)^4.3 funcD(arg7,arg8)^3.4';
        $this->eDisMax->setBoostFunctionsMult($value);

        $this->assertSame(
            $value,
            $this->eDisMax->getBoostFunctionsMult()
        );
    }

    public function testSetAndGetUserFields()
    {
        $value = 'date *_ul';
        $this->eDisMax->setUserFields($value);

        $this->assertSame(
            $value,
            $this->eDisMax->getUserFields()
        );
    }
}

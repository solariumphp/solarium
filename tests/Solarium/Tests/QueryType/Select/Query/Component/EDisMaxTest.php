<?php
/**
 * Copyright 2012 Marc Morera. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this listof conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * The views and conclusions contained in the software and documentation are
 * those of the authors and should not be interpreted as representing official
 * policies, either expressed or implied, of the copyright holder.
 */

namespace Solarium\Tests\QueryType\Select\Query\Component;

use Solarium\QueryType\Select\Query\Component\EdisMax;
use Solarium\QueryType\Select\Query\Query;

class EDisMaxTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EdisMax
     */
    protected $eDisMax;

    public function setUp()
    {
        $this->eDisMax = new EdisMax;
    }

    public function testConfigMode()
    {
        $options = array(
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
        );

        $this->eDisMax->setOptions($options);

        $this->assertEquals($options['queryparser'], $this->eDisMax->getQueryParser());
        $this->assertEquals($options['queryalternative'], $this->eDisMax->getQueryAlternative());
        $this->assertEquals($options['queryfields'], $this->eDisMax->getQueryFields());
        $this->assertEquals($options['minimummatch'], $this->eDisMax->getMinimumMatch());
        $this->assertEquals($options['phrasefields'], $this->eDisMax->getPhraseFields());
        $this->assertEquals($options['phraseslop'], $this->eDisMax->getPhraseSlop());
        $this->assertEquals($options['phrasebigramfields'], $this->eDisMax->getPhraseBigramFields());
        $this->assertEquals($options['phrasebigramslop'], $this->eDisMax->getPhraseBigramSlop());
        $this->assertEquals($options['phrasetrigramfields'], $this->eDisMax->getPhraseTrigramFields());
        $this->assertEquals($options['phrasetrigramslop'], $this->eDisMax->getPhraseTrigramSlop());
        $this->assertEquals($options['queryphraseslop'], $this->eDisMax->getQueryPhraseSlop());
        $this->assertEquals($options['tie'], $this->eDisMax->getTie());
        $this->assertEquals($options['boostquery'], $this->eDisMax->getBoostQuery());
        $this->assertEquals($options['boostfunctionsmult'], $this->eDisMax->getBoostFunctionsMult());
        $this->assertEquals($options['userfields'], $this->eDisMax->getUserFields());
    }

    public function testGetType()
    {
        $this->assertEquals(
            Query::COMPONENT_EDISMAX,
            $this->eDisMax->getType()
        );
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Select\RequestBuilder\Component\EdisMax',
            $this->eDisMax->getRequestBuilder()
        );
    }

    public function testSetAndGetQueryParser()
    {
        $value = 'dummyparser';
        $this->eDisMax->setQueryParser($value);

        $this->assertEquals(
            $value,
            $this->eDisMax->getQueryParser()
        );
    }

    public function testSetAndGetQueryAlternative()
    {
        $value = '*:*';
        $this->eDisMax->setQueryAlternative($value);

        $this->assertEquals(
            $value,
            $this->eDisMax->getQueryAlternative()
        );
    }

    public function testSetAndGetQueryFields()
    {
        $value = 'title^2.0 description';
        $this->eDisMax->setQueryFields($value);

        $this->assertEquals(
            $value,
            $this->eDisMax->getQueryFields()
        );
    }

    public function testSetAndGetMinimumMatch()
    {
        $value = '2.0';
        $this->eDisMax->setMinimumMatch($value);

        $this->assertEquals(
            $value,
            $this->eDisMax->getMinimumMatch()
        );
    }

    public function testSetAndGetPhraseFields()
    {
        $value = 'title^2.0 description^3.5';
        $this->eDisMax->setPhraseFields($value);

        $this->assertEquals(
            $value,
            $this->eDisMax->getPhraseFields()
        );
    }

    public function testSetAndGetPhraseSlop()
    {
        $value = '2';
        $this->eDisMax->setPhraseSlop($value);

        $this->assertEquals(
            $value,
            $this->eDisMax->getPhraseSlop()
        );
    }

    public function testSetAndGetPhraseBigramFields()
    {
        $value = 'description^1.3 date^4.3 field_text2^1.3';
        $this->eDisMax->setPhraseBigramFields($value);

        $this->assertEquals(
            $value,
            $this->eDisMax->getPhraseBigramFields()
        );
    }

    public function testSetAndGetPhraseBigramSlop()
    {
        $value = 3;
        $this->eDisMax->setPhraseBigramSlop($value);

        $this->assertEquals(
            $value,
            $this->eDisMax->getPhraseBigramSlop()
        );
    }

    public function testSetAndGetPhraseTrigramFields()
    {
        $value = 'datetime^4 field1^5 myotherfield^9';
        $this->eDisMax->setPhraseTrigramFields($value);

        $this->assertEquals(
            $value,
            $this->eDisMax->getPhraseTrigramFields()
        );
    }

    public function testSetAndGetPhraseTrigramSlop()
    {
        $value = 5;
        $this->eDisMax->setPhraseTrigramSlop($value);

        $this->assertEquals(
            $value,
            $this->eDisMax->getPhraseTrigramSlop()
        );
    }

    public function testSetAndGetQueryPhraseSlop()
    {
        $value = '3';
        $this->eDisMax->setQueryPhraseSlop($value);

        $this->assertEquals(
            $value,
            $this->eDisMax->getQueryPhraseSlop()
        );
    }

    public function testSetAndGetTie()
    {
        $value = 2.1;
        $this->eDisMax->setTie($value);

        $this->assertEquals(
            $value,
            $this->eDisMax->getTie()
        );
    }

    public function testSetAndGetBoostQuery()
    {
        $value = 'cat:1^3';
        $this->eDisMax->setBoostQuery($value);

        $this->assertEquals(
            $value,
            $this->eDisMax->getBoostQuery()
        );
    }

    public function testSetAndGetBoostFunctions()
    {
        $value = 'funcA(arg1,arg2)^1.2 funcB(arg3,arg4)^2.2';
        $this->eDisMax->setBoostFunctions($value);

        $this->assertEquals(
            $value,
            $this->eDisMax->getBoostFunctions()
        );
    }

    public function testSetAndGetBoostFunctionsMult()
    {
        $value = 'funcC(arg5,arg6)^4.3 funcD(arg7,arg8)^3.4';
        $this->eDisMax->setBoostFunctionsMult($value);

        $this->assertEquals(
            $value,
            $this->eDisMax->getBoostFunctionsMult()
        );
    }

    public function testSetAndGetUserFields()
    {
        $value = 'date *_ul';
        $this->eDisMax->setUserFields($value);

        $this->assertEquals(
            $value,
            $this->eDisMax->getUserFields()
        );
    }
}

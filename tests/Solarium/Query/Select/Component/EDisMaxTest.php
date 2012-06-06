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

class Solarium_Query_Select_Component_EDisMaxTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Query_Select_Component_EDisMax
     */
    protected $_eDisMax;

    public function setUp()
    {
        $this->_eDisMax = new Solarium_Query_Select_Component_EDisMax;
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

        $this->_eDisMax->setOptions($options);

        $this->assertEquals($options['queryparser'], $this->_eDisMax->getQueryParser());
        $this->assertEquals($options['queryalternative'], $this->_eDisMax->getQueryAlternative());
        $this->assertEquals($options['queryfields'], $this->_eDisMax->getQueryFields());
        $this->assertEquals($options['minimummatch'], $this->_eDisMax->getMinimumMatch());
        $this->assertEquals($options['phrasefields'], $this->_eDisMax->getPhraseFields());
        $this->assertEquals($options['phraseslop'], $this->_eDisMax->getPhraseSlop());
        $this->assertEquals($options['phrasebigramfields'], $this->_eDisMax->getPhraseBigramFields());
        $this->assertEquals($options['phrasebigramslop'], $this->_eDisMax->getPhraseBigramSlop());
        $this->assertEquals($options['phrasetrigramfields'], $this->_eDisMax->getPhraseTrigramFields());
        $this->assertEquals($options['phrasetrigramslop'], $this->_eDisMax->getPhraseTrigramSlop());
        $this->assertEquals($options['queryphraseslop'], $this->_eDisMax->getQueryPhraseSlop());
        $this->assertEquals($options['tie'], $this->_eDisMax->getTie());
        $this->assertEquals($options['boostquery'], $this->_eDisMax->getBoostQuery());
        $this->assertEquals($options['boostfunctionsmult'], $this->_eDisMax->getBoostFunctionsMult());
        $this->assertEquals($options['userfields'], $this->_eDisMax->getUserFields());
    }

    public function testGetType()
    {
        $this->assertEquals(
            Solarium_Query_Select::COMPONENT_EDISMAX,
            $this->_eDisMax->getType()
        );
    }

    public function testSetAndGetQueryParser()
    {
        $value = 'dummyparser';
        $this->_eDisMax->setQueryParser($value);

        $this->assertEquals(
            $value,
            $this->_eDisMax->getQueryParser()
        );
    }

    public function testSetAndGetQueryAlternative()
    {
        $value = '*:*';
        $this->_eDisMax->setQueryAlternative($value);

        $this->assertEquals(
            $value,
            $this->_eDisMax->getQueryAlternative()
        );
    }

    public function testSetAndGetQueryFields()
    {
        $value = 'title^2.0 description';
        $this->_eDisMax->setQueryFields($value);

        $this->assertEquals(
            $value,
            $this->_eDisMax->getQueryFields()
        );
    }

    public function testSetAndGetMinimumMatch()
    {
        $value = '2.0';
        $this->_eDisMax->setMinimumMatch($value);

        $this->assertEquals(
            $value,
            $this->_eDisMax->getMinimumMatch()
        );
    }

    public function testSetAndGetPhraseFields()
    {
        $value = 'title^2.0 description^3.5';
        $this->_eDisMax->setPhraseFields($value);

        $this->assertEquals(
            $value,
            $this->_eDisMax->getPhraseFields()
        );
    }

    public function testSetAndGetPhraseSlop()
    {
        $value = '2';
        $this->_eDisMax->setPhraseSlop($value);

        $this->assertEquals(
            $value,
            $this->_eDisMax->getPhraseSlop()
        );
    }

    public function testSetAndGetPhraseBigramFields()
    {
        $value = 'description^1.3 date^4.3 field_text2^1.3';
        $this->_eDisMax->setPhraseBigramFields($value);

        $this->assertEquals(
            $value,
            $this->_eDisMax->getPhraseBigramFields()
        );
    }

    public function testSetAndGetPhraseBigramSlop()
    {
        $value = 3;
        $this->_eDisMax->setPhraseBigramSlop($value);

        $this->assertEquals(
            $value,
            $this->_eDisMax->getPhraseBigramSlop()
        );
    }

    public function testSetAndGetPhraseTrigramFields()
    {
        $value = 'datetime^4 field1^5 myotherfield^9';
        $this->_eDisMax->setPhraseTrigramFields($value);

        $this->assertEquals(
            $value,
            $this->_eDisMax->getPhraseTrigramFields()
        );
    }

    public function testSetAndGetPhraseTrigramSlop()
    {
        $value = 5;
        $this->_eDisMax->setPhraseTrigramSlop($value);

        $this->assertEquals(
            $value,
            $this->_eDisMax->getPhraseTrigramSlop()
        );
    }

    public function testSetAndGetQueryPhraseSlop()
    {
        $value = '3';
        $this->_eDisMax->setQueryPhraseSlop($value);

        $this->assertEquals(
            $value,
            $this->_eDisMax->getQueryPhraseSlop()
        );
    }

    public function testSetAndGetTie()
    {
        $value = 2.1;
        $this->_eDisMax->setTie($value);

        $this->assertEquals(
            $value,
            $this->_eDisMax->getTie()
        );
    }

    public function testSetAndGetBoostQuery()
    {
        $value = 'cat:1^3';
        $this->_eDisMax->setBoostQuery($value);

        $this->assertEquals(
            $value,
            $this->_eDisMax->getBoostQuery()
        );
    }

    public function testSetAndGetBoostFunctions()
    {
        $value = 'funcA(arg1,arg2)^1.2 funcB(arg3,arg4)^2.2';
        $this->_eDisMax->setBoostFunctions($value);

        $this->assertEquals(
            $value,
            $this->_eDisMax->getBoostFunctions()
        );
    }

    public function testSetAndGetBoostFunctionsMult()
    {
        $value = 'funcC(arg5,arg6)^4.3 funcD(arg7,arg8)^3.4';
        $this->_eDisMax->setBoostFunctionsMult($value);

        $this->assertEquals(
            $value,
            $this->_eDisMax->getBoostFunctionsMult()
        );
    }

    public function testSetAndGetUserFields()
    {
        $value = 'date *_ul';
        $this->_eDisMax->setUserFields($value);

        $this->assertEquals(
            $value,
            $this->_eDisMax->getUserFields()
        );
    }
}

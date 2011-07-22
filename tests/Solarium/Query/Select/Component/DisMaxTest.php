<?php
/**
 * Copyright 2011 Bas de Nooijer. All rights reserved.
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

class Solarium_Query_Select_Component_DisMaxTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Query_Select_Component_DisMax
     */
    protected $_disMax;

    public function setUp()
    {
        $this->_disMax = new Solarium_Query_Select_Component_DisMax;
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
            'queryphraseslop' => 4,
            'tie' => 2.1,
            'boostquery' => 'cat:1^3',
            'boostfunctions' => 'funcA(arg1,arg2)^1.2 funcB(arg3,arg4)^2.2',
        );

        $this->_disMax->setOptions($options);

        $this->assertEquals($options['queryparser'], $this->_disMax->getQueryParser());
        $this->assertEquals($options['queryalternative'], $this->_disMax->getQueryAlternative());
        $this->assertEquals($options['queryfields'], $this->_disMax->getQueryFields());
        $this->assertEquals($options['minimummatch'], $this->_disMax->getMinimumMatch());
        $this->assertEquals($options['phrasefields'], $this->_disMax->getPhraseFields());
        $this->assertEquals($options['phraseslop'], $this->_disMax->getPhraseSlop());
        $this->assertEquals($options['queryphraseslop'], $this->_disMax->getQueryPhraseSlop());
        $this->assertEquals($options['tie'], $this->_disMax->getTie());
        $this->assertEquals($options['boostquery'], $this->_disMax->getBoostQuery());
        $this->assertEquals($options['boostfunctions'], $this->_disMax->getBoostFunctions());
    }

    public function testGetType()
    {
        $this->assertEquals(
            Solarium_Query_Select::COMPONENT_DISMAX,
            $this->_disMax->getType()
        );
    }

    public function testSetAndGetQueryParser()
    {
        $value = 'dummyparser';
        $this->_disMax->setQueryParser($value);

        $this->assertEquals(
            $value,
            $this->_disMax->getQueryParser()
        );
    }

    public function testSetAndGetQueryAlternative()
    {
        $value = '*:*';
        $this->_disMax->setQueryAlternative($value);

        $this->assertEquals(
            $value,
            $this->_disMax->getQueryAlternative()
        );
    }

    public function testSetAndGetQueryFields()
    {
        $value = 'title^2.0 description';
        $this->_disMax->setQueryFields($value);

        $this->assertEquals(
            $value,
            $this->_disMax->getQueryFields()
        );
    }

    public function testSetAndGetMinimumMatch()
    {
        $value = '2.0';
        $this->_disMax->setMinimumMatch($value);

        $this->assertEquals(
            $value,
            $this->_disMax->getMinimumMatch()
        );
    }

    public function testSetAndGetPhraseFields()
    {
        $value = 'title^2.0 description^3.5';
        $this->_disMax->setPhraseFields($value);

        $this->assertEquals(
            $value,
            $this->_disMax->getPhraseFields()
        );
    }

    public function testSetAndGetPhraseSlop()
    {
        $value = '2';
        $this->_disMax->setPhraseSlop($value);

        $this->assertEquals(
            $value,
            $this->_disMax->getPhraseSlop()
        );
    }

    public function testSetAndGetQueryPhraseSlop()
    {
        $value = '3';
        $this->_disMax->setQueryPhraseSlop($value);

        $this->assertEquals(
            $value,
            $this->_disMax->getQueryPhraseSlop()
        );
    }

    public function testSetAndGetTie()
    {
        $value = 2.1;
        $this->_disMax->setTie($value);

        $this->assertEquals(
            $value,
            $this->_disMax->getTie()
        );
    }

    public function testSetAndGetBoostQuery()
    {
        $value = 'cat:1^3';
        $this->_disMax->setBoostQuery($value);

        $this->assertEquals(
            $value,
            $this->_disMax->getBoostQuery()
        );
    }

    public function testSetAndGetBoostFunctions()
    {
        $value = 'funcA(arg1,arg2)^1.2 funcB(arg3,arg4)^2.2';
        $this->_disMax->setBoostFunctions($value);

        $this->assertEquals(
            $value,
            $this->_disMax->getBoostFunctions()
        );
    }
    
}

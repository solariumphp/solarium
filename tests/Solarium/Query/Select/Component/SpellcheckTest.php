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

class Solarium_Query_Select_Component_SpellcheckTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Query_Select_Component_Spellcheck
     */
    protected $_spellCheck;

    public function setUp()
    {
        $this->_spellCheck = new Solarium_Query_Select_Component_Spellcheck;
    }

    public function testGetType()
    {
        $this->assertEquals(Solarium_Query_Select::COMPONENT_SPELLCHECK, $this->_spellCheck->getType());
    }

    public function testSetAndGetQuery()
    {
        $value = 'testquery';
        $this->_spellCheck->setQuery($value);

        $this->assertEquals(
            $value,
            $this->_spellCheck->getQuery()
        );
    }

    public function testSetAndGetBuild()
    {
        $value = true;
        $this->_spellCheck->setBuild($value);

        $this->assertEquals(
            $value,
            $this->_spellCheck->getBuild()
        );
    }

    public function testSetAndGetReload()
    {
        $value = false;
        $this->_spellCheck->setReload($value);

        $this->assertEquals(
            $value,
            $this->_spellCheck->getReload()
        );
    }

    public function testSetAndGetDictionary()
    {
        $value = 'myDictionary';
        $this->_spellCheck->setDictionary($value);

        $this->assertEquals(
            $value,
            $this->_spellCheck->getDictionary()
        );
    }

    public function testSetAndGetCount()
    {
        $value = 11;
        $this->_spellCheck->setCount($value);

        $this->assertEquals(
            $value,
            $this->_spellCheck->getCount()
        );
    }

    public function testSetAndGetOnlyMorePopular()
    {
        $value = false;
        $this->_spellCheck->setOnlyMorePopular($value);

        $this->assertEquals(
            $value,
            $this->_spellCheck->getOnlyMorePopular()
        );
    }

    public function testSetAndGetExtendedResults()
    {
        $value = false;
        $this->_spellCheck->setExtendedResults($value);

        $this->assertEquals(
            $value,
            $this->_spellCheck->getExtendedResults()
        );
    }

    public function testSetAndGetCollate()
    {
        $value = false;
        $this->_spellCheck->setCollate($value);

        $this->assertEquals(
            $value,
            $this->_spellCheck->getCollate()
        );
    }

    public function testSetAndGetMaxCollations()
    {
        $value = 23;
        $this->_spellCheck->setMaxCollations($value);

        $this->assertEquals(
            $value,
            $this->_spellCheck->getMaxCollations()
        );
    }

    public function testSetAndGetMaxCollationTries()
    {
        $value = 10;
        $this->_spellCheck->setMaxCollationTries($value);

        $this->assertEquals(
            $value,
            $this->_spellCheck->getMaxCollationTries()
        );
    }

    public function testSetAndGetMaxCollationEvaluations()
    {
        $value = 10;
        $this->_spellCheck->setMaxCollationEvaluations($value);

        $this->assertEquals(
            $value,
            $this->_spellCheck->getMaxCollationEvaluations()
        );
    }

    public function testSetAndGetCollateExtendedResults()
    {
        $value = true;
        $this->_spellCheck->setCollateExtendedResults($value);

        $this->assertEquals(
            $value,
            $this->_spellCheck->getCollateExtendedResults()
        );
    }

    public function testSetAndGetAccuracy()
    {
        $value = .1;
        $this->_spellCheck->setAccuracy($value);

        $this->assertEquals(
            $value,
            $this->_spellCheck->getAccuracy()
        );
    }
}

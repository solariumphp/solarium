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

class Solarium_Query_MoreLikeThisTest extends Solarium_Query_SelectTest
{

    protected $_query;

    public function setUp()
    {
        $this->_query = new Solarium_Query_MoreLikeThis;
    }

    public function testGetType()
    {
        $this->assertEquals(Solarium_Client::QUERYTYPE_MORELIKETHIS, $this->_query->getType());
    }

    public function testSetAndGetMatchInclude()
    {
        $value = true;
        $this->_query->setMatchInclude($value);

        $this->assertEquals(
            $value,
            $this->_query->getMatchInclude()
        );
    }

    public function testSetAndGetMltFields()
    {
        $value = 'name,description';
        $this->_query->setMltFields($value);

        $this->assertEquals(
            $value,
            $this->_query->getMltFields()
        );
    }

    public function testSetAndGetInterestingTerms()
    {
        $value = 'test';
        $this->_query->setInterestingTerms($value);

        $this->assertEquals(
            $value,
            $this->_query->getInterestingTerms()
        );
    }

    public function testSetAndGetQueryStream()
    {
        $value = true;
        $this->_query->setQueryStream($value);

        $this->assertEquals(
            $value,
            $this->_query->getQueryStream()
        );
    }

    public function testSetAndGetMinimumTermFrequency()
    {
        $value = 2;
        $this->_query->setMinimumTermFrequency($value);

        $this->assertEquals(
            $value,
            $this->_query->getMinimumTermFrequency()
        );
    }

    public function testMinimumDocumentFrequency()
    {
        $value = 4;
        $this->_query->setMinimumDocumentFrequency($value);

        $this->assertEquals(
            $value,
            $this->_query->getMinimumDocumentFrequency()
        );
    }

    public function testSetAndGetMinimumWordLength()
    {
        $value = 3;
        $this->_query->setMinimumWordLength($value);

        $this->assertEquals(
            $value,
            $this->_query->getMinimumWordLength()
        );
    }

    public function testSetAndGetMaximumWordLength()
    {
        $value = 15;
        $this->_query->setMaximumWordLength($value);

        $this->assertEquals(
            $value,
            $this->_query->getMaximumWordLength()
        );
    }

    public function testSetAndGetMaximumQueryTerms()
    {
        $value = 5;
        $this->_query->setMaximumQueryTerms($value);

        $this->assertEquals(
            $value,
            $this->_query->getMaximumQueryTerms()
        );
    }

    public function testSetAndGetMaximumNumberOfTokens()
    {
        $value = 5;
        $this->_query->setMaximumNumberOfTokens($value);

        $this->assertEquals(
            $value,
            $this->_query->getMaximumNumberOfTokens()
        );
    }

    public function testSetAndGetBoost()
    {
        $value = true;
        $this->_query->setBoost($value);

        $this->assertEquals(
            $value,
            $this->_query->getBoost()
        );
    }

    public function testSetAndGetQueryFields()
    {
        $value = 'content,name';
        $this->_query->setQueryFields($value);

        $this->assertEquals(
            $value,
            $this->_query->getQueryFields()
        );
    }


}
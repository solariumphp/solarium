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

namespace Solarium\Tests\Query\MoreLikeThis;
use Solarium\Tests\Query\Select\Query\QueryTest as SelectQueryTest;
use Solarium\Query\MoreLikeThis\Query;
use Solarium\Core\Client\Client;

class QueryTest extends SelectQueryTest
{

    protected $query;

    public function setUp()
    {
        $this->query = new Query;
    }

    public function testGetType()
    {
        $this->assertEquals(Client::QUERY_MORELIKETHIS, $this->query->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf('Solarium\Query\MoreLikeThis\ResponseParser', $this->query->getResponseParser());
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf('Solarium\Query\MoreLikeThis\RequestBuilder', $this->query->getRequestBuilder());
    }

    public function testSetAndGetMatchInclude()
    {
        $value = true;
        $this->query->setMatchInclude($value);

        $this->assertEquals(
            $value,
            $this->query->getMatchInclude()
        );
    }

    public function testSetAndGetMltFields()
    {
        $value = 'name,description';
        $this->query->setMltFields($value);

        $this->assertEquals(
            $value,
            $this->query->getMltFields()
        );
    }

    public function testSetAndGetInterestingTerms()
    {
        $value = 'test';
        $this->query->setInterestingTerms($value);

        $this->assertEquals(
            $value,
            $this->query->getInterestingTerms()
        );
    }

    public function testSetAndGetQueryStream()
    {
        $value = true;
        $this->query->setQueryStream($value);

        $this->assertEquals(
            $value,
            $this->query->getQueryStream()
        );
    }

    public function testSetAndGetMinimumTermFrequency()
    {
        $value = 2;
        $this->query->setMinimumTermFrequency($value);

        $this->assertEquals(
            $value,
            $this->query->getMinimumTermFrequency()
        );
    }

    public function testMinimumDocumentFrequency()
    {
        $value = 4;
        $this->query->setMinimumDocumentFrequency($value);

        $this->assertEquals(
            $value,
            $this->query->getMinimumDocumentFrequency()
        );
    }

    public function testSetAndGetMinimumWordLength()
    {
        $value = 3;
        $this->query->setMinimumWordLength($value);

        $this->assertEquals(
            $value,
            $this->query->getMinimumWordLength()
        );
    }

    public function testSetAndGetMaximumWordLength()
    {
        $value = 15;
        $this->query->setMaximumWordLength($value);

        $this->assertEquals(
            $value,
            $this->query->getMaximumWordLength()
        );
    }

    public function testSetAndGetMaximumQueryTerms()
    {
        $value = 5;
        $this->query->setMaximumQueryTerms($value);

        $this->assertEquals(
            $value,
            $this->query->getMaximumQueryTerms()
        );
    }

    public function testSetAndGetMaximumNumberOfTokens()
    {
        $value = 5;
        $this->query->setMaximumNumberOfTokens($value);

        $this->assertEquals(
            $value,
            $this->query->getMaximumNumberOfTokens()
        );
    }

    public function testSetAndGetBoost()
    {
        $value = true;
        $this->query->setBoost($value);

        $this->assertEquals(
            $value,
            $this->query->getBoost()
        );
    }

    public function testSetAndGetQueryFields()
    {
        $value = 'content,name';
        $this->query->setQueryFields($value);

        $this->assertEquals(
            $value,
            $this->query->getQueryFields()
        );
    }


}
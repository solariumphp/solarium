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

namespace Solarium\Tests\QueryType\Select\Query\Component;

use Solarium\QueryType\Select\Query\Component\MoreLikeThis;
use Solarium\QueryType\Select\Query\Query;

class MoreLikeThisTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MoreLikeThis
     */
    protected $mlt;

    public function setUp()
    {
        $this->mlt = new MoreLikeThis;
    }

    public function testConfigMode()
    {
        $options = array(
            'fields' => 'fieldA,fieldB',
            'minimumtermfrequency' => 10,
            'minimumdocumentfrequency' => 2,
            'minimumwordlength' => 3,
            'maximumwordlength' => 10,
            'maximumqueryterms' => 4,
            'maximumnumberoftokens' => 20,
            'boost' => 1.5,
            'queryfields' => 'fieldC,fieldD',
            'count' => 5,
        );

        $this->mlt->setOptions($options);

        $this->assertEquals($options['fields'], $this->mlt->getFields());
        $this->assertEquals($options['minimumtermfrequency'], $this->mlt->getMinimumTermFrequency());
        $this->assertEquals($options['minimumdocumentfrequency'], $this->mlt->getMinimumDocumentFrequency());
        $this->assertEquals($options['minimumwordlength'], $this->mlt->getMinimumWordLength());
        $this->assertEquals($options['maximumwordlength'], $this->mlt->getMaximumWordLength());
        $this->assertEquals($options['maximumqueryterms'], $this->mlt->getMaximumQueryTerms());
        $this->assertEquals($options['boost'], $this->mlt->getBoost());
        $this->assertEquals($options['queryfields'], $this->mlt->getQueryFields());
        $this->assertEquals($options['count'], $this->mlt->getCount());
    }

    public function testGetType()
    {
        $this->assertEquals(Query::COMPONENT_MORELIKETHIS, $this->mlt->getType());
    }

    public function testGetResponseParser()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Select\ResponseParser\Component\MoreLikeThis',
            $this->mlt->getResponseParser()
        );
    }

    public function testGetRequestBuilder()
    {
        $this->assertInstanceOf(
            'Solarium\QueryType\Select\RequestBuilder\Component\MoreLikeThis',
            $this->mlt->getRequestBuilder()
        );
    }

    public function testSetAndGetFields()
    {
        $value = 'name,description';
        $this->mlt->setFields($value);

        $this->assertEquals(
            array('name', 'description'),
            $this->mlt->getFields()
        );
    }

    public function testSetAndGetFieldsWithArray()
    {
        $value = array('name', 'description');
        $this->mlt->setFields($value);

        $this->assertEquals(
            $value,
            $this->mlt->getFields()
        );
    }

    public function testSetAndGetMinimumTermFrequency()
    {
        $value = 2;
        $this->mlt->setMinimumTermFrequency($value);

        $this->assertEquals(
            $value,
            $this->mlt->getMinimumTermFrequency()
        );
    }

    public function testMinimumDocumentFrequency()
    {
        $value = 4;
        $this->mlt->setMinimumDocumentFrequency($value);

        $this->assertEquals(
            $value,
            $this->mlt->getMinimumDocumentFrequency()
        );
    }

    public function testSetAndGetMinimumWordLength()
    {
        $value = 3;
        $this->mlt->setMinimumWordLength($value);

        $this->assertEquals(
            $value,
            $this->mlt->getMinimumWordLength()
        );
    }

    public function testSetAndGetMaximumWordLength()
    {
        $value = 15;
        $this->mlt->setMaximumWordLength($value);

        $this->assertEquals(
            $value,
            $this->mlt->getMaximumWordLength()
        );
    }

    public function testSetAndGetMaximumQueryTerms()
    {
        $value = 5;
        $this->mlt->setMaximumQueryTerms($value);

        $this->assertEquals(
            $value,
            $this->mlt->getMaximumQueryTerms()
        );
    }

    public function testSetAndGetMaximumNumberOfTokens()
    {
        $value = 5;
        $this->mlt->setMaximumNumberOfTokens($value);

        $this->assertEquals(
            $value,
            $this->mlt->getMaximumNumberOfTokens()
        );
    }

    public function testSetAndGetBoost()
    {
        $value = true;
        $this->mlt->setBoost($value);

        $this->assertEquals(
            $value,
            $this->mlt->getBoost()
        );
    }

    public function testSetAndGetQueryFields()
    {
        $value = 'content,name';
        $this->mlt->setQueryFields($value);

        $this->assertEquals(
            array('content', 'name'),
            $this->mlt->getQueryFields()
        );
    }

    public function testSetAndGetQueryFieldsWithArray()
    {
        $value = array('content', 'name');
        $this->mlt->setQueryFields($value);

        $this->assertEquals(
            $value,
            $this->mlt->getQueryFields()
        );
    }

    public function testSetAndGetCount()
    {
        $value = 8;
        $this->mlt->setCount($value);

        $this->assertEquals(
            $value,
            $this->mlt->getCount()
        );
    }
}

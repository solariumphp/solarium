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

class Solarium_Query_Select_Component_MoreLikeThisTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Solarium_Query_Select_Component_MoreLikeThis
     */
    protected $_mlt;

    public function setUp()
    {
        $this->_mlt = new Solarium_Query_Select_Component_MoreLikeThis;
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

        $this->_mlt->setOptions($options);

        $this->assertEquals($options['fields'], $this->_mlt->getFields());
        $this->assertEquals($options['minimumtermfrequency'], $this->_mlt->getMinimumTermFrequency());
        $this->assertEquals($options['minimumdocumentfrequency'], $this->_mlt->getMinimumDocumentFrequency());
        $this->assertEquals($options['minimumwordlength'], $this->_mlt->getMinimumWordLength());
        $this->assertEquals($options['maximumwordlength'], $this->_mlt->getMaximumWordLength());
        $this->assertEquals($options['maximumqueryterms'], $this->_mlt->getMaximumQueryTerms());
        $this->assertEquals($options['boost'], $this->_mlt->getBoost());
        $this->assertEquals($options['queryfields'], $this->_mlt->getQueryFields());
        $this->assertEquals($options['count'], $this->_mlt->getCount());
    }

    public function testGetType()
    {
        $this->assertEquals(Solarium_Query_Select::COMPONENT_MORELIKETHIS, $this->_mlt->getType());
    }

    public function testSetAndGetFields()
    {
        $value = 'name,description';
        $this->_mlt->setFields($value);

        $this->assertEquals(
            $value,
            $this->_mlt->getFields()
        );
    }

    public function testSetAndGetMinimumTermFrequency()
    {
        $value = 2;
        $this->_mlt->setMinimumTermFrequency($value);

        $this->assertEquals(
            $value,
            $this->_mlt->getMinimumTermFrequency()
        );
    }

    public function testMinimumDocumentFrequency()
    {
        $value = 4;
        $this->_mlt->setMinimumDocumentFrequency($value);

        $this->assertEquals(
            $value,
            $this->_mlt->getMinimumDocumentFrequency()
        );
    }

    public function testSetAndGetMinimumWordLength()
    {
        $value = 3;
        $this->_mlt->setMinimumWordLength($value);

        $this->assertEquals(
            $value,
            $this->_mlt->getMinimumWordLength()
        );
    }

    public function testSetAndGetMaximumWordLength()
    {
        $value = 15;
        $this->_mlt->setMaximumWordLength($value);

        $this->assertEquals(
            $value,
            $this->_mlt->getMaximumWordLength()
        );
    }

    public function testSetAndGetMaximumQueryTerms()
    {
        $value = 5;
        $this->_mlt->setMaximumQueryTerms($value);

        $this->assertEquals(
            $value,
            $this->_mlt->getMaximumQueryTerms()
        );
    }

    public function testSetAndGetMaximumNumberOfTokens()
    {
        $value = 5;
        $this->_mlt->setMaximumNumberOfTokens($value);

        $this->assertEquals(
            $value,
            $this->_mlt->getMaximumNumberOfTokens()
        );
    }

    public function testSetAndGetBoost()
    {
        $value = true;
        $this->_mlt->setBoost($value);

        $this->assertEquals(
            $value,
            $this->_mlt->getBoost()
        );
    }

    public function testSetAndGetQueryFields()
    {
        $value = 'content,name';
        $this->_mlt->setQueryFields($value);

        $this->assertEquals(
            $value,
            $this->_mlt->getQueryFields()
        );
    }

    public function testSetAndGetCount()
    {
        $value = 8;
        $this->_mlt->setCount($value);

        $this->assertEquals(
            $value,
            $this->_mlt->getCount()
        );
    }
    
}
